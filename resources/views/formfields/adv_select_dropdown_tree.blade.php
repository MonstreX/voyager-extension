@if(isset($options->relationship))
    {{-- If this is a relationship and the method does not exist, show a warning message --}}
    @if( !method_exists( $dataType->model_name, Str::camel($options->relationship->field) ) )
        <p class="label label-warning">
            <i class="voyager-warning"></i>
            {{ __('voyager::form.field_select_dd_relationship', ['method' => Str::camel($options->relationship->field).'()', 'class' => $dataType->model_name]) }}
        </p>
    @endif

    @if( method_exists( $dataType->model_name, Str::camel($options->relationship->field) ) )
        @php
            $selected_value = $dataTypeContent->{$options->relationship->field}?->{$options->relationship->key};
            $treeOptions = build_flat_from_tree(flat_to_tree(app($row->details->relationship->model)->get()->toArray()));
        @endphp

        <select class="form-control select2" name="{{ $options->relationship->ref_field }}">
            <optgroup label="{{ __('voyager::database.relationship.relationship') }}">
                <option value="0"  @if(empty($selected_value)) {{ 'selected="selected"' }}@endif> ----- </option>
                @foreach($treeOptions as $option)
                    <option value="{{ $option['id'] }}"  @if($selected_value == $option['id']){{ 'selected="selected"' }}@endif>
                        @if($option['level'] > 0) {{ str_repeat("--", $option['level']) }} @endif {{ $option[$options->relationship->label] }}
                    </option>
                @endforeach
            </optgroup>
        </select>
    @endif
@endif
