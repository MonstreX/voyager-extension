@if ($related_options = isset($row->details->related)? $row->details->related : null)
<div class="adv-related-wrapper">
    <div id="adv-related-list-{{$row->field}}" class="adv-related-list">

    </div>
    <div class="adv-related-add-holder">
        <div class="adv-related-add-form">
            <div class="adv-related-add-autocomplete">
                <input class="related-autocomplete"
                       id="adv-related-autocomplete-{{$row->field}}"
                       name="adv-related-autocomplete-{{$row->field}}"
                       type="text"
                       data-field="{{$row->field}}"
                       data-url="{{ route('voyager.ext-records-get') }}"
                       data-slug="{{ $related_options->source }}"
                       data-search="{{ $related_options->search }}"
                       data-display="{{ $related_options->display }}"
                       data-fields="{{ implode(',', $related_options->fields) }}"
                        >
                <button data-field="{{$row->field}}" type="button" disabled class="btn btn-success add-related"><i class='voyager-list-add'></i></button>
            </div>
        </div>
    </div>
    <input id="{{$row->field}}" name="{{$row->field}}" type="hidden" value="{{ $dataTypeContent->{$row->field} }}">
</div>
@else
<div class="adv-related-no-options">No JSON options for the Relaterd Field {{ $row->field }} found</div>
@endif

