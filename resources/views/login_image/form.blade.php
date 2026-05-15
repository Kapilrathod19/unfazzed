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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="login_image">{{ __('messages.image') }} <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" name="login_image[]" class="custom-file-input" id="login_image" accept="image/*" multiple>
                                        <label class="custom-file-label" for="login_image">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                    </div>
                                    <div class="help-block with-errors text-danger"></div>
                                    <p class="mt-2 text-secondary"><small>{{ __('messages.recommended_size_login_image') ?? 'Recommended size: 1080x1920 px' }} </small></p>
                                </div>

                                <div class="mt-2 mb-4">
                                    {{ html()->submit(__('messages.save'))->class('btn btn-primary btn-md') }}
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label d-block text-left">Current Uploaded Images</label>
                                    <div class="row mt-2">
                                        @if(isset($login_images) && count($login_images) > 0)
                                            @foreach($login_images as $image)
                                                <div class="col-md-3 mb-3">
                                                    <div class="position-relative border rounded p-1 bg-white shadow-sm h-100 d-flex flex-column">
                                                        <img src="{{ $image->getUrl() }}" alt="login-image" class="img-fluid rounded mb-2" style="height: 200px; width: 100%; object-fit: cover;">
                                                        <div class="mt-auto text-center pb-1">
                                                            <a href="{{ route('login_image.destroy', $image->id) }}" 
                                                               class="btn btn-outline-danger btn-sm w-100" 
                                                               data--submit="confirm_form" 
                                                               data-confirmation='true' 
                                                               data-title="{{ __('messages.delete_form_title', ['form' => __('messages.image')]) }}" 
                                                               title="{{ __('messages.delete_form_title', ['form' => __('messages.image')]) }}" 
                                                               data-message='{{ __("messages.delete_msg") }}'>
                                                                <i class="fas fa-trash-alt mr-1"></i> {{ __('messages.delete') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="text-center py-5 px-4 text-muted border border-dashed rounded bg-light" style="min-height: 200px;">
                                                    <i class="fas fa-image fa-4x mb-3 opacity-25"></i>
                                                    <p>{{ __('messages.no_image_uploaded') ?? 'No images uploaded yet' }}</p>
                                                </div>
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
