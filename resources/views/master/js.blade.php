@push('javascript')
<script>
function tinymce_init_callback(editor)
{
    //console.log('init tinyMCE...');
}

function tinymce_setup_callback(editor)
{
    console.log('setup tinyMCE...');

    editor.settings.external_plugins = {
        'ace': '{{ voyager_extension_asset('js/ace/plugin.js') }}'
    }

    editor.settings.plugins = 'link, image, -ace, textcolor, lists';
    editor.settings.toolbar = 'removeformat | styleselect bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | link image table | ace';

    editor.settings.cleanup = false;
    editor.settings.verify_html = false;
    editor.settings.min_height = 200;
}

$('document').ready(function () {

    // Remove Legacy Voyager DIALOG BOX, We'll use our own dialog box implementation.
    $('#confirm_delete_modal').remove();

    // Generate Routes for Frontend JS
    window.vext_page_slug = "{{$page_slug}}";
    window.vext_routes = {
        'voyager_media_remove': '{{ route("voyager." . $page_slug . ".media.remove") }}',
        'ext_media_remove': '{{ route("voyager." . $page_slug . ".ext-media.remove") }}',
        'ext_media_sort': '{{ route("voyager." . $page_slug . ".ext-media.sort") }}',
        'ext_media_update': '{{ route("voyager." . $page_slug . ".ext-media.update") }}',
        'ext_media_change': '{{ route("voyager." . $page_slug . ".ext-media.change") }}',
        'ext_media_form': '{{ route("voyager." . $page_slug . ".ext-media.form") }}',
    };

});
</script>
@endpush