$('document').ready(function () {
    vext_page_content.on('click', '.remove-single-image, .remove-multi-image, .remove-single-file, .remove-multi-file', function () {

        var el = $(this).parent().find('img');
        if($(this).hasClass('remove-multi-file')) {
            el = $(this).siblings('a');
        }

        params = vext.getMediaParams(el);

        // Voyager has another structure of these attributes in DOM so we need to correct them
        params.slug = vext_page_slug;
        params.field = $(this).parent().data('field-name');
        params.multi = $(this).hasClass('remove-multi-image') || $(this).hasClass('remove-multi-file');

        vext.dialogMediaRemove({
            'route': vext_routes.voyager_media_remove,
            'params': params,
            'remove_elements': $(this).parent()
        });
    });
});
