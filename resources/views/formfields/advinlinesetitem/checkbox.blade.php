@php
    $default = isset($field->default) && $field->default === 'on'? 1 : 0;
    $checked =  $source? (isset($source[$key_field]) && (int)$source[$key_field] === 1 ? 1 : 0 ) : $default;
    $checked = !$row_id? $default : $checked;
@endphp
<input type="checkbox"
       id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
       name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
       data-field-type="{{$field->type}}"
       class="adv-form-control form-control tiny-toggle"
       data-default="{{$default}}"
       data-tt-size="large"
       data-tt-label-check="{{ $field->on? $field->on : 'on' }}"
       data-tt-label-uncheck="{{ $field->on? $field->off : 'off' }}"
       @if($checked) checked @endif
       @include('voyager-extension::formfields.advinlinesetitem.attr')>
