@if(isset($options->relationship))

    {{-- If this is a relationship and the method does not exist, show a warning message --}}
    @if( !method_exists( $dataType->model_name, Str::camel($row->field) ) )
        <p class="label label-warning"><i class="voyager-warning"></i> {{ __('voyager::form.field_select_dd_relationship', ['method' => Str::camel($row->field).'()', 'class' => $dataType->model_name]) }}</p>
    @endif

    @if( method_exists( $dataType->model_name, Str::camel($row->field) ) )

        @if(isset($dataTypeContent->{$row->field}) && !is_null(old($row->field, $dataTypeContent->{$row->field})))
            <?php $selected_value = old($row->field, $dataTypeContent->{$row->field}); ?>
        @else
            <?php $selected_value = old($row->field); ?>
        @endif

        <select class="form-control select2" name="{{ $row->field }}">
            <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : null; ?>

            @if(isset($options->options))
                <optgroup label="{{ __('voyager::generic.custom') }}">
                @foreach($options->options as $key => $option)
                    <option value="{{ ($key == '_empty_' ? '' : $key) }}" @if($default == $key && $selected_value === NULL){{ 'selected="selected"' }}@endif @if((string)$selected_value == (string)$key){{ 'selected="selected"' }}@endif>{{ $option }}</option>
                @endforeach
                </optgroup>
            @endif
            {{-- Populate all options from relationship --}}
            <?php
            $relationshipListMethod = Str::camel($row->field) . 'List';
            if (method_exists($dataTypeContent, $relationshipListMethod)) {
                $relationshipOptions = $dataTypeContent->$relationshipListMethod();
            } else {
                $relationshipClass = $dataTypeContent->{Str::camel($row->field)}()->getRelated();
                if (isset($options->relationship->where)) {
                    $relationshipOptions = $relationshipClass::where(
                        $options->relationship->where[0],
                        $options->relationship->where[1]
                    )->get();
                } else {
                    $relationshipOptions = $relationshipClass::all();
                }
            }

            // Try to get default value for the relationship
            // when default is a callable function (ClassName@methodName)
            if ($default != null) {
                $comps = explode('@', $default);
                if (count($comps) == 2 && method_exists($comps[0], $comps[1])) {
                    $default = call_user_func([$comps[0], $comps[1]]);
                }
            }

            $treeOptions = build_flat_from_tree(flat_to_tree($relationshipOptions->toArray()));

            ?>

            <optgroup label="{{ __('voyager::database.relationship.relationship') }}">
                <option value="0"  @if(empty($selected_value)) {{ 'selected="selected"' }}@endif> ----- </option>
                @foreach($treeOptions as $option)
                <option value="{{ $option['id'] }}"  @if($selected_value == $option['id']){{ 'selected="selected"' }}@endif>
                    @if($option['level'] > 0) {{ str_repeat("--", $option['level']) }} @endif {{ $option[$options->relationship->label] }}
                </option>
            @endforeach
            </optgroup>
        </select>
    @else
        <select class="form-control select2" name="{{ $row->field }}"></select>
    @endif
@else
    <?php $selected_value = (isset($dataTypeContent->{$row->field}) && !is_null(old($row->field, $dataTypeContent->{$row->field}))) ? old($row->field, $dataTypeContent->{$row->field}) : old($row->field); ?>
    <select class="form-control select2" name="{{ $row->field }}">
        <?php $default = (isset($options->default) && !isset($dataTypeContent->{$row->field})) ? $options->default : null; ?>
        @if(isset($options->options))
            @foreach($options->options as $key => $option)
                <option value="{{ $key }}" @if($default == $key && $selected_value === NULL){{ 'selected="selected"' }}@endif @if($selected_value == $key){{ 'selected="selected"' }}@endif>{{ $option }}</option>
            @endforeach
        @endif
    </select>
@endif
