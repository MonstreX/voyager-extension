function initSortableAdvMediaFiles(filesId) {
    if(filesId) {
        Sortable.create( filesId, {
            animation: 200,
            sort: true,
            scroll: true,
            onSort: function (evt) {
                var files_items = $(evt.item).parent().find('.adv-media-files-item');
                var files_new_order = [];
                files_items.each(function(index, elem) {
                    var file = $(elem).find('.adv-media-files-item-holder');
                    files_new_order.push(file.data('file-id'));
                    $(elem).find('.adv-media-files-order').text(index + 1);
                });

                var parent = files_items.closest('.adv-media-files-list');
                var params = vext.getMediaParams(parent);
                params.multi = false;
                params.files_ids_order = files_new_order;

                $.post(vext_routes.ext_media_sort, params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);

                    } else {
                        toastr.error(vext.trans('bread.error_sorting_media'));
                    }
                });
            }
        });
    }
}

$('document').ready(function () {
    // ------------------------------
    //Init Sortable Files Sections
    // ------------------------------
    var sortable_files = $('.adv-media-files-list');
    sortable_files.each(function(index, elem) {
        initSortableAdvMediaFiles(document.getElementById($(elem).attr('id')));
    });

    // ------------------------------
    // Change Selected File
    // ------------------------------
    vext_page_content.on("click", ".adv-media-files-change", function(evt){
        evt.preventDefault();

        var file_owner_input = $('#' + $(this).closest('.adv-media-files-list').data('input-id'));
        var parent = $(this).closest('.adv-media-files-item-holder');

        var pos = vext.getDialogPosition(evt, true);

        if (!vext_change_file_form) {
            vext_change_file_form = $($('#change-file-template').prop('content')).find('#change-file-form');
        }

        vext_change_file_form.find('img').attr('src', parent.find('.adv-media-files-file img').attr('src'));
        vext_change_file_form.find('[name="_token"]').val(parent.data('token'));
        vext_change_file_form.find('[name="model"]').val(parent.data('model'));
        vext_change_file_form.find('[name="slug"]').val(parent.data('slug'));
        vext_change_file_form.find('[name="id"]').val(parent.data('id'));
        vext_change_file_form.find('[name="field"]').val(parent.data('field-name'));
        vext_change_file_form.find('[name="media_file_id"]').val(parent.data('file-id'));

        vext_change_file_form.find('[type="file"]').attr('name', parent.data('field-name'));
        vext_change_file_form.find('[type="file"]').attr('accept', file_owner_input.attr('accept'));

        if (vext_dialog) {
            vext_dialog.close();
        }

        vext_dialog = new $.Zebra_Dialog('', {
            'type': false,
            'modal': false,
            'position': [pos.x, pos.y],
            'buttons':  [
                {
                    caption: vext.trans('bread.dialog_button_change'), callback: function() {

                        var form = $('#change-file-form')[0];
                        var data = new FormData(form);
                        var image = $('#change-file-form img');
                        var image_src = image.attr('src');
                        var file_type = image.data('file-type');

                        $.ajax({
                            url: vext_routes.ext_media_change,
                            type: "POST",
                            data: data,
                            contentType: false,
                            cache: false,
                            processData:false,
                            success: function(response) {
                                if ( response
                                    && response.data
                                    && response.data.status
                                    && response.data.status == 200 ) {
                                    toastr.success(response.data.message);

                                    // Update File and Filename
                                    parent.data('file-name', response.data.data.file_name);
                                    parent.data('file-id', response.data.data.file_id);
                                    parent.find('img').attr('src', image_src);

                                    if (file_type === 'image') {
                                        parent.find('img').removeClass('file-type');
                                    } else {
                                        parent.find('img').addClass('file-type');
                                    }

                                    parent.parent().find('.adv-media-files-filename').html(response.data.data.file_name_size);

                                } else {
                                    toastr.error(vext.trans('bread.error_saving_media'));
                                }
                            }
                        });
                    }
                },
                {
                    caption: vext.trans('bread.dialog_button_cancel'), callback: function() {}
                }
            ],
            source: {inline: vext_change_file_form}
        });

        vext_dialog.update();

        $("#change_file_input").change(function(){
            vext.readURL(this);
        });

    });


    // ------------------------------
    // Edit Sortable ML File Content
    // ------------------------------
    vext_page_content.on("click", ".adv-media-files-edit", function(evt){
        evt.preventDefault();

        var file_owner = $(this).closest('.adv-media-files-list');
        var parent = $(this).closest('.adv-media-files-item-holder');
        var title_holder = $(this).closest('.adv-media-files-item').find('.adv-media-files-title');

        //var pos = vext.getDialogPosition(evt, file_owner.data('extra-fields'));

        var params = vext.getMediaParams(parent);

        if (vext_dialog) {
            vext_dialog.close();
        }

        vext_dialog = new $.Zebra_Dialog('', {
            'type': false,
            'modal': false,
            'max_width': '90%',
            //'position': [pos.x, pos.y],
            'position': ['center', 'middle'],
            'buttons':  [
                {
                    caption: vext.trans('bread.dialog_button_save'), callback: function() {

                        var codemirror_editors = $('.codemirror_editor');
                        codemirror_editors.each(function(index, elem) {
                            var editor = elem.nextSibling.CodeMirror;
                            elem.value = editor.getValue();
                        });

                        var form_data = $(vext_dialog.body).find('form').serialize();
                        var title = $(vext_dialog.body).find('input[name*="title"]').val();

                        $.post(vext_routes.ext_media_update, form_data, function (response) {
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
                                toastr.error(vext.trans('bread.error_saving_media'));
                            }
                        });
                    }
                },
                {
                    caption: vext.trans('bread.dialog_button_cancel'), callback: function() {}
                }
            ],
            source: {
                ajax: {
                    method: "POST",
                    url: vext_routes.ext_media_form,
                    data: params,
                    complete: function(data) {
                        var codemirror_editors = $('.codemirror_editor');
                        codemirror_editors.each(function(index, elem) {
                            inst = CodeMirror.fromTextArea(elem, {
                                mode: elem.dataset.mode,
                                theme: elem.dataset.theme,
                                autoCloseTags: true,
                                lineNumbers: true
                            });
                        });
                        vext_dialog.update();
                    }
                }
            }
        });
    });

    // ------------------------------
    // Remove File
    // ------------------------------
    vext_page_content.on('click', '.adv-media-files-remove', function () {

        var file = $(this).parent().parent();

        vext.dialogMediaRemove({
            'route': vext_routes.ext_media_remove,
            'params': vext.getMediaParams(file),
            'remove_elements': file.parent()
        });

    });

    // ------------------------------
    // Mark files for bunch remove
    // ------------------------------
    vext_page_content.on("click", ".adv-media-files-mark", function(evt){

        $(this).closest('.adv-media-files-item-holder').toggleClass('remove');

        var files_list = $(this).closest('.adv-media-files-list');
        var bunch_holder = $('#' + files_list.data('bunch-adv-remove-holder'));
        if (files_list.find('.remove').length > 0) {
            bunch_holder.removeClass('hidden');
        } else {
            bunch_holder.addClass('hidden');
        }
    });


    // ------------------------------
    // Unmark selected files and Select All
    // ------------------------------
    vext_page_content.on("click", ".bunch-adv-media-files-unmark, .bunch-adv-media-files-select-all", function(evt){
        var files_list = $('#' + $(this).parent().data('files-gallery-list'));
        var bunch_holder = $('#' + files_list.data('bunch-adv-remove-holder'));
        var files_items = files_list.find('.adv-media-files-item-holder');

        var select_button = $('.bunch-adv-media-files-select-all');
        var clear_button = $('.bunch-adv-media-files-unmark');

        if ($(this).hasClass('bunch-adv-media-files-unmark')) {
            files_items.each(function(index, elem) {
                $(elem).removeClass('remove');
            });
            bunch_holder.addClass('hidden');
            select_button.removeClass('hidden');
            clear_button.addClass('hidden');
        } else {
            files_items.each(function(index, elem) {
                $(elem).addClass('remove');
            });
            bunch_holder.removeClass('hidden');
            select_button.addClass('hidden');
            clear_button.removeClass('hidden');
        }

        //$(this).toggle('hidden');
    });


    // ------------------------------
    // Remove Bunch of selected files
    // ------------------------------
    vext_page_content.on("click", ".bunch-adv-media-files-remove", function(evt){
        var files_list = $('#' + $(this).parent().data('files-gallery-list'));

        if (files_list.find('.remove').length > 0) {

            var files_ids = [];
            files_list.find('.remove').each(function(index, elem) {
                files_ids.push($(elem).data('file-id'));
            });

            vext.dialogMediaRemove({
                'route': vext_routes.ext_media_remove,
                'params': {
                    'media_ids': files_ids,
                    'filename': files_ids.length + vext.trans('bread.adv_media_files.dialog_remove_files')
                    },
                'remove_elements': files_list.find('.remove').parent()
            });
        }
    });

});
