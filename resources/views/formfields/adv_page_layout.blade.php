<div class="adv-page-layout-wrapper">
    <input id="{{ $row->field }}" name="{{ $row->field }}" type="hidden" value="">

    <div id="layout_{{ $row->field }}" class="layout-sections-list">

    @if($fields = json_decode($dataTypeContent->{$row->field}))
        @foreach($fields as $field)
        <div class="layout-section layout-{{ $field->type }}" data-key="{{ $field->key }}">
            <div class="layout-section-holder">
                <div class="layout-section-icon">
                    <i class="{{ $field->icon }}"></i>
                </div>
                <div class="layout-section-title">
                    <span class="layout-section-title-type">{{ $field->type }}</span> <span class="layout-section-title-name">{{ $field->title }}</span>
                </div>
                <div class="layout-section-actions">
                    <a href="javascript:;" title="Remove section" class="btn btn-sm btn-danger remove-layout-section">
                        <i class="voyager-x"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    </div>

    <div class="layout-section layout-template" style="display:none;">
        <div class="layout-section-holder">
            <div class="layout-section-icon">
                <i class="layout-icon"></i>
            </div>
            <div class="layout-section-title">
                <span class="layout-section-title-type"></span> <span class="layout-section-title-name"></span>
            </div>
            <div class="layout-section-actions">
                <a href="javascript:;" title="Remove section" class="btn btn-sm btn-danger remove-layout-section">
                    <i class="voyager-x"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group  col-md-4 select-holder">
            <span class="select-label">Field:</span>
            <select class="form-control select2" name="fields_list" data-type="Field" data-icon="voyager-receipt">
                <optgroup label="Available content fields">
                    @foreach($options->layout_fields as $key => $field)
                        <option value="{{ $key }}" >{{ $field }} ({{ $key }})</option>
                    @endforeach
                </optgroup>
            </select>
            <a href="javascript:;" title="Add field section" class="btn btn-sm btn-success add-layout-section">
                <i class="voyager-plus"></i>
            </a>
        </div>

        <div class="form-group  col-md-4 select-holder">
            <span class="select-label">Block:</span>
            <select class="form-control select2" name="block_list" data-type="Block" data-icon="voyager-puzzle">
                <optgroup label="Available blocks">
                @foreach($blocks as $key => $block)
                    <option value="{{ $block['key'] }}" >{{ $block['title'] }} ({{ $block['key'] }})</option>
                @endforeach
                </optgroup>
            </select>
            <a href="javascript:;" title="Add block section" class="btn btn-sm btn-success add-layout-section">
                <i class="voyager-plus"></i>
            </a>
        </div>

        <div class="form-group  col-md-4 select-holder">
            <span class="select-label">Form:</span>
            <select class="form-control select2" name="form_list" data-type="Form" data-icon="voyager-window-list">
                <optgroup label="Available forms">
                    @foreach($forms as $key => $form)
                        <option value="{{ $form['key'] }}" >{{ $form['title'] }} ({{ $form['key'] }})</option>
                    @endforeach
                </optgroup>
            </select>
            <a href="javascript:;" title="Add form section" class="btn btn-sm btn-success add-layout-section">
                <i class="voyager-plus"></i>
            </a>
        </div>

    </div>

</div>