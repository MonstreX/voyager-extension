$('document').ready(function () {

    function relatedTemplate(relatedObj) {
        return `
        <div class="adv-related-item" data-data='${relatedObj.data}'>
            <div class="adv-related-item__handle"><span></span><span></span><span></span></div>
            <div class="adv-related-item__title">${relatedObj.display}</div>
            <div class="adv-related-item__remove">
                <button data-field="${relatedObj.field}" type="button" class="btn btn-danger remove-related"><i class='voyager-x'></i></button>
            </div>
        </div>`
    }

    function relatedCheck(relatedObj) {
        let result = false;
        $('#adv-related-list-' + relatedObj.field + ' .adv-related-item').each(function() {
           const title = $(this).find('.adv-related-item__title').text()
           if (title === relatedObj.display) {
               console.log('Exist!')
               result = true
               return false
           }
        })
        return result;
    }

    function collectRelatedItemsAndMakeJSON(elList) {
        const field_json = $(`#${elList.data('field')}`);

        const data = []
        elList.find('.adv-related-item').each(function(iRow, elRow) {
            data.push($(elRow).data('data'))
        });
        field_json.val(JSON.stringify(data));

        console.log(field_json.val());
    }

    $('.related-autocomplete').each(function() {
        const related = $(this);
        $( this ).autocomplete({
            serviceUrl: related.data('url'),
            params: {
                slug: related.data('slug'),
                search: related.data('search'),
                display: related.data('display'),
                fields: related.data('fields')
            },
            onSelect: function (suggestion) {
                const data = {
                    display: suggestion.value,
                    fields: suggestion.data
                }
                related.data('display', suggestion.value)
                related.data('data', JSON.stringify(data))
                related.parent().find('button').prop("disabled", false)
            }
        })
    })

    // Add new related item into the related list
    $('.add-related').on('click', function () {
        const related = $('#adv-related-autocomplete-' + $(this).data('field'))
        const relatedList = $('#adv-related-list-' + $(this).data('field'))
        const relatedObj = {
            field: related.data('field'),
            display: related.data('display'),
            data: related.data('data')
        }
        if (!relatedCheck(relatedObj)) {
            $("#adv-related-list-" + related.data('field')).append(relatedTemplate(relatedObj))
            collectRelatedItemsAndMakeJSON(relatedList)
        }
    })

    // Remove related item
    $('.adv-related-list').on('click', '.remove-related', function () {
        const relatedList = $('#adv-related-list-' + $(this).data('field'))
        $(this).closest('.adv-related-item').remove();
        collectRelatedItemsAndMakeJSON(relatedList)
    })

});
