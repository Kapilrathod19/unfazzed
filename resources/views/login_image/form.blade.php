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
                        {{ html()->form('POST', route('login_image.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="login_image">{{ __('messages.image') }} <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" name="login_image" class="custom-file-input" id="login_image" accept="image/*">
                                        <label class="custom-file-label" for="login_image">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                    </div>
                                    <div class="help-block with-errors text-danger"></div>
                                    <p class="mt-2 text-secondary"><small>{{ __('messages.recommended_size_login_image') ?? 'Recommended size: 1080x1920 px (Portrait)' }}</small></p>
                                </div>

                                <div class="mt-4">
                                    {{ html()->submit(__('messages.save'))->class('btn btn-primary btn-md') }}
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label d-block text-left">{{ __('messages.current_image') ?? 'Current Preview' }}</label>
                                    <div class="login-image-preview mt-2 p-2 border rounded bg-light d-inline-block shadow-sm">
                                        @if(isset($login_image) && $login_image)
                                            <img src="{{ $login_image }}" alt="login-image" class="img-fluid rounded" style="max-height: 450px; width: auto; object-fit: contain;">
                                        @else
                                            <div class="text-center py-5 px-4 text-muted border-dashed rounded" style="min-width: 200px;">
                                                <i class="fas fa-image fa-4x mb-3 opacity-25"></i>
                                                <p>{{ __('messages.no_image_uploaded') ?? 'No image uploaded yet' }}</p>
                                            </div>
                                        @endif
                                    </div>
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
