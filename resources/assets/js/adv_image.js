$('document').ready(function () {

    vext_page_content.on('click', '.single-adv-image-remove', function () {

        var image = $(this).parent();

        vext.dialogMediaRemove({
            'route': vext_routes.ext_media_remove,
            'params': vext.getMediaParams(image),
            'remove_elements': image.parent()
        });
    });

});
