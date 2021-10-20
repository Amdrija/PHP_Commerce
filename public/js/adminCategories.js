(function () {
    const showSign = "\u25B6";
    const hideSign = "▲";
    const rootCategoryId = '0';

    let categoryState = (function categoryState() {
        let method = 'POST';
        let selectedCategoryId = '0';

        function setPOSTMethod() {
            method = 'POST';
        }

        function setPUTMethod() {
            method = 'PUT';
        }

        function getMethod() {
            return method;
        }

        function resetSelectedCategoryId() {
            selectedCategoryId = '0';
        }

        function setSelectedCategoryId(id) {
            selectedCategoryId = id;
        }

        function getSelectedCategoryId() {
            return selectedCategoryId;
        }

        return {
            setPOSTMethod: setPOSTMethod,
            setPUTMethod: setPUTMethod,
            getMethod: getMethod,
            resetSelectedCategoryId: resetSelectedCategoryId,
            setSelectedCategoryId: setSelectedCategoryId,
            getSelectedCategoryId: getSelectedCategoryId
        }
    })();

    /**
     * Makes all category titles inactive.
     */
    function inactivateTitles() {
        let categoryTitles = document.getElementsByClassName('category-title');
        for (let title of categoryTitles) {
            title.classList.remove('active');
        }
    }

    /**
     * All category titles become inactive and activates the clicked title.
     */
    function toggleActiveTitle(event) {
        inactivateTitles();

        let categoryTitle = event.target;
        categoryTitle.classList.add('active');

        document.getElementById('parent-category-input').disabled = false;
    }

    /**
     * Populates form fields when creating root category.
     */
    function populateFieldsForRootCategory() {
        populateFields({
            title: '',
            parentId: rootCategoryId,
            parentTitle: '',
            code: '',
            description: ''
        }, 'Add root category');
    }

    /**
     * Populates form fields when creating subcategory.
     * @param parentId
     */
    function populateFieldsForSubCategory(parentId) {
        populateFields({
            title: '',
            parentId: parentId,
            parentTitle: '',
            code: '',
            description: ''
        }, 'Add subcategory');
    }

    /**
     * Populates the category form fields.
     * @param category
     * @param formTitleText
     */
    function populateFields(category, formTitleText) {
        let formTitle = document.getElementsByClassName('category-form-title')[0];
        formTitle.innerText = formTitleText;

        document.getElementById('category-title-input').value = category.title;
        document.getElementById('parent-category-input').value = category.parentId;
        document.getElementById('category-code-input').value = category.code;
        document.getElementById('category-description').value = category.description;
    }

    function displayCategoryForm() {
        let categoryFormContainer = document.getElementsByClassName('category-form-container')[0];
        if (categoryFormContainer.classList.contains('not-displayed')) {
            categoryFormContainer.classList.remove('not-displayed');
        }
    }

    /**
     * Activates the title that was clicked.
     * Displays the category form.
     * Sets the categoryState.selectedCategoryId to the selects category's id.
     * Sets the form submit method to PUT.
     * @param event
     */
    function categoryTitleClick(event) {
        toggleActiveTitle(event);
        displayCategoryForm();

        showElement(document.getElementById('delete-button'));

        // categoryData object is set on the category container element which is a parent of category title.
        let category = event.target.parentElement.parentElement.categoryData;
        populateFields(category, 'Selected Category');

        categoryState.setSelectedCategoryId(category.id);
        categoryState.setPUTMethod();
    }

    /**
     * Toggles the display of the subcategory container.
     * @param event
     */
    function toggleSubcategories(event) {
        // since we are clicking on the button that is inside category-title-container div
        // we need to get the parent of category-title-container div, which is category-container div.
        let categoryContainer = event.target.parentElement.parentElement;
        let subcategoryContainer = categoryContainer.getElementsByClassName('subcategory-container')[0];

        let button = event.target;

        if (isSubcategoryContainerShown(button)) {
            hideSubcategoryContainer(button, subcategoryContainer);
        } else {
            showSubcategoryContainer(button, subcategoryContainer);
        }
    }

    function showSubcategoryContainer(button, subcategoryContainer) {
        button.innerText = hideSign;
        subcategoryContainer.classList.add('collapsed');
    }

    function hideSubcategoryContainer(button, subcategoryContainer) {
        button.innerText = showSign;
        subcategoryContainer.classList.remove('collapsed');
    }

    /**
     * If the inner text of the button is hideSign that means the subcategory container is displayed.
     */
    function isSubcategoryContainerShown(button) {
        return button.innerText === hideSign;
    }

    /**
     * Initializes the Category tree.
     */
    function createCategoryTree(categories, parentContainer) {
        /**
         * @var category
         * @property subcategories
         */
        for (let category of categories) {
            let categoryContainer = createElement('div', ['category-container']);

            let categoryTitleContainer = createElement('div', ['category-title-container']);

            let categoryTitle = createElement('h3', ['category-title'], category.title);
            categoryTitle.addEventListener('click', categoryTitleClick);

            categoryTitleContainer.appendChild(categoryTitle);
            categoryContainer.appendChild(categoryTitleContainer);

            if (category.subcategories.length > 0) {
                let collapseButton = createElement('button', ['collapsable', 'button'], showSign);
                collapseButton.addEventListener('click', toggleSubcategories);

                let subcategoryContainer = createElement('div', ['subcategory-container']);

                createCategoryTree(category.subcategories, subcategoryContainer);

                categoryTitleContainer.insertBefore(collapseButton, categoryTitle);
                categoryContainer.appendChild(subcategoryContainer);
            } else {
                categoryTitle.classList.add('leaf-title');
            }

            categoryContainer.categoryData = {
                id: category.id,
                title: category.title,
                parentId: category.parentId,
                parentTitle: category.parentTitle,
                code: category.code,
                description: category.description
            };
            parentContainer.appendChild(categoryContainer);
        }

    }

    /**
     * Constructs a list of all available categories from the categoryData properties of category-container elements.
     */
    function constructCategoryArray() {
        let categoryContainers = document.getElementsByClassName('category-container');

        let categoryArray = [];
        for (let categoryContainer of categoryContainers) {
            categoryArray.push({
                id: categoryContainer.categoryData.id,
                title: categoryContainer.categoryData.title
            });
        }
        return categoryArray;
    }

    /**
     * Initializes the select element's options to be able to select parent categories.
     * @param selectElement
     * @param categories
     */
    function initializeSelect(selectElement, categories) {
        initializeCategorySelect(selectElement, categories)

        let rootOption = document.createElement('option');
        rootOption.value = rootCategoryId;
        rootOption.innerText = 'Root';
        selectElement.appendChild(rootOption);
    }

    function hideCategoryForm() {
        document.getElementsByClassName('category-form-container')[0].classList.add('not-displayed');
    }

    function validateCategoryFormInputs(title, code, description) {
        return title !== '' && code !== '' && description !== '';
    }

    /**
     * Hides the element.
     */
    function hideElement(element) {
        element.classList.add('hidden');
    }

    /**
     * Remove the hidden class from the element.
     * @param element
     */
    function showElement(element) {
        element.classList.remove('hidden');
    }

    /**
     * Displays the category form when the 'add root category button' is clicked.
     * Sets the category form submit method to POST.
     * Disables the parent dropdown menu.
     * Makes all the category titles inactive.
     * @param event
     */
    function rootCategoryButtonClick(event) {
        event.preventDefault();
        displayCategoryForm();
        populateFieldsForRootCategory();
        categoryState.setPOSTMethod();
        document.getElementById('parent-category-input').disabled = true;
        inactivateTitles();
        hideElement(document.getElementById('delete-button'));
    }

    /**
     * Displays the category form when the 'add subcategory button' is clicked.
     * Sets the category form submit method to POST.
     * Disables the parent dropdown menu.
     * Makes all the category titles inactive.
     * @param event
     */
    function subCategoryButtonClick(event) {
        event.preventDefault();
        displayCategoryForm();
        populateFieldsForSubCategory(categoryState.getSelectedCategoryId());
        categoryState.setPOSTMethod();
        document.getElementById('parent-category-input').disabled = true;
        inactivateTitles();
        hideElement(document.getElementById('delete-button'));
    }

    /**
     * Deletes the elements inside the categoryTreeContainer and
     * constructs a new category tree inside the categoryTreeContainer.
     * @param categories
     */
    function regenerateCategoryTree(categories) {
        let categoryTreeContainer = document.getElementById('category-tree-container');
        categoryTreeContainer.innerHTML = "";

        createCategoryTree(categories, categoryTreeContainer);
    }

    /**
     * Validates form data and submits either a POST or a PUT request to the server.
     * @param event
     */
    async function submitButtonClick(event) {
        event.preventDefault();

        let title = document.getElementById('category-title-input').value;
        let code = document.getElementById('category-code-input').value;
        let description = document.getElementById('category-description').value;
        let parentId = document.getElementById('parent-category-input').value;

        if (validateCategoryFormInputs(title, code, description)) {
            let response = await AJAX.sendJSONRequest('/admin/categories', categoryState.getMethod(), {
                id: categoryState.getSelectedCategoryId(),
                title: title,
                code: code,
                parentId: parentId,
                description: description
            });

            if (response.ok) {
                let categories = await response.json();
                reinitializeElements(categories);
                return;
            }

            let error = await response.json();
            alert(error.errorMessage);
        }
    }

    /**
     * Prompts the user to see if they want to delete the selected category.
     * If the answer is affirmative, sends a DELETE request to the server.
     * @param event
     * @returns {Promise<void>}
     */
    async function deleteButtonClick(event) {
        event.preventDefault();

        let title = document.getElementById('category-title-input').value;

        if (confirm('Do you want to delete this category. Category Title : ' + title)) {
            let response = await AJAX.sendJSONRequest('/admin/categories', 'DELETE', {
                id: categoryState.getSelectedCategoryId()
            });

            if (response.ok) {
                let categories = await response.json();
                reinitializeElements(categories);
            } else {

                let error = await response.json();
                alert(error.errorMessage);
            }
        }

        categoryState.resetSelectedCategoryId();
    }

    function reinitializeElements(categories) {
        regenerateCategoryTree(categories);
        hideCategoryForm();
        initializeSelect(
            document.getElementById('parent-category-input'),
            constructCategoryArray()
        );
        inactivateTitles();
    }

    window.addEventListener('DOMContentLoaded', () => {
        reinitializeElements(window.categories);

        let deleteButton = document.getElementById('delete-button');
        deleteButton.addEventListener('click', deleteButtonClick);

        let cancelButton = document.getElementById('cancel-button');
        cancelButton.addEventListener('click', function (event) {
            event.preventDefault();
            hideCategoryForm();
            categoryState.resetSelectedCategoryId();
            inactivateTitles();
        });

        let submitButton = document.getElementById('submit-button');
        submitButton.addEventListener('click', submitButtonClick);

        let rootCategoryButton = document.getElementById('root-category-button');
        rootCategoryButton.addEventListener('click', rootCategoryButtonClick);

        let subCategoryButton = document.getElementById('subcategory-button');
        subCategoryButton.addEventListener('click', subCategoryButtonClick);
    });
})();