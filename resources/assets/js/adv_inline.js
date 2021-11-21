$('document').ready(function () {

    const inlineItemsList = $('.adv-inline-set-list')

    // ------------------------------
    // Sorting rows
    // ------------------------------
    inlineItemsList.each(function(index, elem) {
        Sortable.create( document.getElementById($(elem).attr('id')), {
            animation: 200,
            sort: true,
            scroll: true,
            handle: ".adv-inline-set-handle",
            onSort: function (evt) {
                //
            }
        });
    });

    // ------------------------------
    // Mark for delete on save
    // ------------------------------
    inlineItemsList.on('click', '.adv-inline-set-delete', function () {
        const elItem = $(this).closest('.adv-inline-set-item')
        const elDeleteInput = elItem.find('.adv-inline-set-delete-input')
        elItem.toggleClass('inline-delete')
        if (elItem.hasClass('inline-delete')) {
            elDeleteInput.val(true)
        } else {
            elDeleteInput.val(false)
        }
    })

    function getNextInlineID(elInlineList) {
        const elInlineItems = elInlineList.find('.adv-inline-set-item')
        let maxID = $(elInlineItems[0]).data('index')
        elInlineItems.each(function(idx, item) {
            const index = $(item).data('index')
            if (index > maxID ) {
                maxID = index
            }
        })
        return maxID + 1
    }

    // ------------------------------
    // Add new Inline Item to the Inline Items List
    // ------------------------------
    $('.add-inline-set').on('click', function () {
        const elWrapper = $(this).closest('.adv-inline-set-wrapper')
        const elInlineList = elWrapper.find('.adv-inline-set-list')
        const elNewInlineItem = elWrapper.find('.adv-inline-set-template').clone(true)

        const newIndex = getNextInlineID(elInlineList)

        elNewInlineItem.removeClass('adv-inline-set-template')
        elNewInlineItem.data('index', newIndex)
        elNewInlineItem.find('.adv-inline-set-index').val(0)

        elNewInlineItem.find('.form-group').each(function(idx, item) {
            const elLabel = $(item).find('label')
            const elField = $(item).find('.form-control')
            elLabel.attr('for', elLabel.attr('for') + newIndex)
            elField.attr('id', elField.attr('id') + newIndex)
        })

        elInlineList.append(elNewInlineItem)

        if(elInlineList.data('many') !== 1) {
            elWrapper.find('.adv-inline-set-actions').remove()
        }
    })


})
