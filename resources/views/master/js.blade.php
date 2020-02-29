@push('javascript')
<script>
$('document').ready(function () {

    // Remove Legacy Voyager DIALOG BOX, We'll use our own implementation.
    $('#confirm_delete_modal').remove();

    // Generate Routes for Frontend
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