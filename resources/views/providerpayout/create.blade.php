<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        <a href="{{ route('earning') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('providerpayout.store'))->attributes(['enctype' => 'multipart/form-data', 'data-toggle' => 'validator', 'id' => 'providerpayout'])->open() }}       
                    {{ html()->hidden('provider_id',$payoutdata->provider_id ?? null) }}
                    {{ html()->hidden('redirect_type', $redirect_type) }}
                    <div class="row">
                        <div class="form-group col-md-4" id="payment_method_id">
                            {{ html()->label(trans('messages.method') . ' <span class="text-danger">*</span>', 'method')->class('form-control-label') }}
                            {{ html()->select('payment_method', ['bank' => __('messages.bank') /* ,'cash' => __('messages.cash') */], old('method'))->attributes(['id' => 'method', 'class' => 'form-select select2js', 'required']) }}
                        </div>
                
                        <div class="form-group col-md-4" id="select_bank">
                            {{ html()->label(__('messages.select_bank', ['select' => __('messages.select_bank')]) . ' <span class="text-danger">*</span>', 'bank')->class('form-control-label') }}
                            <a href="{{ route('bank.create', ['user_id' => $payoutdata->provider_id]) }}" class="me-1 btn-link btn-link-hover"><i class="fa fa-plus-circle"></i> {{ trans('messages.add_form_title', ['form' => trans('messages.bank')]) }}</a>
                            <br />
                            {{ html()->select('bank', [])
                                ->attributes(['class' => 'select2js form-group col-md-12 bank','id' => 'bank', 'data-placeholder' => __('messages.select_bank', ['select' => __('messages.')])]) }}
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.amount'), 'amount')->class('form-control-label') }}
                            {{ html()->text('amount', $payoutdata->amount ?? 0)->attributes([
                                'class' => 'form-control',
                                'id' => 'amount',
                                'placeholder' => __('messages.amount'),
                                'required'
                            ]) }}
                        </div>

                        {{-- Bank Details Display Area --}}
                        <div class="form-group col-md-12 d-none" id="bank_details_display">
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{-- <h6 class="card-title">{{ __('messages.bank_name', ['select' => __('messages.bank_details')]) }}</h6> --}}
                                    <div class="row">
                                        <div class="col-md-3"><strong>Bank Name:</strong> <span id="display_bank_name"></span></div>
                                        <div class="col-md-3"><strong>Provider Name:</strong> <span id="display_branch_name"></span></div>
                                        <div class="col-md-3"><strong>Account No:</strong> <span id="display_account_no"></span></div>
                                        <div class="col-md-3"><strong>IFSC Code:</strong> <span id="display_ifsc_no"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                        </div>
                    {{ html()->submit('Paid')->attributes(['class' => 'btn btn-md btn-primary float-end', 'id' => 'saveButton']) }}
                    {{ html()->form()->close() }}
                </div>                
            </div>
        </div>
    </div>
</div>
@section('bottom_script')
<script type="text/javascript">
            (function($) {
                "use strict";
                $(document).ready(function(){
                    var provider_id = $('input[name="provider_id"]').val();
       
                    bankdetails(provider_id);

                    $(document).on('change', '#method', function() {
                        var payment_method = $(this).val();

                        if (payment_method == 'bank') {
                            $("#select_bank").removeClass("d-none"); 
                            $("#bank").attr("required", true);      
                            bankdetails(provider_id);
                        } else {
                            $('#select_bank').addClass("d-none");  
                            $("#bank").attr("required", false);      
                            $('#bank_details_display').addClass('d-none');
                        }
                    });

                    // Trigger on both change and select2:select
                    $('#bank').on('change select2:select', function() {
                        var bank_id = $(this).val();
                        if (bank_id) {
                            fetchBankDetails(bank_id);
                        } else {
                            $('#bank_details_display').addClass('d-none');
                        }
                    });
                   
                })

                function fetchBankDetails(bank_id) {
                    var route = "{{ route('ajax-list', ['type' => 'bank_details']) }}&bank_id=" + bank_id;
                    $.ajax({
                        url: route,
                        type: 'GET',
                        success: function(result) {
                            if (result && result.id) {
                                $('#display_bank_name').text(result.bank_name || '-');
                                $('#display_branch_name').text(result.branch_name || '-');
                                $('#display_account_no').text(result.account_no || '-');
                                $('#display_ifsc_no').text(result.ifsc_no || '-');
                                $('#bank_details_display').removeClass('d-none');
                            } else {
                                $('#bank_details_display').addClass('d-none');
                            }
                        },
                        error: function(e) {
                            console.error("Failed to fetch bank details", e);
                        }
                    });
                }

                function bankdetails(provider_id){
                    var bank_route = "{{ route('ajax-list', [ 'type' => 'bank','provider_id' =>'']) }}"+provider_id;
                    bank_route = bank_route.replace('amp;','');

                    $.ajax({
                        url: bank_route,
                        success: function(result){
                            $('#bank').select2({
                                width: '100%',
                                placeholder: "{{ trans('messages.bank_name',['select' => trans('messages.bank_name')]) }}",
                                data: result.results
                            });
                            
                            // If banks exist, auto-select the first one and fetch its details
                            if (result.results && result.results.length > 0) {
                                var firstBankId = result.results[0].id;
                                $('#bank').val(firstBankId).trigger('change');
                            }
                        }
                    });
                }
        
               
            })(jQuery);


            window.onload = function() {
    if (window.history && window.history.pushState) {
        window.history.pushState('', null, '');
        window.onpopstate = function() {
            window.history.pushState('', null, '');
        };
    }
};

    $(document).ready(function() {
        $('#providerpayout').on('submit', function() {
            $('#saveButton').attr('disabled', true); 
        });
    });
</script>
@endsection
</x-master-layout>
