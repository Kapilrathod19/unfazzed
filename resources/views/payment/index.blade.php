<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    </head>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center justify-content-between gy-3">
                <div class="col-md-6 col-lg-4 col-xl-5">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <form action="{{ route('payment.bulk-action') }}" id="quick-action-form"
                            class="form-disabled d-flex gap-3 align-items-center">
                            @csrf
                            @if (auth()->user()->hasAnyRole(['admin']))
                                <select name="action_type" class="form-select select2" id="quick-action-type"
                                    style="width:100%" disabled>
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                </select>

                                <button id="quick-action-apply" class="btn btn-primary" data-ajax="true"
                                    data--submit="{{ route('payment.bulk-action') }}" data-datatable="reload"
                                    data-confirmation='true' data-title="{{ __('payment', ['form' => __('payment')]) }}"
                                    title="{{ __('payment', ['form' => __('payment')]) }}"
                                    data-message='{{ __('Do you want to perform this action?') }}'
                                    >{{ __('messages.apply') }}</button>
                            @endif
                        </form>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Export">{{ __('messages.export') }}</button>
                    </div>
                </div>

                <div class="col-md-6 col-lg-8 col-xl-7">
                    <div class="d-flex justify-content-end gap-2 align-items-center">
                        <div class="datatable-filter ml-auto">
                            <select name="column_status" id="column_status" class="select2 form-select"
                                data-filter="select" style="width: 100%">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="advanced_paid">{{ __('messages.advanced_paid') }}</option>
                                <option value="paid">{{ __('messages.paid') }}</option>
                                <option value="pending_by_admin">{{ __('messages.pending_by_admin') }}</option>
                                <option value="approved_by_admin">{{ __('messages.approved_by_admin') }}</option>
                                <option value="approved_by_provider">{{ __('messages.approved_by_provider') }}</option>
                                <option value="pending_by_provider">{{ __('messages.pending_by_provider') }}</option>
                                <option value="send_to_provider">{{ __('messages.send_to_provider') }}</option>
                                <option value="approved_by_handyman">{{ __('messages.approved_by_handyman') }}</option>
                            </select>
                        </div>
                        <div class="input-group input-group-search ms-2">
                            <span class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control dt-search" placeholder="Search..."
                                aria-label="Search" aria-describedby="addon-wrapping" aria-controls="dataTableBuilder">
                        </div>
                        <button class="btn btn-primary d-flex align-items-center gap-1 btn-group position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                            <i class="ph ph-funnel"></i>{{ __('messages.filter') }}
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-striped border">
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-advance-filter>
        <x-slot name="title">
            <h4>{{ __('messages.filter_title') }}</h4>
        </x-slot>
        <div class="form-group datatable-filter">
            <label class="form-label" for="date_range">{{ __('messages.daterange_label') }}</label>
            <input type="text" id="datepicker1" class="form-control flatpickr" placeholder="{{ __('messages.select_date_range') }}" />
        </div>
    </x-advance-filter>

    <!-- Modal -->
    <div class="modal fade" id="Export" tabindex="-1" role="dialog" aria-labelledby="exportModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalTitle">{{__('messages.export_data')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{__('messages.select_file_type')}}</label>
                        <div class="btn-group btn-group-toggle d-flex flex-wrap export-type" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="fileType" value="xlsx" checked /> XLSX
                            </label>
                        </div>
                    </div>
                    <!-- Column Selection -->
                    <div class="form-group">
                        <label>{{__('messages.select_column')}}</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colID" checked />
                            <label class="form-check-label" for="colID">{{ __('messages.id') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colService" checked />
                            <label class="form-check-label" for="colService">{{ __('messages.service') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colUser" checked />
                            <label class="form-check-label" for="colUser">{{ __('messages.user') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colPaymentType" checked />
                            <label class="form-check-label" for="colPaymentType">{{ __('messages.payment_type') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colStatus" checked />
                            <label class="form-check-label" for="colStatus">{{ __('messages.status') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colDateTime" checked />
                            <label class="form-check-label" for="colDateTime">{{ __('messages.datetime') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="colTotalAmount" checked />
                            <label class="form-check-label" for="colTotalAmount">{{ __('messages.total_amount') }}</label>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="downloadButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">{{ __('messages.export') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {

            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('payment.index_data') }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        };
                        d.advanceFilter = {
                            date_range: selectedFilters.date_range
                        };
                    },
                },
                columns: [
                    @if (auth()->user()->hasAnyRole(['admin']))
                        {
                            name: 'check',
                            data: 'check',
                            title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                            exportable: false,
                            orderable: false,
                            searchable: false,
                        },
                    @endif {
                        data: 'updated_at',
                        name: 'updated_at',
                        title: "{{ __('product.lbl_update_at') }}",
                        orderable: true,
                        visible: false,
                    },
                    {
                        data: 'id',
                        name: 'id',
                        title: "{{ __('messages.id') }}",
                    },
                    {
                        data: 'booking_id',
                        name: 'booking_id',
                        title: "{{ __('messages.service') }}",
                        //  orderable: false,
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id',
                        title: "{{ __('messages.user') }}"
                    },
                    {
                        data: 'payment_type',
                        name: 'payment_type',
                        title: "{{ __('messages.payment_type') }}"
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        title: "{{ __('messages.status') }}"
                    },
                    {
                        data: 'datetime',
                        name: 'datetime',
                        title: "{{ __('messages.datetime') }}"
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        title: "{{ __('messages.total_paid_amount') }}"
                    },
                    @if (auth()->user()->hasAnyRole(['admin']))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            title: "{{ __('messages.action') }}",
                        }
                    @endif ()
                ],
                order: [
                    @if (auth()->user()->hasAnyRole(['admin']))
                        [7, 'desc']
                    @else
                        [6, 'desc']
                    @endif
                ],
                language: {
                    processing: "{{ __('messages.processing') }}" // Set your custom processing text
                }
            });
            
            $("#datepicker1").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2 || selectedDates.length === 1) {
                        selectedFilters.date_range = dateStr;
                        $('#datatable').DataTable().ajax.reload();
                    }
                }
            });

            // Export logic
            document.getElementById('downloadButton').addEventListener('click', function() {
                const fileType = document.querySelector('input[name="fileType"]:checked').value;
                const selectedColumns = [];
                document.querySelectorAll('.form-check-input:checked').forEach((checkbox) => {
                    selectedColumns.push(checkbox.id);
                });

                const formData = new FormData();
                formData.append('format', fileType);
                formData.append('columns', JSON.stringify(selectedColumns));
                formData.append('advanceFilter[date_range]', selectedFilters.date_range || '');

                const buttonText = document.querySelector('.button-text');
                const spinner = document.querySelector('.spinner-border');
                const downloadButton = this;
                downloadButton.disabled = true;
                spinner.classList.remove('d-none');
                buttonText.textContent = "Loading...";

                var baseUrl = $('meta[name="baseUrl"]').attr('content') || "{{ url('/') }}";

                fetch(baseUrl+'/payment-export', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json();
                    }
                    return response.blob();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        resetExportModal();
                        return;
                    }
                    const url = window.URL.createObjectURL(data);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `Payments.${fileType}`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                    $('#Export').modal('hide');
                    resetExportModal();
                })
                .catch(error => {
                    console.error('Export error:', error);
                    alert('Failed to export data. Please try again.');
                    resetExportModal();
                });
            });

            function resetExportModal() {
                const downloadButton = document.getElementById('downloadButton');
                const spinner = document.querySelector('.spinner-border');
                const buttonText = document.querySelector('.button-text');
                downloadButton.disabled = false;
                spinner.classList.add('d-none');
                buttonText.textContent = "{{ __('messages.export') }}";
            }
        });

        let selectedFilters = {
            date_range: ''
        };


        $(document).ready(function() {
            $('#statusSelect').change(function() {
                var selectedValue = $(this).val();
                var selectedOption = $('#statusSelect option:selected');
                var route = selectedOption.data('route');

                if (selectedValue === 'cash' && route) {
                    window.location.href = route;
                }
                window.location.href = route;
            });
        });

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            console.log(actionValue)
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });

        $(document).on('update_quick_action', function() {

        })

        $(document).on('click', '[data-ajax="true"]', function(e) {
            e.preventDefault();
            const button = $(this);
            const confirmation = button.data('confirmation');

            if (confirmation === 'true') {
                const message = button.data('message');
                if (confirm(message)) {
                    const submitUrl = button.data('submit');
                    const form = button.closest('form');
                    form.attr('action', submitUrl);
                    form.submit();
                }
            } else {
                const submitUrl = button.data('submit');
                const form = button.closest('form');
                form.attr('action', submitUrl);
                form.submit();
            }
        });
    </script>
    
</x-master-layout>
