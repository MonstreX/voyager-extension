@section('page_title', $dataType->display_name_plural . ' ' . __('voyager::bread.tree_list'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-list"></i>{{ $dataType->display_name_plural }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
<div class="page-content container-fluid vext-browse">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <p class="panel-title" style="color:#777">{{ __('voyager::generic.drag_drop_info') }}</p>
                </div>
                <div class="panel-body tree-items-list" style="padding:30px;">
                    <div class="dd tree-items-list">
                        @include('voyager-extension::bread.partials.tree-list', ['items' => flat_to_tree($dataTypeContent->toArray())])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="{{ voyager_extension_asset('js/tinytoggle/css/tinytoggle.min.css') }}">
@stop


@section('javascript')
<script src="{{ voyager_extension_asset('js/tinytoggle/jquery.tinytoggle.min.js') }}"></script>

<script>
$(document).ready(function () {
    var params = {};

    @if ($isModelTranslatable)
        $('.side-body').multilingual();
        //Reinitialise the multilingual features when they change tab
        $('#dataTable').on('draw.dt', function(){
            $('.side-body').data('multilingual').init();
        })
    @endif

    function setTreeParents() {
        var dd_items = $(".tree-items-list li.dd-item");
        dd_items.each(function(index, elem) {
            if($(elem).find('ol').length > 0) {
                $(elem).addClass('has-children-items');
            } else {
                $(elem).removeClass('has-children-items');
            }
        });
    }

    setTreeParents();

    // Toggle CHECKBOXES
    $(".tiny-toggle").tinyToggle({
        onChange: function() {

            var parent = $(this).parent().parent().parent().parent().parent().parent();
            var value = $(this).attr("checked") ? 1 : 0;
            params = {
                slug: parent.data("slug"),
                id: parent.data("record-id"),
                field: $(this).attr("name"),
                value: value,
                json: null,
                _token: '{{ csrf_token() }}'
            }

            $.post('{{ route('voyager.'.$dataType->slug.'.ext-record-update', '__id') }}'.replace('__id', $(this).data('id')), params, function (response) {
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

        },
    });


    $('.dd').nestable({
        handleClass: 'dd-tree-handle',
        placeClass: 'dd-tree-placeholder'
    });

    /**
    * Reorder items
    */
    $('.dd').on('change', function (e) {

        params = {
            slug:   '{{ $dataType->slug }}',
            order: JSON.stringify($('.dd').nestable('serialize')),
            _token: '{{ csrf_token() }}'
        }

        $.post('{{ route('voyager.'.$dataType->slug.'.ext-records-order') }}', params, function (data) {
            toastr.success("{{ __('voyager::bread.updated_order') }}");
        });

        setTreeParents();

    });

    // Clone RECORD
    $('.dd-handle').on('click', '.btn.clone', function () {
        vext.dialogActionRequest({
            'message': '{{ __('voyager-extension::bread.dialog_clone_message') }} - (id:' + $(this).data('id') +')',
            'class': 'vext-dialog-request',
            'yes': '{{ __('voyager-extension::bread.dialog_clone_yes_button') }}',
            'url': '{{ route('voyager.'.$dataType->slug.'.clone', '__id') }}'.replace('__id', $(this).data('id')),
            'method': 'POST',
            'method_field': '',
            'fields': '',
            'csrf_field': '{{ csrf_field() }}'
        });
    });

    // Delete ONE RECORD
    $('.dd-handle').on('click', '.delete', function (e) {

        if ($(this).closest('li').hasClass('has-children-items')) {
            vext.createDialogOk({
                'message': '{{ __('voyager-extension::bread.dialog_cant_delete_with_children') }}',
                'title': '{{ __('voyager-extension::bread.dialog_cant_delete_with_children_title') }}',
                'class': 'vext-dialog-request'
            });
            return false;
        }

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

});
</script>
@stop
