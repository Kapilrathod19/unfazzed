<!-- Modal -->

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
        </div>

       {{ html()->form('POST', route('booking.provider_assigned'))->attribute('data-toggle', 'validator')->open() }}
        <div class="modal-body">
        {{ html()->hidden('id',$bookingdata->id ?? null) }}

            <div class="row">
                
                <div class="col-md-12 form-group ">
                {{ html()->label(__('messages.select_name', ['select' => __('messages.provider')]) . ' <span class="text-danger">*</span>', 'provider_id')->class('form-control-label')}}
                    
                    <br />
                    {{ html()->select('provider_id', [null => __('messages.select_name', ['select' => __('messages.provider')])] + $providers->toArray(), $bookingdata->provider_id)
                                        ->class('select2js form-group')
                                        ->id('provider_id')
                                        ->required()
                                    }}
                  
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">{{ trans('messages.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax" >{{ trans('messages.save') }}</button>
        </div>
        {{ html()->form()->close() }}
  
    </div>
</div>
<script>
    $('#provider_id').select2({
        width: '100%',
        placeholder: "{{ __('messages.select_name',['select' => __('messages.provider')]) }}",
    });
</script>
