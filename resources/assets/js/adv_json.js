$('document').ready(function () {

    // ------------------------------
    // Sorting rows
    // ------------------------------
    $('.adv-json-list').each(function(index, elem) {
        Sortable.create( document.getElementById($(elem).attr('id')), {
            animation: 200,
            sort: true,
            scroll: true,
            // Sort sections EVENT
            onSort: function (evt) {
                collectFieldsAndMakeJSON($(elem));
            }
        });
    });

    // ------------------------------
    // Add row
    // ------------------------------
    vext_page_content.on('click', '.add-json', function () {

        var json_list = $(`.adv-json-list.${$(this).data('field')}`);
        var json_form = $(this).parent().parent().clone(true);

        json_form.removeClass('adv-json-add-form');
        json_form.addClass('adv-json-item');
        json_form.find('input').attr('id', null);
        json_form.find('label').remove();
        json_button = json_form.find('button');
        json_button.html(json_button.data('remove'));
        json_button.removeClass(['btn-success','add-json']);
        json_button.addClass(['btn-danger','remove-json']);

        json_list.append(json_form);

        collectFieldsAndMakeJSON(json_list);

    });

    // ------------------------------
    // Remove row
    // ------------------------------
    vext_page_content.on('click', '.remove-json', function () {
        $(this).parent().parent().remove();
        var elList = $(`#adv-json-list-${$(this).data('field')}`);
        collectFieldsAndMakeJSON(elList);
    });

    // ------------------------------
    // Change value
    // ------------------------------
    vext_page_content.on('change', '.adv-json-item input', function () {
        var elList = $(`#adv-json-list-${$(this).data('masterField')}`);
        collectFieldsAndMakeJSON(elList);
    });

    // ------------------------------
    // Make JSON data
    // ------------------------------
    function collectFieldsAndMakeJSON(elList) {
        var field_json = $(`#${elList.data('field')}`);
        var json_data = [];

        elList.find('.adv-json-item').each(function(i1, item) {
            var row_data = {};
            $(item).find('input').each(function(index, elem) {
                // Make one field row
                $(elem).each(function(i, inp) {
                    var inputField = $(inp);
                    row_data[inputField.data('field')] = {
                        title: inputField.data('title'),
                        key: inputField.data('field'),
                        value: inputField.val()
                    };
                });
            });
            json_data.push(row_data);
        });

        field_json.val(JSON.stringify(json_data));
    }

});
