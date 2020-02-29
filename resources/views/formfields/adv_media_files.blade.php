<div class="field-helper">
    @lang('voyager-extension::bread.adv_media_files.help', ['gallery' => $row->field])
</div>
@php
    $adv_media_files = $dataTypeContent->getMedia($row->field)
@endphp
@if(count($adv_media_files) > 0)
<div class="adv-media-files-holder">
    <div id="{{ $row->field }}" class="adv-media-files-list sortable-files-field-{{ $row->field }}"
         data-bunch-adv-remove-holder="bunch-adv-remove-{{ $row->field }}"
         data-extra-fields="{{ isset($row->details->extra_fields)? "true" : "false" }}"
         data-type="{{ $row->type }}"
         data-model="{{ $dataType->model_name }}"
         data-slug="{{ $dataType->slug }}"
         data-field-name="{{ $row->field }}"
         data-id="{{ $dataTypeContent->id }}"
         data-input-id="adv-media-files-input-{{ $row->field }}">

        @foreach($adv_media_files as $key => $adv_file)
        <div class="adv-media-files-item">
            <div class="adv-media-files-item-holder"
                 data-type="{{ $row->type }}"
                 data-model="{{ $dataType->model_name }}"
                 data-slug="{{ $dataType->slug }}"
                 data-field-name="{{ $row->field }}"
                 data-file-name="{{ $adv_file->file_name }}"
                 data-id="{{ $dataTypeContent->id }}"
                 data-file-id="{{ $adv_file->id }}">

                <div class="adv-media-files-order">
                    {{ $key + 1 }}
                </div>

                <div class="adv-media-files-bunch">
                    <span class="adv-media-files-mark icon voyager-check" title="Mark file"></span>
                </div>

                <div class="adv-media-files-actions">
                    <span class="adv-media-files-change icon voyager-refresh" title="Change file"></span>
                    <span class="adv-media-files-edit icon voyager-edit" title="Edit file meta fields"></span>
                    <span class="adv-media-files-remove icon voyager-x" title="Delete file"></span>
                </div>
                <div class="adv-media-files-file">
                    @if(explode('/', $adv_file->mime_type)[0] === 'image')
                        <img src="{{ $adv_file->getFullUrl() }}">
                    @else
                        <img class="file-type" src="{{ voyager_extension_asset('icons/files/'.explode('/', $adv_file->mime_type)[1].'.svg') }}">
                    @endif
                </div>
            </div>
            <div class="adv-media-files-data">
                <span class="adv-media-files-filename">{{ Str::limit($adv_file->file_name, 20, ' (...)') }} <i class="@if($adv_file->size > 100000) large @endif">{{ $adv_file->human_readable_size }}</i></span>
                <span class="adv-media-files-title">
                    @if(!empty($adv_file->getCustomProperty('title')))
                        {{ Str::limit($adv_file->getCustomProperty('title'), 30, ' (...)') }}
                    @else
                        <i>@lang('voyager-extension::bread.adv_image.not_set_title')</i>
                    @endif
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bunch-adv-select-all" data-files-gallery-list="{{ $row->field }}">
        <a href="javascript:;"
           title="@lang('voyager-extension::bread.adv_media_files.select_all')"
           class="bunch-adv-media-files-select-all">
            <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_media_files.select_all')</span>
        </a>
    </div>

    <div id="bunch-adv-remove-{{ $row->field }}" class="bunch-adv-remove-holder hidden" data-files-gallery-list="{{ $row->field }}">
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
           id="adv-media-files-input-{{ $row->field }}"
           type="file"
           name="{{ $row->field }}[]"
           multiple="multiple"
           accept=@if(isset($row->details->input_accept)) {{ $row->details->input_accept }}  @else "image/*" @endif>
</div>
