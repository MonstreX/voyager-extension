<div class="adv-inline-set-item columns-{{$columns}} {{ !$source? 'adv-inline-set-template' : '' }}" data-index="{{ $row_id }}" data-row-id="{{ $row_id }}">
    <div class="adv-inline-set-handle">
        <span></span><span></span><span></span>
    </div>
    <div class="adv-inline-set-holder">
        <div class="row">
            <input class="adv-inline-set-index" type="hidden" name="{{$row_field}}_id[]"
            @if (!$local_storage) value="{{ $source? $source['id'] : -1 }}" @else value="{{ $row_id }}" @endif
            >
            <input class="adv-inline-set-delete-input" type="hidden" name="{{$row_field}}_delete[]" value=false>
            @foreach($inline_fields as $key_field => $field)
                {{-- FIELD TYPES --}}
                <div class="form-group {{ isset($field->class)? $field->class : 'col-md-12' }}">
                    <label for="{{$row_field}}_{{$key_field}}_{{$row_id}}">{{$field->label}}</label>
                    @if($field->type === 'text')
                        <input type="text"
                               class="form-control"
                               id="{{$row_field}}_{{$key_field}}_{{$row_id?? '' }}"
                               name="{{$row_field}}_{{$key_field}}[]"
                               value="{{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}">
                    @elseif($field->type === 'textarea')
                        <textarea id="{{$row_field}}_{{$key_field}}_{{$row_id?? '' }}"
                                  class="form-control"
                                  name="{{$row_field}}_{{$key_field}}[]">{{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}</textarea>
                    @elseif($field->type === 'media')
                        <p>{{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}</p>
                        <input id="{{$row_field}}_{{$key_field}}_{{$row_id}}"
                            class="adv-inline-change-name"
                            name="{{$row_field}}_{{$key_field}}_{{$row_id?? '' }}"
                            type="file"
                            multiple="multiple"
                            accept="image/*">
                        <input type="hidden" name="{{$row_field}}_{{$key_field}}_media_{{$row_id?? '' }}"
                               value="{{ $source? (isset($source[$key_field])? $source[$key_field] : '' ): '' }}">
                    @endif
                </div>
                {{-- FIELD TYPES END --}}
            @endforeach
        </div>
    </div>
    <button type="button" class="adv-inline-set-delete">
        <i class="voyager-x"></i>
    </button>
</div>
