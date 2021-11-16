@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(isset($dataType->order_column) && isset($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach($actions as $action)
            @if (method_exists($action, 'massAction'))
                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
            @endif
        @endforeach

        @php
            $model_filters = [];
            foreach($dataType->browseRows as $row) {
                if(isset($row->details->browse_filter)) {
                    $model_filters[] = [
                        'filter_items' => build_flat_from_tree(flat_to_tree(app($row->details->model)->get()->toArray())),
                        'filter_title' => $row->display_name,
                        'filter_column' => $row->details->column,
                        'filter_key' => $row->details->key,
                        'filter_label' => $row->details->label,
                    ];
                }
            }
        @endphp

        @if(count($model_filters) > 0)
            <div class="browse-filters-holder" data-url="{{ Request::url() }}">
                @foreach($model_filters as $key => $filter)
                    <span class="filter-selector">
                <label for="filter-selector-{{ $key }}">{{ $filter['filter_title'] }}:</label>
                <select id="filter-selector-{{ $key }}" name="filter-selector[]" data-column="{{ $filter['filter_column'] }}" class="filter-select select2">
                    <option value="">---</option>

                    @php
                        $val = null;
                        // $filters - comes from the index controller
                        if ($filters) {
                            foreach ($filters['field'] as $idx => $field) {
                                $val = $field === $filter['filter_column']? $val = $filters['value'][$idx] : $val;
                            }
                        }
                    @endphp

                    @foreach($filter['filter_items'] as $key2 => $item)
                    <option value="{{ $item['id'] }}" @if ($val && $item['id'] == $val) selected @endif>
                        @if($item['level'] > 0) {{ str_repeat("--", $item['level']) }} @endif {{ $item[$filter['filter_label']] }}
                    </option>
                    @endforeach

                </select>
            </span>
                @endforeach
            </div>
        @endif

        @include('voyager::multilingual.language-selector')
    </div>
@stop

@php
    $extra_details = json_decode($dataType->extra_details);
    // Set specified columns order
    $dataType->browseRows = $dataType->browseRows->sortBy(function ($row, $key) use ($extra_details) {
        if (isset($extra_details->browse_order)) {
            return get_index_by_name($extra_details->browse_order, $row->field);
        } else {
            return isset($row->details->browse_order)? $row->details->browse_order : 0;
        }
    });

    // Correct Index Column for sorting in the table
    $orderColumn[0][0] = $dataType->browseRows->pluck('field')->search($orderBy) + ($showCheckboxColumn ? 1 : 0);
@endphp

@section('content')
    <div class="page-content browse container-fluid vext-browse">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">

                                <div id="search-input">
                                    <div class="col-2">
                                        <select id="search_key" name="key">
                                            @foreach($searchNames as $key => $name)
                                            <option value="{{ $key }}" @if($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select id="filter" name="filter">
                                            <option value="contains" @if($search->filter == "contains") selected @endif>contains</option>
                                            <option value="equals" @if($search->filter == "equals") selected @endif>=</option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}" name="s" value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                @if (Request::has('sort_order') && Request::has('order_by'))
                                    <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                    <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                @endif
                            </form>
                        @endif
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        @if($showCheckboxColumn)
                                            <th>
                                                <input type="checkbox" class="select_all">
                                            </th>
                                        @endif

                                        @foreach($dataType->browseRows as $row)

                                        <th class="@if(isset($row->details->browse_align)){{ $row->details->browse_align }}@endif"
                                            @if(isset($row->details->browse_width)) style="width:{{ $row->details->browse_width }}"@endif>

                                            @if ($isServerSide)
                                                <a href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
                                            @endif

                                            @if(isset($row->details->browse_title))
                                                {{ $row->details->browse_title }}
                                            @else
                                                {{ $row->getTranslatedAttribute('display_name') }}
                                            @endif

                                            @if ($isServerSide)
                                                @if ($row->isCurrentSortField($orderBy))
                                                    @if ($sortOrder == 'asc')
                                                        <i class="voyager-angle-up pull-right"></i>
                                                    @else
                                                        <i class="voyager-angle-down pull-right"></i>
                                                    @endif
                                                @endif
                                                </a>
                                            @endif
                                        </th>
                                        @endforeach

                                        <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                    <tr data-record-id="{{$data->getKey()}}"
                                        data-slug="{{$dataType->slug}}"
                                        class="{{ isset($data->status) && (int)$data->status === 0? 'unpublished-record' : '' }} @if($dataType->server_side){{ $loop->index % 2 === 0? 'odd' : 'even' }}@endif">
                                        @if($showCheckboxColumn)
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                                            </td>
                                        @endif
                                        @foreach($dataType->browseRows as $row)
                                            @php
                                            if ($data->{$row->field.'_browse'}) {
                                                $data->{$row->field} = $data->{$row->field.'_browse'};
                                            }
                                            @endphp
                                            <td class="@if(isset($row->details->browse_align)){{ $row->details->browse_align }}@endif"
                                                @if(isset($row->details->browse_font_size)) style="font-size:{{ $row->details->browse_font_size }}"@endif>

                                                @if (isset($row->details->view))
                                                    @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field}, 'action' => 'browse', 'view' => 'browse', 'options' => $row->details])

                                                {{-- FIELD IMAGE TYPE --}}
                                                @elseif($row->type == 'image')
                                                    <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="height: 50px; width:auto">

                                                {{-- FIELD ADVANCED MEDIA IMAGE TYPE --}}
                                                @elseif($row->type == 'adv_image')
                                                    @if($adv_image = $data->getFirstMedia($row->field))
                                                        <img src="{{ $adv_image->getFullUrl() }}" style="height: {{ $row->details->browse_image_max_height?? '50px' }}; width:auto">
                                                    @endif

                                                {{-- FIELD ADVANCED MEDIA FILES TYPE --}}
                                                @elseif($row->type == 'adv_media_files')
                                                    @if($adv_media_files = $data->getMedia($row->field)->take(3))
                                                        @foreach($adv_media_files as $key => $adv_file)
                                                            @if(explode('/', $adv_file->mime_type)[0] === 'image')
                                                                <img src="{{ $adv_file->getFullUrl() }}" style="height: {{ $row->details->browse_image_max_height?? '50px' }}; width:auto" >
                                                            @else
                                                                <a href="{{ $adv_file->getFullUrl() }}">
                                                                    <img class="file-type" src="{{ voyager_extension_asset('icons/files/'.explode('/', $adv_file->mime_type)[1].'.svg') }}" style="height: {{ $row->details->browse_image_max_height?? '20px' }}; width:auto">
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                {{-- FIELD RELATION TYPE --}}
                                                @elseif($row->type == 'relationship')
                                                    @include('voyager::formfields.relationship', ['view' => 'browse','options' => $row->details])

                                                {{-- FIELD SELECT MULTIPLE TYPE --}}
                                                @elseif($row->type == 'select_multiple')
                                                    @if(property_exists($row->details, 'relationship'))
                                                        @foreach($data->{$row->field} as $item)
                                                            {{ $item->{$row->field} }}
                                                        @endforeach
                                                    @elseif(property_exists($row->details, 'options'))
                                                        @if (!empty(json_decode($data->{$row->field})))
                                                            @foreach(json_decode($data->{$row->field}) as $item)
                                                                @if (@$row->details->options->{$item})
                                                                    {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {{ __('voyager::generic.none') }}
                                                        @endif
                                                    @endif

                                                {{-- FIELD MULTIPLE CHECKBOX TYPE --}}
                                                @elseif($row->type == 'multiple_checkbox' && property_exists($row->details, 'options'))
                                                    @if (@count(json_decode($data->{$row->field})) > 0)
                                                        @foreach(json_decode($data->{$row->field}) as $item)
                                                            @if (@$row->details->options->{$item})
                                                                {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ __('voyager::generic.none') }}
                                                    @endif

                                                {{-- FIELD DROPDOWN SELECT TYPE --}}
                                                @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') && property_exists($row->details, 'options'))
                                                    {!! $row->details->options->{$data->{$row->field}} ?? '' !!}

                                                {{-- FIELD ADVANCED TREE DROPDOWN SELECT TYPE --}}
                                                @elseif(($row->type == 'adv_select_dropdown_tree'))
                                                    @if(!empty($data->{$row->field}))
                                                    <span class="browse-dropdown-title">
                                                        <span class="label label-info">
                                                        {{ $data->{$row->details->relationship->field}[$row->details->relationship->label] }}
                                                        </span>
                                                    </span>
                                                    @endif

                                                {{-- FIELD DATE OR TIMESTUMP TYPE --}}
                                                @elseif($row->type == 'date' || $row->type == 'timestamp')
                                                    @if ( property_exists($row->details, 'format') && !is_null($data->{$row->field}) )
                                                        {{ \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format) }}
                                                    @else
                                                        {{ $data->{$row->field} }}
                                                    @endif

                                                {{-- FIELD CHECKBOX TYPE --}}
                                                @elseif($row->type == 'checkbox')
                                                    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                                        @if(property_exists($row->details, 'browse_inline_checkbox') || property_exists($row->details, 'browse_inline_editor'))
                                                            <input type="checkbox" data-id="{{ $data->id }}" name="{{ $row->field }}" @if($data->{$row->field}) checked @endif class="tiny-toggle" data-tt-type="dot" data-tt-size="tiny">
                                                        @else
                                                            @if($data->{$row->field})
                                                            <span class="label label-info">{{ $row->details->on }}</span>
                                                            @else
                                                            <span class="label label-primary">{{ $row->details->off }}</span>
                                                            @endif
                                                        @endif
                                                    @else
                                                    {{ $data->{$row->field} }}
                                                    @endif

                                                {{-- FIELD COLOR TYPE --}}
                                                @elseif($row->type == 'color')
                                                    <span class="badge badge-md" style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field} }}</span>

                                                {{-- FIELD TEXT OR NUMBER TYPE --}}
                                                @elseif($row->type == 'text' || $row->type == 'number')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div class="text-field-holder">
                                                        @if(isset($row->details->browse_inline_editor))
                                                        <div class="browse-inline-editor">
                                                            <input class="browse-inline-input" data-id="{{ $data->id }}" @if($row->type == 'number') type="number" @else type="text" @endif name="{{$row->field}}" value="{{ $data->{$row->field} }}">
                                                            <button class="text-inline-save" type="button" title="@lang('voyager-extension::bread.inline_save')"><i class="voyager-check"></i></button>
                                                            <button class="text-inline-cancel" type="button" title="@lang('voyager-extension::bread.inline_cancel')"><i class="voyager-x"></i></button>
                                                        </div>
                                                        @endif
                                                        <div class="browse-text-holder">
                                                            @if(isset($row->details->url))
                                                            <a href="{{ route('voyager.'.$dataType->slug.'.'.$row->details->url, $data->{$data->getKeyName()}) }}">
                                                            @elseif(isset($row->details->route) && isset($row->details->route->name) && isset($row->details->route->param_field))
                                                            <a href="{{ route($row->details->route->name, $data->{$row->details->route->param_field}) }}">
                                                            @endif
                                                                <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>
                                                            @if(isset($row->details->url))
                                                            </a>
                                                            @endif
                                                            @if(isset($row->details->browse_inline_editor))
                                                            <button class="text-inline-edit" type="button" title="@lang('voyager-extension::bread.inline_edit')"><i class="voyager-edit"></i></button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                {{-- FIELD TEXT AREA TYPE --}}
                                                @elseif($row->type == 'text_area')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( $data->{$row->field} ) > 200 ? mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}</div>

                                                {{-- FIELD FILE TYPE --}}
                                                @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    @if(json_decode($data->{$row->field}) !== null)
                                                        @foreach(json_decode($data->{$row->field}) as $file)
                                                            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                                                {{ $file->original_name ?: '' }}
                                                            </a>
                                                            <br/>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}" target="_blank">
                                                            Download
                                                        </a>
                                                    @endif

                                                {{-- FIELD RICH TEXT TYPE --}}
                                                @elseif($row->type == 'rich_text_box')
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <div>{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ? mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200) . ' ...' : strip_tags($data->{$row->field}, '<b><i><u>') }}</div>

                                                {{-- FIELD COORDINATES TYPE --}}
                                                @elseif($row->type == 'coordinates')
                                                    @include('voyager::partials.coordinates-static-image')

                                                {{-- FIELD MULTIPLE IMAGES TYPE --}}
                                                @elseif($row->type == 'multiple_images')
                                                    @php $images = json_decode($data->{$row->field}); @endphp
                                                    @if($images)
                                                        @php $images = array_slice($images, 0, 3); @endphp
                                                        @foreach($images as $image)
                                                            <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif" style="width:50px">
                                                        @endforeach
                                                    @endif

                                                {{-- FIELD MEDIA PICKER TYPE --}}
                                                @elseif($row->type == 'media_picker')
                                                    @php
                                                        if (is_array($data->{$row->field})) {
                                                            $files = $data->{$row->field};
                                                        } else {
                                                            $files = json_decode($data->{$row->field});
                                                        }
                                                    @endphp
                                                    @if ($files)
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                            <img src="@if( !filter_var($file, FILTER_VALIDATE_URL)){{ Voyager::image( $file ) }}@else{{ $file }}@endif" style="width:50px">
                                                            @endforeach
                                                        @else
                                                            <ul>
                                                            @foreach (array_slice($files, 0, 3) as $file)
                                                                <li>{{ $file }}</li>
                                                            @endforeach
                                                            </ul>
                                                        @endif
                                                        @if (count($files) > 3)
                                                            {{ __('voyager::media.files_more', ['count' => (count($files) - 3)]) }}
                                                        @endif
                                                    @elseif (is_array($files) && count($files) == 0)
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @elseif ($data->{$row->field} != '')
                                                        @if (property_exists($row->details, 'show_as_images') && $row->details->show_as_images)
                                                            <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif" style="width:50px">
                                                        @else
                                                            {{ $data->{$row->field} }}
                                                        @endif
                                                    @else
                                                        {{ trans_choice('voyager::media.files', 0) }}
                                                    @endif

                                                {{-- FIELD GROUP TYPE --}}
                                                @elseif($row->type == 'adv_fields_group')
                                                    <div class="browse-group-fields">
                                                        @php
                                                            $group = json_decode($data->{$row->field});
                                                            if (!isset($group->fields) && isset($row->details->fields)) {
                                                                $fields = $row->details->fields;
                                                            } else {
                                                                $fields = $group->fields;
                                                            }
                                                        @endphp
                                                        @if(isset($fields))
                                                            @foreach($fields as $key => $field)
                                                                <span class="browse-group-field" data-key="{{$key}}">
                                                                @if(!empty($field->value))
                                                                    <i class="voyager-check"></i>
                                                                @else
                                                                    <i class="voyager-dot"></i>
                                                                @endif
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <i class="voyager-dot"></i><i class="voyager-dot"></i><i class="voyager-dot"></i>
                                                        @endif
                                                        @if(property_exists($row->details, 'browse_inline_editor'))
                                                            <button data-name="{{$row->field}}" class="group-inline-edit" type="button" title="@lang('voyager-extension::bread.inline_edit')"><i class="voyager-edit"></i></button>
                                                        @endif
                                                    </div>

                                                {{-- FIELD OTHER TYPES --}}
                                                @else
                                                    @include('voyager::multilingual.input-hidden-bread-browse')
                                                    <span>{{ $data->{$row->field} }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="no-sort no-click" id="bread-actions">
                                            @foreach($actions as $action)
                                                @if (!method_exists($action, 'massAction'))
                                                    @include('voyager::bread.partials.actions', ['action' => $action])
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search->value,
                                    'filter' => $search->filter,
                                    'key' => $search->key,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder,
                                    'showSoftDeleted' => $showSoftDeleted,
                                ])
                                ->onEachSide(5)
                                ->links('voyager-extension::bread.partials.pagination') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
