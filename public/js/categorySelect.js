/**
 * Populates the selectElement with the options that are a list of categories.
 * @param selectElement
 * @param categories
 * @param selectedId
 */
function initializeCategorySelect(selectElement, categories, selectedId = 0) {
    selectElement.innerHTML = '';
    for (let category of categories) {
        let option = document.createElement('option');
        option.value = category.id;
        option.innerText = category.title;
        selectElement.appendChild(option);
    }

    selectElement.value = selectedId;
}

//  This was refactored into a separate file, because when editing a product we need to populate
// the select for categories.
// The problem that we have now is that the user can change select options and break the select.