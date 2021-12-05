@php
$datetime = isset($source[$key_field])? $source[$key_field] : null;
@endphp
<input type="date"
       class="adv-form-control form-control"
       id="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
       name="{{$row_field}}_{{$key_field}}_{{$row_id?? '%id%'}}"
       placeholder="{{ $datetime }}"
       value="{{ $datetime? \Carbon\Carbon::parse($datetime)->format('Y-m-d') : null }}"
       @include('voyager-extension::formfields.advinlinesetitem.attr')>
