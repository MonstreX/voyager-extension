<form class="w-modal-form inline-group-form" data-slug="{{ $slug }}" data-id="{{ $id }}" $data-field="{{ $field }}">
@foreach($fields as $key => $field)
    @if($field->type === 'text')
    <div class="w-modal-form-group @if(isset($field->class)) {{ $field->class }} @endif">
        <label class="w-modal-label" for="{{ $key }}">{{ $field->label }}</label>
        <input type="text" data-label="{{ $field->label }}" class="w-modal-form-control" name="{{ $key }}"
               placeholder="{{ $field->label }}"
               value="{{ isset($field->value)? $field->value : '' }}">
    </div>
    @endif
@endforeach
</form>

