<div class="adv-inline-set-wrapper">
@php
if (isset($row->details->inline_set->source)) {
    $inlineSource = $inline_set[$row->field]->toArray();
} else {
    $inlineSource = json_decode($dataTypeContent->{$row->field});
}
//dd($row, $dataType, $dataTypeContent, $inline_set);
//dd($row->details);
@endphp

@if(isset($row->details->inline_set->fields))

    @if ($inlineSource && count($inlineSource) > 0)
    <div class="adv-inline-set-list">
        @foreach($inlineSource as $source_id => $source)
        <div class="adv-inline-set-item">
            <div class="adv-inline-set-handle">
                <span></span><span></span><span></span>
            </div>
            <div class="adv-inline-set-holder">
                <input type="hidden" name="{{$row->field}}_id[]" value="{{ $source['id'] }}">
                @foreach($row->details->inline_set->fields as $key_field => $field)
                    {{-- FIELD TYPES --}}
                    @if($field->type === 'text')
                        <div class="form-group">
                            <label for="{{$row->field}}_{{$key_field}}_{{$source_id}}">{{$field->label}}</label>
                            <input type="text" id="{{$row->field}}_{{$key_field}}_{{$source_id}}" class="form-control" name="{{$row->field}}_{{$key_field}}[]" value="{{ $source[$key_field] }}">
                        </div>
                    @elseif($field->type === 'textarea')
                        <div class="form-group">
                            <label for="{{$row->field}}_{{$key_field}}_{{$source_id}}">{{$field->label}}</label>
                            <textarea id="{{$row->field}}_{{$key_field}}_{{$source_id}}" class="form-control" name="{{$row->field}}_{{$key_field}}[]">{{ $source[$key_field] }}</textarea>
                        </div>
                    @endif
                    {{-- FIELD TYPES END --}}
                @endforeach
            </div>
            <div class="adv-inline-set-delete">
                <i class="voyager-x"></i>
            </div>
        </div>
        @endforeach
    </div>

    @else
        EMPTY SOURCE
    @endif

    @if ($row->details->inline_set->many)
    <div class="adv-inline-set-actions">
        <button type="submit" class="btn btn-success save">Add a new fields set</button>
    </div>
    @endif
@else
    NO JSON OPTIONS FOUND
@endif
</div>
