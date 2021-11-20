@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());

    // Init Tabs Subsystem
    $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};
    $tabs[] = __('voyager-extension::bread.tab_main_title');
    foreach($dataTypeRows as $row) {
        if(isset($row->details->tab_title) && !in_array($row->details->tab_title, $tabs)) {
            $tabs[] = $row->details->tab_title;
        }
    }
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            id="form-edit-add"
                            class="form-edit-add"
                            data-url="{{ Request::url() }}"
                            data-url-create="{{ route('voyager.'.$dataType->slug.'.create') }}"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <input id="redirect-to" type="hidden" name="redirect_to" value="" >
                        <input type="hidden" name="model_name" value="{{ $dataType->model_name }}">
                        <input type="hidden" name="model_id" value="{{ $dataTypeContent->id }}">

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp


                            @if(count($tabs) > 1)
                            <ul class="nav nav-tabs tab-extension-edit">
                                @foreach($tabs as $key => $tab)
                                    <li @if($key == 0)  class="active" @endif ><a data-toggle="tab" href="#{{ 'tab-id-'.Str::slug($tab) }}">{{$tab}}</a></li>
                                @endforeach
                            </ul>
                            @endif

                            @if(count($tabs) > 1) <div class="tab-content tab-extension-edit"> @endif

                            @foreach($dataTypeRows as $row)

                                @if(count($tabs) > 1 && $loop->first)
                                    <div id="{{ 'tab-id-'.Str::slug($tabs[0]) }}" class="tab-pane active"><div>
                                    @php $cur_tab = $tabs[0] @endphp
                                @elseif(count($tabs) > 1 && isset($row->details->tab_title) && $row->details->tab_title !== $cur_tab)
                                    </div></div>
                                    <div id="{{ 'tab-id-'.Str::slug($row->details->tab_title) }}" class="tab-pane"><div>
                                    @php $cur_tab = $row->details->tab_title @endphp
                                @endif

                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp

                                @if (isset($row->details->section))
                                    <div class="panel-section col-md-12">
                                        <h3>{{ $row->details->section }}</h3>
                                    </div>
                                @endif

                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')

                                    @if (isset($row->details->view) && !isset($row->details->view_browse))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- FIELD END -->

                                @if(count($tabs) > 1 && $loop->last)
                                    </div></div>
                                @endif

                            @endforeach

                            @if(count($tabs) > 1) </div> @endif

                        </div><!-- panel-body -->

                        @if(!config('voyager-extension.sticky_action_panel.enabled'))
                            <div class="panel-footer">
                                @section('submit-buttons')
                                    <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                                @stop
                                @yield('submit-buttons')
                            </div>
                        @else
                            <div class="float-action-panel float-action-edit @if(!config('voyager-extension.sticky_action_panel.autohide')) locked @endif">
                                @section('submit-buttons')
                                    <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                                @stop
                                @yield('submit-buttons')
                                <button type="button" class="btn btn-save-and-continue btn-success">{{ __('voyager-extension::bread.save_and_continue') }}</button>
                                <button type="button" class="btn btn-save-and-create btn-warning">{{ __('voyager-extension::bread.save_and_create') }}</button>
                            </div>
                        @endif


                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script src="{{ voyager_extension_asset('js/jquery-autocomplete/jquery.autocomplete.min.js') }}"></script>
    <script>

        $('document').ready(function () {

            // Manage sticky action panel
            @if(config('voyager-extension.sticky_action_panel.autohide'))
            const elFloatPanel = $('.float-action-panel');
            elFloatPanel.on("mouseover", function () {
                $(this).css("bottom","0");
            });
            elFloatPanel.on("mouseleave", function () {
                $(this).css("bottom","-40px");
            });
            @endif

            // Save and continue
            const elForm = $('#form-edit-add')
            const elRedirect = $('#redirect-to')
            $('.btn-save-and-continue').on('click', function(){
                elRedirect.val(elForm.data('url'));
                elForm.submit();
            });

            $('.btn-save-and-create').on('click', function(){
                elRedirect.val(elForm.data('url-create'));
                elForm.submit();
            });

            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
