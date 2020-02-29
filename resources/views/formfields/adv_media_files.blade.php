<div class="field-helper">
    @lang('voyager-extension::bread.adv_media_files.help', ['gallery' => $row->field])
</div>
@php
    $adv_media_files = $dataTypeContent->getMedia($row->field)
@endphp
@if(count($adv_media_files) > 0)
<div class="adv-media-files-holder">
    <div id="{{ $row->field }}" class="adv-media-files-list sortable-images-field-{{ $row->field }}"
         data-bunch-adv-remove-holder="bunch-adv-remove-{{ $row->field }}"
         data-extra-fields="{{ isset($row->details->extra_fields)? "true" : "false" }}"
         data-type="{{ $row->type }}"
         data-model="{{ $dataType->model_name }}"
         data-slug="{{ $dataType->slug }}"
         data-field-name="{{ $row->field }}"
         data-id="{{ $dataTypeContent->id }}">

        @foreach($adv_media_files as $key => $adv_image)
        <div class="adv-media-files-item">
            <div class="adv-media-files-item-holder"
                 data-type="{{ $row->type }}"
                 data-model="{{ $dataType->model_name }}"
                 data-slug="{{ $dataType->slug }}"
                 data-field-name="{{ $row->field }}"
                 data-file-name="{{ $adv_image->file_name }}"
                 data-id="{{ $dataTypeContent->id }}"
                 data-image-id="{{ $adv_image->id }}">

                <div class="adv-media-files-order">
                    {{ $key + 1 }}
                </div>

                <div class="adv-media-files-bunch">
                    <span class="adv-media-files-mark icon voyager-check" title="Mark image"></span>
                </div>

                <div class="adv-media-files-actions">
                    <span class="adv-media-files-change icon voyager-refresh" title="Change image"></span>
                    <span class="adv-media-files-edit icon voyager-edit" title="Edit image meta fields"></span>
                    <span class="adv-media-files-remove icon voyager-x" title="Delete image"></span>
                </div>
                <div class="adv-media-files-image">
                    @if(explode('/', $adv_image->mime_type)[0] === 'image')
                        <img src="{{ $adv_image->getFullUrl() }}">
                    @else
                        <img class="file-type" src="{{ voyager_extension_asset('icons/files/'.explode('/', $adv_image->mime_type)[1].'.svg') }}">
                    @endif
                </div>
            </div>
            <div class="adv-media-files-data">
                <span class="adv-media-files-filename">{{ Str::limit($adv_image->file_name, 20, ' (...)') }} <i class="@if($adv_image->size > 100000) large @endif">{{ $adv_image->human_readable_size }}</i></span>
                <span class="adv-media-files-title">
                    @if(!empty($adv_image->getCustomProperty('title')))
                        {{ Str::limit($adv_image->getCustomProperty('title'), 30, ' (...)') }}
                    @else
                        <i>@lang('voyager-extension::bread.adv_image.not_set_title')</i>
                    @endif
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bunch-adv-select-all" data-images-gallery-list="{{ $row->field }}">
        <a href="javascript:;"
           title="@lang('voyager-extension::bread.adv_media_files.select_all')"
           class="bunch-adv-media-files-select-all">
            <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_media_files.select_all')</span>
        </a>
    </div>

    <div id="bunch-adv-remove-{{ $row->field }}" class="bunch-adv-remove-holder hidden" data-images-gallery-list="{{ $row->field }}">
        <a href="javascript:;"
           title="@lang('voyager-extension::bread.adv_media_files.remove_selected')"
           class="btn btn-sm btn-danger bunch-adv-media-files-remove">
            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_media_files.remove_selected')</span>
        </a>
        <a href="javascript:;"
           title="@lang('voyager-extension::bread.adv_media_files.unmark_selected')"
           class="bunch-adv-media-files-unmark">
            <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_media_files.unmark_selected')</span>
        </a>
    </div>

</div>
@endif
<div class="adv-media-files-file-upload">
    <input @if($row->required == 1) required @endif
           type="file"
           name="{{ $row->field }}[]"
           multiple="multiple"
           accept=@if(isset($row->details->input_accept)) {{ $row->details->input_accept }}  @else "image/*" @endif>
</div>
