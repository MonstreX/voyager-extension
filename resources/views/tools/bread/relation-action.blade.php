<input type="checkbox"
       name="field_{{ $action }}_{{ $relationship['field'] }}"
       class="tiny-toggle"
       @if(isset($relationship->{$action}) && $relationship->{$action}) checked="checked" @elseif(!isset($relationship->{$action})) checked="checked" @endif
       data-tt-type="dot"
       data-tt-size="tiny"
       data-tt-palette="{{ $color }}"
       data-tt-label-check="{{ __('voyager::database.relationship.' . $action) }}"
       data-tt-label-uncheck="{{ __('voyager::database.relationship.' . $action) }}">
