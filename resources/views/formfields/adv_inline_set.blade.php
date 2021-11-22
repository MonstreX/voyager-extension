@php
$localStorage = !isset($row->details->inline_set->source);
if (isset($inline_set)) {
    $inlineSource = !$localStorage? $inline_set[$row->field]->toArray() : json_decode($dataTypeContent->{$row->field}, true);
} else {
    $inlineSource = [];
}
@endphp

<div class="adv-inline-set-wrapper">
    @if(isset($row->details->inline_set->fields))

        <div id="{{ $row->field }}_list" class="adv-inline-set-list"
             data-many="{{$row->details->inline_set->many}}"
             data-local-storage="{{ $localStorage }}">
            @if ($inlineSource && count($inlineSource) > 0)
                @foreach($inlineSource as $key => $source)
                    @include('voyager-extension::formfields.adv_inline_set_item', [
                        'columns' => isset($row->details->inline_set->columns)? $row->details->inline_set->columns : 1,
                        'row_id' => isset($source['id'])? $source['id'] : 0,
                        'source' => $source,
                        'local_storage' => $localStorage,
                        'row_field' => $row->field,
                        'inline_fields' => $row->details->inline_set->fields,
                    ])
                @endforeach
            @endif
        </div>

        @include('voyager-extension::formfields.adv_inline_set_item', [
            'columns' => isset($row->details->inline_set->columns)? $row->details->inline_set->columns : 1,
            'row_id' => null,
            'source' => null,
            'local_storage' => $localStorage,
            'row_field' => $row->field,
            'inline_fields' => $row->details->inline_set->fields,
        ])

        @if ($row->details->inline_set->many || !$inlineSource)
            <div class="adv-inline-set-actions">
                <button type="button" class="btn btn-success add-inline-set">
                    {{ __('voyager-extension::bread.add_new_inline_set') }}
                </button>
            </div>
        @endif
    @else
        {{ __('voyager-extension::bread.no_inline_set_data') }}
    @endif
</div>
