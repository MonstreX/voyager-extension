<span class="tt-checkbox-holder">
    <input type="checkbox" data-id="field_browse_{{ $data['field'] }}"
           id="field_{{ $action }}_{{ $data['field'] }}"
           name="field_{{ $action }}_{{ $data['field'] }}"
           class="tiny-toggle"
           @if(isset($dataRow->{$action}) && $dataRow->{$action})
           checked="checked"
           @elseif($data['key'] == 'PRI')
           @elseif($data['type'] == 'timestamp' && $data['field'] == 'updated_at')
           @elseif(!isset($dataRow->{$action}))
           checked="checked"
           @endif
           data-tt-type="dot"
           data-tt-size="tiny"
           data-tt-palette="{{ $color }}"
           data-tt-label-check="{{ __('voyager::generic.' . $action) }}"
           data-tt-label-uncheck="{{ __('voyager::generic.' . $action) }}">
</span>
