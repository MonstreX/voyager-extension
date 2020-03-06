@push('javascript')
<script>
function tinymce_init_callback(editor)
{
    console.log('init tinyMCE...');
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

// var ace_editor_element = document.getElementsByClassName("ace_editor");
//
// // For each ace editor element on the page
// for(var i = 0; i < ace_editor_element.length; i++)
// {
//     // Create an ace editor instance
//     var ace_editor = ace.edit(ace_editor_element[i].id);
//     if(ace_editor_element[i].getAttribute('data-ace-theme')){
//         ace_editor.setTheme("ace/theme/" + ace_editor_element[i].getAttribute('data-ace-theme'));
//     }
//     if(ace_editor_element[i].getAttribute('data-ace-mode')){
//         ace_editor.getSession().setMode("ace/mode/" + ace_editor_element[i].getAttribute('data-ace-mode'));
//     }
//     // Set auto height of window
//     ace_editor.setOptions({
//         maxLines: Infinity
//     });
// }


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


    //console.log(window.vext_routes);

});
</script>
@endpush