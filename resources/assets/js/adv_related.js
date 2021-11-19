$('document').ready(function () {

    const advRelatedList = $('.adv-related-list')

    // ------------------------------
    // Sorting related items
    // ------------------------------
    advRelatedList.each(function(index, elem) {
        Sortable.create( document.getElementById($(elem).attr('id')), {
            animation: 200,
            sort: true,
            scroll: true,
            onSort: function (evt) {
                const relatedList = $('#adv-related-list-' + $(elem).data('field'))
                collectRelatedItemsAndMakeJSON(relatedList)
            }
        });
    });

    // ------------------------------
    // Related Item Template
    // ------------------------------
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

    // ------------------------------
    // Check for doubles
    // ------------------------------
    function relatedCheck(relatedObj) {
        let result = false;
        $('#adv-related-list-' + relatedObj.field + ' .adv-related-item').each(function() {
           const title = $(this).find('.adv-related-item__title').text()
           if (title === relatedObj.display) {
               result = true
               return false
           }
        })
        return result;
    }

    // ------------------------------
    // Collect all related items, convert to
    // JSON and store it in the result input field
    // ------------------------------
    function collectRelatedItemsAndMakeJSON(elList) {
        const field_json = $(`#${elList.data('field')}`);
        const data = []
        elList.find('.adv-related-item').each(function(iRow, elRow) {
            data.push($(elRow).data('data'))
        });
        field_json.val(JSON.stringify(data));
    }

    // ------------------------------
    // Initialize and handle All Autocomplete Fields
    // ------------------------------
    $('.related-autocomplete').each(function() {
        const related = $(this);
        $( this ).autocomplete({
            lookup: function (query, done) {
                const params = {
                    query: related.val(),
                    slug: related.data('slug'),
                    where: 'like',
                    prefix: '%',
                    suffix: '%',
                    search_field: related.data('search-field'),
                    display_field: related.data('display-field'),
                    fields: related.data('fields')
                }

                $.get(related.data('url'), params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        let suggestions = []
                        suggestions = response.data.data.map(function(item, index) {
                            return {
                                value: item[related.data('display-field')],
                                data: item
                            }
                        })

                        done({ suggestions: suggestions});

                    } else {
                        console.log('Adv Related: Autocomplete AJAX Error...')
                    }
                });
            },
            onSelect: function (suggestion) {
                const data = {
                    display_field: related.data('display-field'),
                    fields: suggestion.data
                }
                related.data('display', suggestion.value)
                related.data('data', JSON.stringify(data))
                related.parent().find('button').prop("disabled", false)
            }
        })
    })

    // ------------------------------
    // Add new related item into the related list
    // ------------------------------
    $('.add-related').on('click', function () {
        const related = $('#adv-related-autocomplete-' + $(this).data('field'))
        const relatedList = $('#adv-related-list-' + $(this).data('field'))
        const relatedObj = {
            field: related.data('field'),
            display: related.data('display'),
            display_field: related.data('display-field'),
            data: related.data('data')
        }

        if (!relatedCheck(relatedObj)) {
            $("#adv-related-list-" + related.data('field')).append(relatedTemplate(relatedObj))
            collectRelatedItemsAndMakeJSON(relatedList)
        }
    })

    // ------------------------------
    // Remove related item
    // ------------------------------
    advRelatedList.on('click', '.remove-related', function () {
        const relatedList = $('#adv-related-list-' + $(this).data('field'))
        $(this).closest('.adv-related-item').remove();
        collectRelatedItemsAndMakeJSON(relatedList)
    })

});
