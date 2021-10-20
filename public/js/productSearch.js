(function (){
    function addEmptyOptionToCategorySelect(categorySelectInput){
        let emptyOption = createElement('option',[],'');
        emptyOption.value = '';
        categorySelectInput.appendChild(emptyOption);
    }

    window.addEventListener('DOMContentLoaded', function (){
        let selectInput = document.getElementById('product-category-input');
        initializeCategorySelect(selectInput, window.categories,window.selectedCategoryId);
        addEmptyOptionToCategorySelect(selectInput);
        if(window.selectedCategoryId === ''){
            selectInput.value = '';
        }
    });
})();