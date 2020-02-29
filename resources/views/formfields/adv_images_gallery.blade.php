<div class="field-helper">
    @lang('voyager-extension::bread.adv_images_gallery.help', ['gallery' => $row->field])
</div>
@php
    $adv_images_gallery = $dataTypeContent->getMedia($row->field)
@endphp
@if(count($adv_images_gallery) > 0)
<div class="adv-images-gallery-holder">
    <div id="{{ $row->field }}" class="adv-images-gallery-list sortable-images-field-{{ $row->field }}"
         data-bunch-adv-remove-holder="bunch-adv-remove-{{ $row->field }}"
         data-extra-fields="{{ isset($row->details->extra_fields)? "true" : "false" }}"
         data-type="{{ $row->type }}"
         data-model="{{ $dataType->model_name }}"
         data-slug="{{ $dataType->slug }}"
         data-field-name="{{ $row->field }}"
         data-id="{{ $dataTypeContent->id }}">

        @foreach($adv_images_gallery as $key => $adv_image)
        <div class="adv-images-gallery-item">
            <div class="adv-images-gallery-item-holder"
                 data-type="{{ $row->type }}"
                 data-model="{{ $dataType->model_name }}"
                 data-slug="{{ $dataType->slug }}"
                 data-field-name="{{ $row->field }}"
                 data-file-name="{{ $adv_image->file_name }}"
                 data-id="{{ $dataTypeContent->id }}"
                 data-image-id="{{ $adv_image->id }}">

                <div class="adv-images-gallery-order">
                    {{ $key + 1 }}
                </div>

                <div class="adv-images-gallery-bunch">
                    <span class="adv-images-gallery-mark icon voyager-check" title="Mark image"></span>
                </div>

                <div class="adv-images-gallery-actions">
                    <span class="adv-images-gallery-change icon voyager-refresh" title="Change image"></span>
                    <span class="adv-images-gallery-edit icon voyager-edit" title="Edit image meta fields"></span>
                    <span class="adv-images-gallery-remove icon voyager-x" title="Delete image"></span>
                </div>
                <div class="adv-images-gallery-image">
                    <img src="{{ $adv_image->getFullUrl() }}">
                </div>
            </div>
            <div class="adv-images-gallery-data">
                <span class="adv-images-gallery-filename">{{ Str::limit($adv_image->file_name, 20, ' (...)') }} <i class="@if($adv_image->size > 100000) large @endif">{{ $adv_image->human_readable_size }}</i></span>
                <span class="adv-images-gallery-title">
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
           title="@lang('voyager-extension::bread.adv_images_gallery.select_all')"
           class="bunch-adv-images-gallery-select-all">
            <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_images_gallery.select_all')</span>
        </a>
    </div>

    <div id="bunch-adv-remove-{{ $row->field }}" class="bunch-adv-remove-holder hidden" data-images-gallery-list="{{ $row->field }}">
        <a href="javascript:;"
           title="@lang('voyager-extension::bread.adv_images_gallery.remove_selected')"
           class="btn btn-sm btn-danger bunch-adv-images-gallery-remove">
            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_images_gallery.remove_selected')</span>
        </a>
        <a href="javascript:;"
           title="@lang('voyager-extension::bread.adv_images_gallery.unmark_selected')"
           class="bunch-adv-images-gallery-unmark">
            <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_images_gallery.unmark_selected')</span>
        </a>
    </div>

</div>
@endif
<div class="adv-images-gallery-file-upload">
    <input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple" accept="image/*">
</div>
