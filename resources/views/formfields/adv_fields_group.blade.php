<div class="adv-fields-group-wrapper">
    @php
        if(!$fields = json_decode($dataTypeContent->{$row->field})) {
            $fields = $options;
        }
    @endphp
    @foreach($fields->fields as $key_field => $field)
        @php
            $value = isset($field->value)? $field->value : "";
        @endphp
        @if($field->type === 'text')
            <div class="form-group">
                <label for="{{$key_field}}">{{$field->label}}</label>
                <input type="text" id="{{$key_field}}" class="form-control" name="{{$key_field}}" value="{{ $value }}">
            </div>
        @elseif($field->type === 'number')
            <div class="form-group">
                <label for="{{$key_field}}">{{$field->label}}</label>
                <input type="number" id="{{$key_field}}" class="form-control" name="{{$key_field}}" value="{{ $value }}">
            </div>
        @elseif($field->type === 'textarea')
            <div class="form-group">
                <label for="{{$key_field}}">{{$field->label}}</label>
                <textarea id="{{$key_field}}" class="form-control" name="{{$key_field}}">{{ $value }}</textarea>
            </div>
        @endif
    @endforeach
</div>