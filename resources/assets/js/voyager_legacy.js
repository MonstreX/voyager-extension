$('document').ready(function () {
    vext_page_content.on('click', '.remove-single-image, .remove-multi-image', function () {

        params = vext.getMediaParams($(this).parent().find('img'));
        // Voyager has another structure of these attributes in DOM so we need to correct them
        params.slug = vext_page_slug;
        params.field = $(this).parent().data('field-name');
        params.multi = $(this).hasClass('remove-multi-image');

        vext.dialogMediaRemove({
            'route': vext_routes.voyager_media_remove,
            'params': params,
            'remove_element': $(this).parent()
        });
    });
});
