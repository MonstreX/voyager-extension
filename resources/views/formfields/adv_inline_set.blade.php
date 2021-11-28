@php
$localStorage = !isset($row->details->inline_set->source);
$inlineSource = isset($inline_set)? $inline_set[$row->field] : [];
@endphp

<div class="adv-inline-set-wrapper">
    @if(isset($row->details->inline_set->fields))

        <div id="{{ $row->field }}_list" class="adv-inline-set-list"
            data-field="{{ $row->field }}"
            data-deleted=""
            data-many="{{$row->details->inline_set->many}}"
            data-local-storage="{{ $localStorage }}">

            <input class="adv-inline-set-row-ids" type="hidden" name="{{ $row->field }}_row_ids"
                   value="{{ implode(',', collect($inlineSource)->map(function ($item, $key) { return $item['row_id']; })->toArray()) }}">
            <input class="adv-inline-set-ids" type="hidden" name="{{ $row->field }}_ids"
                   value="{{ implode(',', collect($inlineSource)->map(function ($item, $key) { return isset($item['id'])? $item['id'] : 0; })->toArray()) }}">
            <input class="adv-inline-set-deleted-ids" type="hidden" name="{{ $row->field }}_deleted_ids" value="">
            <input class="adv-inline-set-deleted-media" type="hidden" name="{{ $row->field }}_deleted_media" value="">

            @if ($inlineSource && count($inlineSource) > 0)
                @foreach($inlineSource as $key => $source)
                    @include('voyager-extension::formfields.adv_inline_set_item', [
                        'columns' => isset($row->details->inline_set->columns)? $row->details->inline_set->columns : 1,
                        'id' => isset($source['id'])? $source['id'] : 0,
                        'row_id' => $source['row_id'],
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
            'id' => 0,
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
