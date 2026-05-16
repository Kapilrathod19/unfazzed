<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ html()->form('POST', route('app_share_link.store'))->attribute('data-toggle', 'validator')->open() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="app_share_link">{{ __('messages.url') }} <span class="text-danger">*</span></label>
                                    {{ html()->text('app_share_link', $app_share_link)->class('form-control')->placeholder(__('messages.url'))->required() }}
                                    <div class="help-block with-errors text-danger"></div>
                                </div>

                                <div class="mt-2 mb-4">
                                    {{ html()->submit(__('messages.save'))->class('btn btn-primary btn-md') }}
                                </div>
                            </div>
                        </div>
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
