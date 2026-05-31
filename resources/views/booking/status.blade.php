@php
$extraValue = 0;
$sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
$datetime = $sitesetup ? json_decode($sitesetup->value) : null;
$timezone = $datetime->time_zone ?? getTimeZone();

// Build activity map
$activityStatusTimes = [];
$assignedTime = null;

// Initialize pending time with booking created_at
$activityStatusTimes['pending'] = $bookingdata->created_at;

if ($bookingdata->bookingActivity) {
    foreach ($bookingdata->bookingActivity as $activity) {
        $actData = json_decode($activity->activity_data, true);
        
        // Check for update_booking_status
        if ($actData && isset($actData['status'])) {
            $activityStatusTimes[$actData['status']] = $activity->datetime;
        }
        
        $type = $activity->activity_type;
        if (strtolower($type) == 'add booking' || $type == __('messages.add_booking')) {
            $activityStatusTimes['pending'] = $activity->datetime;
        }
        
        if (str_contains(strtolower($type), 'assigned') || str_contains(strtolower($type), 'transfer') || (is_array($actData) && isset($actData['handyman_id']))) {
            $assignedTime = $activity->datetime;
        }
    }
}

// Enforce order / hierarchy weights of standard sequence
$statusWeights = [
    'pending' => 1,
    'accept' => 2,
    'assigned' => 3,
    'on_going' => 4,
    'arrived' => 5,
    'in_progress' => 6,
    'hold' => 7,
    'pending_approval' => 8,
    'completed' => 9,
];

// Determine the current effective status
$currentStatus = $bookingdata->status;
$effectiveStatus = $currentStatus;

// If handyman is assigned, we insert "assigned" as a state
$hasHandyman = $bookingdata->handymanAdded->count() > 0;
if ($currentStatus == 'accept' && $hasHandyman) {
    $effectiveStatus = 'assigned';
}

$currentWeight = $statusWeights[$effectiveStatus] ?? 1;
$isTerminal = in_array($currentStatus, ['cancelled', 'rejected', 'failed']);

// Define the steps we want to show
$steps = [
    [
        'status' => 'pending',
        'label' => __('messages.new_booking') ?? 'New Booking',
        'description' => 'New Booking Added by ' . (optional($bookingdata->customer)->display_name ?? 'Customer'),
        'color' => '#dc3545', // Red
    ],
    [
        'status' => 'accept',
        'label' => __('messages.accept_booking') ?? 'Accept Booking',
        'description' => 'Booking Accepted by ' . (optional($bookingdata->provider)->display_name ?? 'Provider'),
        'color' => '#ffc107', // Yellow
    ]
];

// Show "Assigned" step if handyman is assigned, or if we have an assigned activity, or if the current status weight is past accept
if ($hasHandyman || $assignedTime !== null || ($currentWeight >= 3 && isset($statusWeights[$effectiveStatus]))) {
    $handymanNames = [];
    foreach ($bookingdata->handymanAdded as $h) {
        $handymanNames[] = optional($h->handyman)->display_name;
    }
    $handymanNameStr = !empty($handymanNames) ? implode(', ', $handymanNames) : 'Handyman';
    $steps[] = [
        'status' => 'assigned',
        'label' => __('messages.assigned_booking') ?? 'Assigned Booking',
        'description' => 'Service Assigned to ' . $handymanNameStr,
        'color' => '#fd7e14', // Orange
    ];
}

// Show "Ongoing" step
$steps[] = [
    'status' => 'on_going',
    'label' => __('messages.on_going') ?? 'Ongoing',
    'description' => 'Service is ongoing',
    'color' => '#007bff', // Blue
];

// Show "Arrived" step
$steps[] = [
    'status' => 'arrived',
    'label' => 'Arrived',
    'description' => 'Provider/Handyman Arrived',
    'color' => '#17a2b8', // Teal/Info
];

// Show "In Progress" step
$steps[] = [
    'status' => 'in_progress',
    'label' => 'In Progress',
    'description' => 'Service is currently in progress',
    'color' => '#6f42c1', // Purple
];

// Show "Hold" step only if currently on hold or was on hold in the past
if ($currentStatus == 'hold' || isset($activityStatusTimes['hold'])) {
    $steps[] = [
        'status' => 'hold',
        'label' => __('messages.hold') ?? 'Hold',
        'description' => 'Service Put on Hold - Reason: ' . ($bookingdata->reason ?? 'None'),
        'color' => '#6c757d', // Grey
    ];
}

// Show "Pending Approval" step only if currently pending approval or was pending approval in the past
if ($currentStatus == 'pending_approval' || isset($activityStatusTimes['pending_approval'])) {
    $steps[] = [
        'status' => 'pending_approval',
        'label' => 'Pending Approval',
        'description' => 'Pending Approval from Customer',
        'color' => '#6f42c1', // Purple
    ];
}

