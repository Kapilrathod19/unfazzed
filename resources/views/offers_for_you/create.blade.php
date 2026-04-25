<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            <a href="{{ route('offers-for-you.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('offers-for-you.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('offer')->open()}}
                        {{ html()->hidden('id',$offerdata->id ?? null) }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                                {{ html()->text('title',$offerdata->title)->placeholder(__('messages.title'))->class('form-control')->required()}}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.short_description_1'), 'short_description_1')->class('form-control-label') }}
                                {{ html()->text('short_description_1',$offerdata->short_description_1)->placeholder(__('messages.short_description_1'))->class('form-control')}}
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.short_description_2'), 'short_description_2')->class('form-control-label') }}
                                {{ html()->text('short_description_2',$offerdata->short_description_2)->placeholder(__('messages.short_description_2'))->class('form-control')}}
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.background_color'), 'background_color')->class('form-control-label') }}
                                {{ html()->input('color', 'background_color', $offerdata->background_color ?? '#ffffff')->class('form-control')}}
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.type') . ' <span class="text-danger">*</span>', 'type')->class('form-control-label') }}
                                {{ html()->select('type', ['small' => 'Small Card', 'large' => 'Large Card'], $offerdata->type)->class('form-select select2js')->required()}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $offerdata->status)->class('form-select select2js')->required()}}
                            </div>
                    
                            <div class="form-group col-md-4">
                                <label class="form-control-label" for="offer_image">{{ __('messages.image') }} / {{ __('messages.icon') }} <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="offer_image" class="custom-file-input" onchange="previewImage(event)" accept="image/*">
                                    @if($offerdata && getMediaFileExit($offerdata, 'offer_image'))
                                        <label class="custom-file-label upload-label">{{ $offerdata->getFirstMedia('offer_image')->file_name }}</label>
                                    @else
                                        <label class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.image')]) }}</label>
                                    @endif
                                </div>
                            </div>
                    
                            <div class="col-md-2 mb-2">
                                <div class="image-preview-container position-relative">
                                    <img id="offer_image_preview" src="{{ getMediaFileExit($offerdata, 'offer_image') ? getSingleMedia($offerdata, 'offer_image') : '' }}" alt="Image preview" class="attachment-image mt-1" style="width: 150px; {{ getMediaFileExit($offerdata, 'offer_image') ? '' : 'display: none;' }}">
                                    <a class="text-danger remove-file" id="removeButton" onclick="removeImage(event, '{{ route('remove.file', ['id' => $offerdata->id, 'type' => 'offer_image']) }}')" style="{{ getMediaFileExit($offerdata, 'offer_image') ? 'display: inline;' : 'display: none;' }}">
                                        <i class="ri-close-circle-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    
                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end')->id('saveButton')}}
                        {{ html()->form()->close() }}
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    <script type="text/javascript">
        function previewImage(event) {
            const preview = document.getElementById('offer_image_preview');
            const fileLabel = document.querySelector('.custom-file-label');
            const saveButton = document.getElementById('saveButton');
            const removeButton = document.getElementById('removeButton');

            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.style.display = 'block';
            fileLabel.textContent = event.target.files[0].name;

            removeButton.style.display = 'inline';
            saveButton.disabled = false;
        }

        function removeImage(event, removeUrl) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to remove the image?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    const preview = document.getElementById('offer_image_preview');
                    const fileLabel = document.querySelector('.custom-file-label');
                    const saveButton = document.getElementById('saveButton');
                    const removeButton = document.getElementById('removeButton');

                    $.ajax({
                        url: removeUrl,
                        type: 'POST',
                        success: function(result) {
                            preview.src = '';
                            preview.style.display = 'none';
                            document.querySelector('input[name="offer_image"]').value = '';
                            fileLabel.textContent = '{{ __('messages.choose_file', ['file' => __('messages.image')]) }}';
                            saveButton.disabled = true;
                            removeButton.style.display = 'none';
                            Swal.fire('Deleted!', 'Image has been removed.', 'success');
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkImage();
        });

        function checkImage() {
            var id = @json($offerdata->id);
            if(!id) return;
            var route = "{{ route('check-image', ':id') }}";
            route = route.replace(':id', id);
            var type = 'offer';

            $.ajax({
                url: route,
                type: 'GET',
                data: { type: type },
                success: function(result) {
                    var attachments = result.results;
                    var attachmentsCount = Object.keys(attachments).length;
                    if (attachmentsCount == 0) {
                        $('input[name="offer_image"]').attr('required', 'required');
                    } else {
                        $('input[name="offer_image"]').removeAttr('required');
                    }
                }
            });
        }
    </script>
    @endsection
</x-master-layout>
