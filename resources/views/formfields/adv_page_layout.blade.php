<div class="adv-page-layout-wrapper">
    <input id="{{ $row->field }}" name="{{ $row->field }}" type="hidden" value='{!! $dataTypeContent->{$row->field} !!}'>
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
                    <a href="javascript:;" title="@lang('voyager-extension::bread.adv_page_layout.remove_section_title')" class="btn btn-sm btn-danger remove-layout-section">
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
                <a href="javascript:;" title="@lang('voyager-extension::bread.adv_page_layout.remove_section_title')" class="btn btn-sm btn-danger remove-layout-section">
                    <i class="voyager-x"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
         @isset($options->layout_fields)
            <div class="form-group select-holder {{ $options->style_classes ?? 'col-md-4' }}">
            <span class="select-label">@lang('voyager-extension::bread.adv_page_layout.field_title')</span>
            <select class="form-control select2" name="fields_list" data-type="Field" data-icon="voyager-receipt">
                <optgroup label="@lang('voyager-extension::bread.adv_page_layout.available_content_fields')">
                    @foreach($options->layout_fields as $key => $field)
                        <option value="{{ $key }}" >{{ $field }} ({{ $key }})</option>
                    @endforeach
                </optgroup>
            </select>
            <a href="javascript:;" title="@lang('voyager-extension::bread.adv_page_layout.add_field_section')" class="btn btn-sm btn-success add-layout-section">
                <i class="voyager-plus"></i>
            </a>
        </div>
        @endisset

        @isset($blocks)
            <div class="form-group select-holder {{ $options->style_classes ?? 'col-md-4' }}">
            <span class="select-label">@lang('voyager-extension::bread.adv_page_layout.block_title')</span>
            <select class="form-control select2" name="block_list" data-type="Block" data-icon="voyager-puzzle">
                <optgroup label="@lang('voyager-extension::bread.adv_page_layout.available_blocks')">
                @foreach($blocks as $key => $block)
                    <option value="{{ $block['key'] }}" >{{ $block['title'] }} ({{ $block['key'] }})</option>
                @endforeach
                </optgroup>
            </select>
            <a href="javascript:;" title="@lang('voyager-extension::bread.adv_page_layout.add_block_section')" class="btn btn-sm btn-success add-layout-section">
                <i class="voyager-plus"></i>
            </a>
        </div>
        @endisset

        @isset($forms)
            <div class="form-group select-holder {{ $options->style_classes ?? 'col-md-4' }}">
            <span class="select-label">@lang('voyager-extension::bread.adv_page_layout.form_title')</span>
            <select class="form-control select2" name="form_list" data-type="Form" data-icon="voyager-window-list">
                <optgroup label="@lang('voyager-extension::bread.adv_page_layout.available_forms')">
                        @foreach($forms as $key => $form)
                            <option value="{{ $form['key'] }}" >{{ $form['title'] }} ({{ $form['key'] }})</option>
                        @endforeach
                </optgroup>
            </select>
            <a href="javascript:;" title="@lang('voyager-extension::bread.adv_page_layout.add_form_section')" class="btn btn-sm btn-success add-layout-section">
                <i class="voyager-plus"></i>
            </a>
        </div>
        @endisset
    </div>
</div>
