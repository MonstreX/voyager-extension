$('document').ready(function () {

    vext_page_content.on('click', '.single-adv-image-remove', function (evt) {

        var image = $(this).parent();

        if($(this).parent().data('file-name')) {
            console.log('.single-adv-image-remove');
        } else if ($(this).parent().parent().data('file-name')) {
            console.log('.adv-images-gallery-remove');
        }

        console.log(image);

        vext.createDialogYesNo( evt,{
            'title': vext.trans('bread.dialog_remove_title'),
            'message': vext.trans('bread.dialog_remove_message'),
            'callback': function () {

                console.log('press YES');

            }
        });

    });

});
