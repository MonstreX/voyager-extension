<div class="field-helper">
    @lang('voyager-extension::bread.adv_image.help', ['gallery' => $row->field])
</div>

@if($adv_image = $dataTypeContent->getFirstMedia($row->field))
<div class="adv-image-wrapper">
    <div class="adv-image"
         data-type="{{ $row->type }}"
         data-model="{{ $dataType->model_name }}"
         data-slug="{{ $dataType->slug }}"
         data-field-name="{{ $row->field }}"
         data-file-name="{{ $adv_image->file_name }}"
         data-id="{{ $dataTypeContent->id }}"
         data-image-id="{{ $adv_image->id }}">

        <img class="" src="{{ $adv_image->getFullUrl() }}">
        <div class="adv-image-fields">
            <div class="adv-image-field adv-image-file">
                <span class="adv-image-file-title">@lang('voyager-extension::bread.adv_image.file')</span>
                <span class="adv-image-file-name">{{ Str::limit($adv_image->file_name, 30, ' (...)') }}</span>
                <span class="adv-image-file-size @if($adv_image->size > 100000) large @endif">{{ $adv_image->human_readable_size }}</span>
            </div>
            <div class="adv-image-field">
                <label class="control-label" for="{{ $row->field }}_title">@lang('voyager-extension::bread.adv_image.title')</label>
                <input type="text" class="form-control" name="{{ $row->field }}_title"
                       placeholder="Image Title"
                       value="{{ $adv_image->getCustomProperty('title') }}">
            </div>
            <div class="adv-image-field">
                <label class="control-label" for="{{ $row->field }}_alt">@lang('voyager-extension::bread.adv_image.alt')</label>
                <input type="text" class="form-control" name="{{ $row->field }}_alt"
                       placeholder="Image Alt"
                       value="{{ $adv_image->getCustomProperty('alt') }}">
            </div>
        </div>
        <a href="javascript:;" title="Удалить" class="btn btn-sm btn-danger single-adv-image-remove">
            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">@lang('voyager-extension::bread.adv_image.delete')</span>
        </a>
    </div>
</div>
@endif
<input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" name="{{ $row->field }}" accept="image/*">