<link rel="stylesheet" href="{{ voyager_extension_asset('js/tinytoggle/css/tinytoggle.min.css') }}">
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
    <script src="{{ voyager_extension_asset('js/tinytoggle/jquery.tinytoggle.min.js') }}"></script>
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif

    <script>
        $(document).ready(function () {


            // Change Filter Selection
            $('.filter-select').on('change', function () {

                let url = $('.browse-filters-holder').data('url');
                let filter_params = '';

                let i = 0;
                $('.filter-select').each(function(index, elem) {
                    console.log($(elem).val());
                    if ($(elem).val()) {
                        filter_params = filter_params + (i > 0? '&' : '') + `field[${i}]=${$(elem).data('column')}&value[${i}]=${$(elem).val()}`;
                        i++;
                    }
                });

                if (filter_params.length === 0) {
                    window.location.replace(`${url}?reset_filters`);
                } else {
                    window.location.replace(`${url}?${filter_params}`);
                }
            });


            // Add some new functionality (hidden when we have not selected records) and change ID for using with our own handler
            $('#bulk_delete_btn').addClass('hidden').prop('id','vext_bulk_delete_btn');

            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => $orderColumn,
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                    }else{
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                    }

                    $('#redir')[0].click();
                })
            })
        @endif

        $('input[name="row_id"]').on('change', function () {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });

            if(ids.length > 0) {
                $('#vext_bulk_delete_btn').removeClass('hidden');
            } else {
                $('#vext_bulk_delete_btn').addClass('hidden');
            }

            $('.selected_ids').val(ids);
        });

        // Delete ONE RECORD
        $('td').on('click', '.delete', function (e) {
            vext.dialogActionRequest({
                'title': '<i class="voyager-trash"></i> {{ __("voyager::generic.delete_question") }}',
                'message': '{{ __("voyager::generic.delete_question") }} <br/>"<span class="dialog-file-name">{{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}' + '</span>"',
                'class': 'vext-dialog-warning',
                'yes': '{{ __('voyager-extension::bread.dialog_button_remove') }}',
                'url': '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id')),
                'method': 'POST',
                'fields': '',
                'method_field': '{{ method_field("DELETE") }}',
                'csrf_field': '{{ csrf_field() }}'
            });
        });

        // Delete MULTI RECORDS - BULK
        $('.side-body').on('click', '#vext_bulk_delete_btn', function (e) {

            var ids = [];
            var $checkedBoxes = $('#dataTable input[name=row_id]:checked').not('.select_all');
            var count = $checkedBoxes.length;

            // Deletion info
            var displayName = count > 1 ? '{{ $dataType->getTranslatedAttribute('display_name_plural') }}' : '{{ $dataType->getTranslatedAttribute('display_name_singular') }}';
            displayName = displayName.toLowerCase();

            // Gather IDs
            $.each($checkedBoxes, function () {
                var value = $(this).val();
                ids.push(value);
            })

            vext.dialogActionRequest({
                'title': '<i class="voyager-trash"></i> {{ __("voyager::generic.delete_question") }}',
                'message': '{{ __("voyager::generic.delete_question") }} <br/>"<span class="dialog-file-name">' + displayName + ' (' + count + ')</span>"',
                'fields': '<input type="hidden" name="ids" id="bulk_delete_input" value="'+ ids + '">',
                'class': 'vext-dialog-warning',
                'yes': '{{ __('voyager-extension::bread.dialog_button_remove') }}',
                'url': '{{ route('voyager.'.$dataType->slug.'.index') }}/0',
                'method': 'POST',
                'method_field': '{{ method_field("DELETE") }}',
                'csrf_field': '{{ csrf_field() }}'
            });

        });

        // Clone RECORD
        $('#dataTable').on('click', '.btn.clone', function () {
            vext.dialogActionRequest({
                'message': '{{ __('voyager-extension::bread.dialog_clone_message') }}',
                'class': 'vext-dialog-request',
                'yes': '{{ __('voyager-extension::bread.dialog_clone_yes_button') }}',
                'url': '{{ route('voyager.'.$dataType->slug.'.clone', '__id') }}'.replace('__id', $(this).data('id')),
                'method': 'POST',
                'method_field': '',
                'fields': '',
                'csrf_field': '{{ csrf_field() }}'
            });
        });

        // Toggle CHECKBOXES
        $(".tiny-toggle").tinyToggle({
            onChange: function() {

                var parent = $(this).parent().parent().parent();
                var value = $(this).attr("checked") ? 1 : 0;

                params = {
                    slug: parent.data("slug"),
                    id: parent.data("record-id"),
                    field: $(this).attr("name"),
                    value: value,
                    json: null,
                    _token: '{{ csrf_token() }}'
                }

                updateRecord(parent, params);
            },
        });

        // Updated requested record field - helper
        function updateRecord(parent, params) {
            $.post('{{ route('voyager.'.$dataType->slug.'.ext-record-update', '__id') }}'.replace('__id', params.id), params, function (response) {
                if (response
                    && response.data
                    && response.data.status
                    && response.data.status == 200) {

                    toastr.success(response.data.message);

                    if (params.field === 'status') {
                        parent.toggleClass('unpublished-record');
                    }

                } else {
                    toastr.error("Error setting new value for the field.");
                }
            });
        }

        // Inline Edit button
        $('.text-inline-edit').on('click', function(e) {
            let elEditorHolder = $(this).parent().parent().find('.browse-inline-editor');
            $(this).parent().css('display','none');
            elEditorHolder.css('display','flex');
            elEditorHolder.find('input').select();
        });

        // Inline Cancel button
        $('.text-inline-cancel').on('click', function(e) {
            $(this).parent().css('display','none');
            $(this).parent().parent().find('.browse-text-holder').css('display','flex');
        });

        // Inline press Enter
        $('.browse-inline-input').keypress(function(event) {
            if (event.keyCode == 13) {
                $(this).parent().find('.text-inline-save').click();
            }
        });

        // Inline Save button
        $('.text-inline-save').on('click', function(e) {

            let elTextHolder = $(this).parent().parent().find('.browse-text-holder');
            let elInput = $(this).parent().find('input');
            let parent = $(this).parent().parent().parent().parent();

            $(this).parent().css('display','none');
            elTextHolder.css('display','flex');
            elTextHolder.find('div').html(elInput.val());

            params = {
                slug: parent.data("slug"),
                id: elInput.data("id"),
                field: elInput.attr("name"),
                value: elInput.val(),
                json: null,
                _token: '{{ csrf_token() }}'
            };

            updateRecord(parent, params);

        });

        // Inline Group Field Edit button
        $('.group-inline-edit').on('click', function(e) {

            let parent = $(this).parent().parent().parent();
            let elFieldsHodlder =  $(this).parent();

            params = {
                slug: parent.data("slug"),
                id: parent.data("record-id"),
                field: $(this).data("name"),
                _token: '{{ csrf_token() }}'
            };

            if (vext_dialog) {
                vext_dialog.close();
            }

            // Inline Group Field Dialog
            vext_dialog = new $.Zebra_Dialog('', {
                'title': "{{ __('voyager-extension::bread.dialog_inline_title') }}",
                'custom_class': 'dialog-field-group class',
                'type': false,
                'modal': true,
                'position': ['center', 'middle'],
                'backdrop_opacity': 0.6,
                'buttons':  [
                    {
                        caption: "{{ __('voyager-extension::bread.dialog_button_save') }}", callback: function() {

                            let elForm = $('.inline-group-form');
                            let elInputs = elForm.find('input');
                            let data = { fields: {}};

                            // Make a complex fields object
                            elInputs.each(function(index, elem) {
                                data.fields[$(elem).data('key')] = {
                                    type: $(elem).attr('type'),
                                    label: $(elem).data('label'),
                                    value: $(elem).val()
                                }

                                let icon = $(elem).val() && $(elem).val().length > 0? 'voyager-check' : 'voyager-dot';

                                elFieldsHodlder.find(`[data-key='${$(elem).data('key')}']`).html(`<i class="${icon}"></i>`);

                            });

                            params.value = JSON.stringify(data);

                            updateRecord(parent, params);
                        }
                    },
                    {
                        caption: vext.trans('bread.dialog_button_cancel'), callback: function() {}
                    }
                ],
                source: {
                    ajax: {
                        method: "GET",
                        url: '{{ route('voyager.'.$dataType->slug.'.ext-group.form', '__id') }}'.replace('__id', params.id),
                        data: params,
                        complete: function(data) {
                            vext_dialog.update();
                        }
                    }
                }
            });

        });

    </script>
@stop
