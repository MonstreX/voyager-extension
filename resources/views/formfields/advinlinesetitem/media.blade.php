@if($mediaFiles = $source && isset($source[$key_field])? $dataTypeContent->getMedia($source[$key_field]) : null)
    @if(count($mediaFiles) > 0)
    <div id="list_{{$row_field}}_{{$key_field}}_{{$row_id}}" class="adv-inline-set-media-list">
    @foreach($mediaFiles as $mediaFile)
        <div class="adv-inline-set-media-item columns-{{ 12/$columns}}"
             data-media-id="{{ $mediaFile->id }}"
             data-remove-delay="{{ isset($field->remove_delay)? (int)$field->remove_delay : 2500 }}">
            <div class="adv-inline-set-media-delete">
                <i class="voyager-x"></i>
            </div>
            <div class="adv-inline-set-media-removing">
                <div class="removing-bar"></div>
                <span>Click to cancel removing</span>
            </div>
            @if(explode('/', $mediaFile->mime_type)[0] === 'image')
            <img src="{{ $mediaFile->getFullUrl() }}">
            @else
            <img class="file-type" src="{{ voyager_extension_asset('icons/files/'.explode('/', $mediaFile->mime_type)[1].'.svg') }}">
            @endif
        </div>
    @endforeach
    </div>
    @endif
@endif

<input id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
       class="form-control media-library"
       data-field-type="{{$field->type}}"
       name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}[]"
       type="file"
       multiple="multiple"
       accept="{{isset($field->accept)? $field->accept : "image/*" }}"
       @include('voyager-extension::formfields.advinlinesetitem.attr')
       >
