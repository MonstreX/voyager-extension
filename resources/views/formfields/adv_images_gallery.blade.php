<div class="field-helper">
    @lang('voyager-extension::bread.adv_images_gallery.help', ['gallery' => $row->field])
</div>
@if($adv_images_gallery = $dataTypeContent->getMedia($row->field))
<div class="adv-images-gallery-holder">
    <div id="{{ $row->field }}" class="adv-images-gallery-list sortable-images-field-{{ $row->field }}"
         data-extra-fields="{{ isset($row->details->extra_fields)? "true" : "false" }}"
         data-type="{{ $row->type }}"
         data-model="{{ $dataType->model_name }}"
         data-slug="{{ $dataType->slug }}"
         data-field-name="{{ $row->field }}"
         data-id="{{ $dataTypeContent->id }}"
         data-token="{{ csrf_token() }}"
         data-sort-route="{{ route('voyager.'.$dataType->slug.'.media.sort') }}"
         data-form-route="{{ route('voyager.'.$dataType->slug.'.media.form.load') }}"
         data-update-route="{{ route('voyager.'.$dataType->slug.'.media.update') }}" >

        @foreach($adv_images_gallery as $key => $adv_image)
        <div class="adv-images-gallery-item">
            <div class="adv-images-gallery-item-holder"
                 data-type="{{ $row->type }}"
                 data-model="{{ $dataType->model_name }}"
                 data-slug="{{ $dataType->slug }}"
                 data-field-name="{{ $row->field }}"
                 data-file-name="{{ $adv_image->file_name }}"
                 data-id="{{ $dataTypeContent->id }}"
                 data-image-id="{{ $adv_image->id }}"
                 data-token="{{ csrf_token() }}">
                <div class="adv-images-gallery-order">
                    {{ $key + 1 }}
                </div>
                <div class="adv-images-gallery-actions">
                    <span class="adv-images-gallery-edit icon voyager-edit" title="Редактировать"></span>
                    <span class="adv-images-gallery-remove icon voyager-x" title="Удалить"></span>
                </div>
                <div class="adv-images-gallery-image">
                    <img src="{{ $adv_image->getFullUrl() }}" data-file-id="{{ $adv_image->id }}" data-id="{{ $dataTypeContent->id }}">
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
</div>
@endif
<input @if($row->required == 1) required @endif type="file" name="{{ $row->field }}[]" multiple="multiple" accept="image/*">
