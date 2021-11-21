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

    <div id="{{ $row->field }}_list" class="adv-inline-set-list" data-many="{{$row->details->inline_set->many}}">
    @if ($inlineSource && count($inlineSource) > 0)
        @foreach($inlineSource as $index => $source)
            @include('voyager-extension::formfields.adv_inline_set_item', [
                'index' => $index,
                'source' => $source,
                'row_field' => $row->field,
                'inline_fields' => $row->details->inline_set->fields,
            ])
        @endforeach
    @endif
    </div>

    @include('voyager-extension::formfields.adv_inline_set_item', [
        'index' => null,
        'source' => null,
        'row_field' => $row->field,
        'inline_fields' => $row->details->inline_set->fields,
    ])

    @if ($row->details->inline_set->many || !$inlineSource)
    <div class="adv-inline-set-actions">
        <button type="button" class="btn btn-success add-inline-set">Add a new fields set</button>
    </div>
    @endif
@else
    NO JSON OPTIONS FOUND
@endif
</div>
