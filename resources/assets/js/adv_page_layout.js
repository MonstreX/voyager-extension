function makeLayoutData() {
    $('.adv-page-layout-wrapper').each(function(index, elem) {
        var layout_input = $(elem).find('input');
        var layout_elems = $(elem).find('.layout-sections-list .layout-section');
        var layout_data = [];
        layout_elems.each(function(index, elem) {

            layout_data.push({
                "type": $(elem).find('.layout-section-title-type').text(),
                "title": $(elem).find('.layout-section-title-name').text(),
                "icon": $(elem).find('.layout-icon').attr('class'),
                "key": $(elem).data('key')
            })
        });

        layout_input.val(JSON.stringify(layout_data));

    })
}

$('document').ready(function () {

    // ------------------------------
    // Sorting sections
    // ------------------------------
    $('.layout-sections-list').each(function(index, elem) {
        Sortable.create( document.getElementById($(elem).attr('id')), {
            animation: 200,
            sort: true,
            scroll: true,
            onSort: function (evt) {
                makeLayoutData();
            }
        });
    });

    // ------------------------------
    // Add section
    // ------------------------------
    vext_page_content.on('click', '.add-layout-section', function () {

        var layout_root = $(this).parent().parent().parent();
        var layout_list = layout_root.find('.layout-sections-list');
        var layout_template = layout_root.find('.layout-template');
        var select = $(this).parent().find('select');

        // Add new section
        layout_list.append('<div class="layout-section layout-added" style="display:none;">' + layout_template.html() + '</div>');
        var new_section = layout_list.find('.layout-added');
        new_section.removeClass('layout-added').addClass('layout-' + select.data('type'));
        new_section.find('.layout-section-title-type').text(select.data('type'));
        new_section.find('.layout-section-title-name').text(select.find('option:selected').text());
        new_section.find('.layout-icon').addClass(select.data('icon'));
        new_section.data('key', select.val());
        new_section.slideDown("slow");

        makeLayoutData();

    });

    // ------------------------------
    // Remove section
    // ------------------------------
    vext_page_content.on('click', '.remove-layout-section', function () {
        var section = $(this).parent().parent().parent();
        section.slideUp("slow", function(){
            section.remove();
            makeLayoutData();
        });
    });

});
