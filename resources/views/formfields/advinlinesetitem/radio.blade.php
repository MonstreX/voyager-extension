@if ($field->options)
@foreach($field->options as $value => $label)
    @php
    $default = isset($field->default) && $field->default === $value ? 'checked' : '';
    $checked = isset($source[$key_field]) && $source[$key_field] === $value ? 'checked' : (empty($source[$key_field])? $default : '');
    @endphp
    <div class="adv-inline-set-radio">
        <input class="adv-form-control"
               type="radio"
               id="{{$value}}_{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
               name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
               value="{{$value}}"
               {{ $checked }}
               >
        <label for="{{$value}}_{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}">{{$label}}</label>
    </div>
@endforeach
@endif