// If terminal (cancelled, rejected, failed), insert the terminal step here
if ($isTerminal) {
    $terminalLabel = ucwords($currentStatus);
    $terminalDesc = 'Booking has been ' . $currentStatus;
    if ($currentStatus == 'cancelled') {
        $terminalLabel = trim(str_replace([':name', ':'], '', __('messages.cancelled') ?? 'Cancelled'));
        $terminalDesc = 'Booking has been cancelled';
    } elseif ($currentStatus == 'rejected') {
        $terminalLabel = __('messages.rejected') ?? 'Rejected';
        $terminalDesc = 'Booking has been rejected';
    } elseif ($currentStatus == 'failed') {
        $terminalLabel = __('messages.failed') ?? 'Failed';
        $terminalDesc = 'Booking has failed';
    }
    
    $steps[] = [
        'status' => $currentStatus,
        'label' => $terminalLabel,
        'description' => $bookingdata->reason ? $bookingdata->reason : $terminalDesc,
        'color' => '#dc3545', // Red
        'is_terminal' => true,
    ];
} else {
    // Show Completed step
    $steps[] = [
        'status' => 'completed',
        'label' => __('messages.completed') ?? 'Completed',
        'description' => 'Service Completed - Final Amount: ' . getPriceFormat($bookingdata->total_amount),
        'color' => '#28a745', // Green
    ];
}
@endphp
<div class="row">
    <!-- Timeline Column -->
    <div class="col-md-6">
        <div class=" pb-3">
            <h2 class="modal-title" id="breakdownModalLabel">{{__('messages.booking_status')}}</h2>

            <div class="vertical-timeline mb-4">
                @foreach($steps as $step)
                    @php
                    $stepStatus = $step['status'];
                    $stepWeight = $statusWeights[$stepStatus] ?? 99;
                    
                    $isCompleted = false;
                    $isCurrentActive = false;

                    if ($isTerminal) {
                        if ($stepStatus === $currentStatus) {
                            $isCurrentActive = true;
                        } else {
                            if ($stepStatus === 'pending') {
                                $isCompleted = true;
                            } elseif ($stepStatus === 'assigned') {
                                $isCompleted = ($assignedTime !== null || $hasHandyman);
                            } else {
                                $isCompleted = isset($activityStatusTimes[$stepStatus]);
                            }
                        }
                    } else {
                        if ($stepStatus === $effectiveStatus) {
                            $isCurrentActive = true;
                        } elseif ($stepWeight < $currentWeight) {
                            $isCompleted = true;
                        }
                    }

                    $stepTime = null;
                    if ($stepStatus == 'assigned') {
                        $stepTime = $assignedTime;
                    } else {
                        $stepTime = $activityStatusTimes[$stepStatus] ?? null;
                    }

                    if ($stepTime === null && ($isCompleted || $isCurrentActive)) {
                        if ($stepStatus == 'pending') {
                            $stepTime = $bookingdata->created_at;
                        } elseif ($stepStatus == $currentStatus) {
                            $stepTime = $bookingdata->updated_at;
                        }
                    }
                    if ($stepTime) {
                        if ($stepTime instanceof \Carbon\Carbon) {
                            $carbonTime = \Carbon\Carbon::parse($stepTime->toDateTimeString(), $timezone);
                        } else {
                            $carbonTime = \Carbon\Carbon::parse($stepTime, $timezone);
                        }
                    }
                    @endphp
                    
                    <div class="timeline-item {{ $stepStatus }} {{ $isCurrentActive ? 'active current-active' : '' }} {{ $isCompleted ? 'completed-step' : '' }}" data-status="{{ $stepStatus }}">
                        <div class="timeline-date">
                            @if($stepTime)
                                {{ $carbonTime->format($datetime->time_format) }}
                                <div class="date-details">
                                    {{ $carbonTime->format($datetime->date_format) }}
                                </div>
                            @else
                                <span class="text-muted">--:--</span>
                                <div class="date-details">
                                    <span class="text-muted">----/--/--</span>
                                </div>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="point {{ $isCurrentActive ? 'pulse-active' : '' }}" 
                                 style="background-color: {{ ($isCompleted || $isCurrentActive) ? $step['color'] : '#e0e0e0' }}; 
                                        --pulse-box-shadow: {{ $step['color'] }}66; 
                                        --pulse-box-shadow-fade: {{ $step['color'] }}00;"></div>
                            <div class="timeline-info">
                                <p class="fs-4" style="color: {{ ($isCompleted || $isCurrentActive) ? '#1C1F34' : '#888' }}">
                                    <strong>{{ $step['label'] }}</strong>
                                </p>
                                <div class="timeline-details">
                                    <p class="mt-2" style="color: {{ ($isCompleted || $isCurrentActive) ? '#555' : '#aaa' }}">
                                        {{ $step['description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-connector" style="border-left-color: {{ $isCompleted ? $step['color'] : '#e0e0e0' }};"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Provider and Handyman Cards Column -->
    <div class="col-md-6">
        <!-- Booking Summary Card -->
        <div class="card mb-4">
            <div class="card-body bg-body">
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between mb-2 p-2">
                        <span class="text-muted">{{ __('messages.book_placed') }}:</span>
                        <span class="fw-medium">
                            {{ \Carbon\Carbon::parse($bookingdata->created_at->toDateTimeString(), $timezone)->format($datetime->date_format) }} /
                            {{ \Carbon\Carbon::parse($bookingdata->created_at->toDateTimeString(), $timezone)->format($datetime->time_format) }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between mb-2 p-2">
                        <span class="text-muted">{{__('messages.booking_status')}}:</span>
                        <strong><span class="text-primary">{{ isset($bookingdata->status) ? ucwords(str_replace('_', ' ', $bookingdata->status)) : __('messages.pending') }}</span></strong>
                    </li>                   
                    <li class="d-flex justify-content-between mb-2 p-2">
                        <span class="text-muted">{{ __('messages.payment_status') }}:</span>
                        @if(isset($payment) && $payment->payment_status)
                            @php
                                $statusClass = match($payment->payment_status) {
                                    'paid', 'advanced_paid' => 'text-success',
                                    'Advanced Refund' => 'text-warning',
                                    default => 'text-danger',
                                };
                            @endphp
                            <strong>
                                <span class="{{ $statusClass }}">
                                    {{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}
                                </span>
                            </strong>
                        @else
                            <strong>
                                <span class="text-danger">{{ __('messages.pending') }}</span>
                            </strong>
                        @endif
                    </li>      
                    <li class="d-flex justify-content-between p-2">
                        <span class="text-muted">{{__('messages.booking_amount')}}:</span>
                        <span class="fw-medium">{{ getPriceFormat($bookingdata->total_amount) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-12">
            <!-- Provider Information -->
                <div class="card mb-4">
                    <div class="card-body bg-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <img src="{{ getSingleMedia($bookingdata->provider,'profile_image', null) }}" 
                                        alt="Provider Profile" 
                                        class="rounded-circle"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                    @if(optional($bookingdata->provider)->profile_image)
                                        <img src="{{ asset('images/default-user.png') }}" 
                                            alt="Default Profile" 
                                            class="rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-primary">{{__('messages.provider')}}</p>
                                    <h5 class="mb-2">{{optional($bookingdata->provider)->display_name ?? '-'}}</h5>
                                </div>
                            </div>
                        <ul class="list-unstyled mt-3">
                            <li class="d-flex align-items-center mb-2">
                                <i class="ri-phone-line me-2"></i>
                                <a href="tel:{{optional($bookingdata->provider)->contact_number}}" class="text-body">
                                    {{ optional($bookingdata->provider)->contact_number ?? '-' }}
                                </a>
                            </li>
                            <!-- <li class="d-flex align-items-center mb-2">
                                <i class="ri-mail-line me-2"></i>
                                <a href="mailto:{{optional($bookingdata->provider)->email}}" class="text-body">
                                    {{ optional($bookingdata->provider)->email ?? '-' }}
                                </a>
                            </li> -->
                            <li class="d-flex align-items-center">
                                <i class="ri-map-pin-line me-2"></i>
                                <span class="text-wrap">{{ optional($bookingdata->provider)->address ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

            <!-- Handyman Information -->
                        @if(count($bookingdata->handymanAdded) > 0)
                            <div class="card mb-4">
                                <div class="card-body bg-body">
        
                                    @foreach($bookingdata->handymanAdded as $booking)
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="flex-shrink-0">
                                            
                                                <img src="{{ getSingleMedia($booking->handyman,'profile_image', null) }}" 
                                                    alt="Handyman Profile" 
                                                    class="rounded-circle"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                    @if(optional($booking->handyman)->profile_image)
                                                <img src="{{ asset('images/default-user.png') }}" 
                                                    alt="Default Profile" 
                                                    class="rounded-circle"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 text-primary">{{__('messages.handyman')}}</p>
                                            <h5 class="mb-2 ">{{optional($booking->handyman)->display_name ?? '-'}}</h5>
                                        </div>
                                    </div>
                                            <ul class="list-unstyled mt-3">
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="ri-phone-line me-2"></i>
                                                    <a href="tel:{{optional($booking->handyman)->contact_number}}" class="text-body">
                                                        {{ optional($booking->handyman)->contact_number ?? '-' }}
                                                    </a>
                                                </li>
                                                <li class="d-flex align-items-center">
                                                    <i class="ri-map-pin-line me-2"></i>
                                                    <span class="text-wrap">{{ optional($booking->handyman)->address ?? '-' }}</span>
                                                </li>
                                            </ul>
                                    @endforeach
                                </div>
                            </div>
                        @endif
        </div>
                    
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingId = '{{ $bookingdata->id }}';
    let currentStatus = '{{ $bookingdata->status }}';

    function updateTimelineStatus() {
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach(item => {
            if (item.classList.contains(currentStatus)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    function updateBookingStatus(status) {
        const bookingStatusElement = document.querySelector('[data-booking-status]');
        if (bookingStatusElement) {
            bookingStatusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
        }
    }

    // Polling for status updates
    function pollForStatusUpdates() {
        setInterval(() => {
            fetch(`/api/booking/${bookingId}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== currentStatus) {
                        currentStatus = data.status;
                        updateTimelineStatus();
                        updateBookingStatus(data.status);
                    }
                });
        }, 5000); // Poll every 5 seconds
    }

    // Initialize
    updateTimelineStatus();
    updateBookingStatus(currentStatus);
    pollForStatusUpdates();
});
</script>

<!-- Add required CSS for animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

<style>
/* Enhance existing timeline styles */
.timeline-item {
    transition: all 0.3s ease-in-out;
}

.timeline-item .point {
    transition: background-color 0.3s ease-in-out;
}

.timeline-item.active .point {
    animation: pulse 2s infinite;
}

.timeline-item .timeline-connector {
    transition: border-color 0.3s ease-in-out;
}

/* Smooth color transitions */
.timeline-item:nth-child(1).active .point,
.timeline-item:nth-child(1).active .timeline-connector {
    transition: all 0.3s ease-in-out;
}

.timeline-item:nth-child(2).active .point,
.timeline-item:nth-child(2).active .timeline-connector {
    transition: all 0.3s ease-in-out;
}

.timeline-item:nth-child(3).active .point,
.timeline-item:nth-child(3).active .timeline-connector {
    transition: all 0.3s ease-in-out;
}

.timeline-item:nth-child(4).active .point {
    transition: all 0.3s ease-in-out;
}

/* Enhanced pulse animation */
@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 var(--pulse-box-shadow, rgba(0, 123, 255, 0.4));
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px var(--pulse-box-shadow-fade, rgba(0, 123, 255, 0));
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 var(--pulse-box-shadow-fade, rgba(0, 123, 255, 0));
1qwertyui    }
}

/* Base Timeline Structure */
.vertical-timeline {
    position: relative;
    padding: 20px 0;
    margin-left: 100px;
}

.timeline-item {
    position: relative;
    padding-bottom: 50px;
    margin-bottom: 15px;  
}

.timeline-date {
    position: absolute;
    left: -120px;
    width: 100px;
    text-align: right;
}

.timeline-content {
    display: flex;
    align-items: flex-start;
    padding-top: 10px;    
}


.timeline-connector {
    position: absolute;
    left: 9px;
    top: 25px;
    bottom: 0;
    width: 2px;
    border-left: 2px dashed #e0e0e0;
    height: calc(100% - 15px);
}


.point {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e0e0e0;
    margin-right: 15px;
}


/* New Booking - Red */
.timeline-item:nth-child(1).active .point {
    background: #dc3545;
}
.timeline-item:nth-child(1).active .timeline-connector {
    border-left: 2px dashed #dc3545;
}

/* Accepted - Yellow */
.timeline-item:nth-child(2).active .point {
    background: #ffc107;
}
.timeline-item:nth-child(2).active .timeline-connector {
    border-left: 2px dashed #ffc107;
}

/* Assigned - Orange */
.timeline-item:nth-child(3).active .point {
    background: #fd7e14;
}
.timeline-item:nth-child(3).active .timeline-connector {
    border-left: 2px dashed #fd7e14;
}

/* Completed - Green */
.timeline-item:nth-child(4).active .point {
    background: #28a745;
}
.timeline-item:nth-child(4).active .timeline-connector {
    border-left: 2px dashed #28a745;
}

/* Remove last connector */
.timeline-item:last-child .timeline-connector {
    display: none;
}

/* Add style for on_going status */
.timeline-item.active[data-status="on_going"] .point {
    background: #0dcaf0; /* Using Bootstrap's info color */
    animation: pulse 2s infinite;
}

.timeline-item.active[data-status="on_going"] .timeline-connector {
    border-left: 2px dashed #0dcaf0;
}
</style>