<div class="adv-inline-set-item columns-{{$columns}} {{ !$source? 'adv-inline-set-template' : '' }}" data-index="{{ $index }}">
    <div class="adv-inline-set-handle">
        <span></span><span></span><span></span>
    </div>
    <div class="adv-inline-set-holder">
        <div class="row">
            <input class="adv-inline-set-index" type="hidden" name="{{$row_field}}_id[]"
            @if (!$local_storage) value="{{ $source? $source['id'] : -1 }}" @else value="{{ $index }}" @endif
            >
            <input class="adv-inline-set-delete-input" type="hidden" name="{{$row_field}}_delete[]" value=false>
            @foreach($inline_fields as $key_field => $field)
                {{-- FIELD TYPES --}}
                <div class="form-group {{ isset($field->class)? $field->class : 'col-md-12' }} }}">
                @if($field->type === 'text')
                    <label for="{{$row_field}}_{{$key_field}}_{{$index}}">{{$field->label}}</label>
                    <input type="text"
                           class="form-control"
                           id="{{$row_field}}_{{$key_field}}_{{$index?? '' }}"
                           name="{{$row_field}}_{{$key_field}}[]"
                           value="{{ $source? $source[$key_field] : '' }}">
                @elseif($field->type === 'textarea')
                    <label for="{{$row_field}}_{{$key_field}}_{{$index}}">{{$field->label}}</label>
                    <textarea id="{{$row_field}}_{{$key_field}}_{{$index?? '' }}"
                              class="form-control"
                              name="{{$row_field}}_{{$key_field}}[]">{{ $source? $source[$key_field] : '' }}</textarea>
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
