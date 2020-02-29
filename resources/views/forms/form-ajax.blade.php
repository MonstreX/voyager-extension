<form class="w-modal-form">
    <input type="hidden" name="model" value="{{ $model['model'] }}">
    <input type="hidden" name="id" value="{{ $model['id'] }}">
    <input type="hidden" name="field" value="{{ $model['field'] }}">
    <input type="hidden" name="media_file_id" value="{{ $model['media_file_id'] }}">

    @if($dataRow->type === 'adv_media_files')
        <div class="w-modal-form-group">
            <label class="w-modal-label" for="title">@lang('voyager-extension::bread.adv_image.title')</label>
            <input type="text" class="w-modal-form-control" name="title"
                   placeholder="@lang('voyager-extension::bread.adv_image.title_placeholder')"
                   value="{{ $file->getCustomProperty('title') }}">
        </div>
        <div class="w-modal-form-group">
            <label class="w-modal-label" for="alt">@lang('voyager-extension::bread.adv_image.alt')</label>
            <input type="text" class="w-modal-form-control" name="alt"
                   placeholder="@lang('voyager-extension::bread.adv_image.alt_placeholder')"
                   value="{{ $file->getCustomProperty('alt') }}">
        </div>
    @endif

    @if(isset($dataRow->details->extra_fields))
        @foreach($dataRow->details->extra_fields as $key => $field)
            @if($field->type === 'text')
            <div class="w-modal-form-group @if(isset($field->class)) {{ $field->class }} @endif">
                <label class="w-modal-label" for="{{ $key }}">{{ $field->title }}</label>
                <input type="text" class="w-modal-form-control" name="{{ $key }}"
                       placeholder="{{ $field->title }}"
                       value="{{ $file->getCustomProperty($key) }}">
            </div>
            @endif
            @if($field->type === 'textarea')
                <div class="w-modal-form-group @if(isset($field->class)) {{ $field->class }} @endif">
                    <label class="w-modal-label" for="{{ $key }}">{{ $field->title }}</label>
                    <textarea class="w-modal-form-control" name="{{ $key }}" rows="5">{{ $file->getCustomProperty($key) }}</textarea>
                </div>
            @endif
            @if($field->type === 'codemirror')
                <div class="w-modal-form-group @if(isset($field->class)) {{ $field->class }} @endif">
                    <label class="w-modal-label" for="acebox-{{ $key }}">{{ $field->title }}</label>
                    <textarea id="codemirror-{{ $key }}" name="{{ $key }}" data-theme="neat" data-mode="text/html" class="codemirror_editor">{{ $file->getCustomProperty($key) }}</textarea>
                </div>
            @endif
        @endforeach
    @endif

</form>
