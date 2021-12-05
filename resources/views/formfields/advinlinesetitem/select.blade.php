@if ($field->options)
<select class="form-control adv-form-control select2"
        name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
        id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
        data-field-type="{{$field->type}}"
        @include('voyager-extension::formfields.advinlinesetitem.attr')>
    @foreach($field->options as $value => $label)
        @php
        $default = isset($field->default) && $field->default === $value ? 'selected' : '';
        $selected = isset($source[$key_field]) && $source[$key_field] === $value ? 'selected' : (empty($source[$key_field])? $default : '');
        @endphp
        <option value="{{ $value }}" {{ $selected }}>{{ $label }}</option>
    @endforeach
@endif
</select>
