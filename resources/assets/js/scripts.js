var params = {};
var $file;
var zebra_dialog;

function setParams(el) {
    params = {
        type:  el.data('type'),
        model:  el.data('model'),
        slug:   el.data('slug'),
        id:     el.data('id'),
        field:  el.data('field-name'),
        filename:  el.data('file-name'),
        _token: el.data('token')
    }
}

function deleteHandlerVExt(selector, isMulti) {
    return function() {

        $file = $(this).closest( selector );

        setParams($file);

        params.image_id = $file.data('image-id');
        params.multi = isMulti,

        console.log(params);

        $('.confirm_delete_name').text(params.filename);
        $('#confirm_delete_modal').modal('show');
    };
}

function initSortableAdvImagesGallery(imagesId) {
    if(imagesId) {
        Sortable.create( imagesId, {
            animation: 200,
            sort: true,
            scroll: true,
            onSort: function (evt) {
                var images_items = $(evt.item).parent().find('.adv-images-gallery-item');
                var images_new_order = [];
                images_items.each(function(index, elem) {
                    var image = $(elem).find('.adv-images-gallery-item-holder');
                    images_new_order.push(image.data('image-id'));
                    $(elem).find('.adv-images-gallery-order').text(index + 1);
                });

                var parent = images_items.closest('.adv-images-gallery-list');

                setParams(parent);
                params.multi =false;
                params.images_ids_order = images_new_order;

                $.post(parent.data('sort-route'), params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);

                    } else {
                        toastr.error("Error sorting media.");
                    }
                });
            }
        });
    }
}

$('document').ready(function () {

    // Bread Field Helper
    $(".page-content").on("click", ".control-label", function(){
        var helper = $(this).parent().find('.field-helper');
        if(!helper.hasClass('showed')) {
            helper.addClass('showed');
        } else {
            helper.removeClass('showed');
        }
    });

    // Upload Binding for Deleting Images
    $('.adv-image').on('click', '.single-adv-image-remove', deleteHandlerVExt('.adv-image', false));
    $('.adv-images-gallery-holder').on('click', '.adv-images-gallery-remove', deleteHandlerVExt('.adv-images-gallery-item-holder', true));


    //Init Sortable Images Sections
    var sortable_images = $('.adv-images-gallery-list');
    sortable_images.each(function(index, elem) {
        initSortableAdvImagesGallery(document.getElementById($(elem).attr('id')));
    });

    // Edit Sortable ML Image Content
    $(".page-content").on("click", ".adv-images-gallery-edit", function(evt){
        evt.preventDefault();

        var image_owner = $(this).closest('.adv-images-gallery-list');
        var parent = $(this).closest('.adv-images-gallery-item-holder');
        var title_holder = $(this).closest('.adv-images-gallery-item').find('.adv-images-gallery-title');
        var w_width = $(window).width();
        var w_height = $(window).height();

        setParams(parent);
        params.image_id = parent.data('image-id');

        var x_offs = (w_width - evt.screenX < 200) ? evt.screenX - 200 : evt.screenX;
        x_offs = (evt.screenX < 400) ? evt.screenX - 400 : x_offs;
        var y_offs = (w_height - evt.screenY < 200) ? evt.screenY - 200: evt.screenY;
        var y_pos = image_owner.data('extra-fields')? 'center' : 'top + ' + (y_offs - 50);

        if (zebra_dialog) {
            zebra_dialog.close();
        }

        zebra_dialog = new $.Zebra_Dialog('', {
            //'width': 400,
            'type': false,
            'modal': false,
            'max_width': '90%',
            'position': ['left + ' + (x_offs - 200), y_pos],
            'buttons':  [
                {
                    caption: 'Save', callback: function() {

                        var codemirror_editors = $('.codemirror_editor');
                        codemirror_editors.each(function(index, elem) {
                            var editor = elem.nextSibling.CodeMirror;
                            elem.value = editor.getValue();
                        });

                        var form_data = $(zebra_dialog.body).find('form').serialize();
                        var title = $(zebra_dialog.body).find('input[name*="title"]').val();

                        $.post(image_owner.data('update-route'), form_data, function (response) {
                            if ( response
                                && response.data
                                && response.data.status
                                && response.data.status == 200 ) {
                                toastr.success(response.data.message);

                                if (title.length > 0) {
                                    title_holder.html(title);
                                } else {
                                    title_holder.html('<i>...</i>');
                                }
                            } else {
                                toastr.error("Error saving media.");
                            }
                        });
                    }
                },
                {
                    caption: 'Cancel', callback: function() {}
                }
            ],
            source: {
                ajax: {
                    method: "POST",
                    url: image_owner.data('form-route'),
                    data: params,
                    complete: function(data) {

                        var codemirror_editors = $('.codemirror_editor');
                        codemirror_editors.each(function(index, elem) {
                            CodeMirror.fromTextArea(elem, {
                                mode: elem.dataset.mode,
                                theme: elem.dataset.theme,
                                autoCloseTags: true,
                                lineNumbers: true
                            });
                        });

                        zebra_dialog.update();

                    }
                }
            }
        });
    });

});

