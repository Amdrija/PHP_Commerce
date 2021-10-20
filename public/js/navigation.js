(function () {
    /**
     * Makes all category titles inactive.
     */
    function inactivateTitles() {
        let categoryTitles = document.getElementsByClassName('category-title-container');
        for (let title of categoryTitles) {
            title.classList.remove('active');
        }
    }

    /**
     * All category titles become inactive and activates the clicked title.
     */
    function toggleActiveTitle(titleContainer) {
        inactivateTitles();
        titleContainer.classList.add('active');
    }

    function toggleSubcategoryContainer(subcategoryContainer) {
        if (subcategoryContainer.classList.contains('collapsed')) {
            hideSubcategoryContainer(subcategoryContainer);
        } else {
            showSubcategoryContainer(subcategoryContainer);
        }
    }

    function showSubcategoryContainer(subcategoryContainer) {
        subcategoryContainer.classList.add('collapsed');
    }

    function hideSubcategoryContainer(subcategoryContainer) {
        subcategoryContainer.classList.remove('collapsed');
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
            categoryTitleContainer.addEventListener('click', categoryTitleClick);

            let categoryTitle = createElement('h3', ['category-title'], category.title);

            categoryTitleContainer.appendChild(categoryTitle);
            categoryContainer.appendChild(categoryTitleContainer);

            if (category.subcategories.length > 0) {
                let subcategoryContainer = createElement('div', ['subcategory-container']);

                // This is for constructing the All Laptops 'subcategory' if we have a Laptops category.
                let allCategoryContainer = createElement('div', ['category-container']);
                allCategoryContainer.categoryData = category;

                let allCategoryTitleContainer = createElement('div', ['category-title-container']);
                allCategoryTitleContainer.addEventListener('click', categoryTitleClick);

                let allCategoryTitle = createElement('div', ['category-title'], 'All ' + category.title);

                allCategoryTitleContainer.appendChild(allCategoryTitle);
                allCategoryContainer.appendChild(allCategoryTitleContainer);
                subcategoryContainer.appendChild(allCategoryContainer);

                createCategoryTree(category.subcategories, subcategoryContainer);
                categoryContainer.appendChild(subcategoryContainer);
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

    function goToCategoryProductsPage(categoryCode) {
        let path = '/category/' + categoryCode;
        window.location.href = path;
    }

    /**
     * Activates the title that was clicked.
     * Displays the category form.
     * Sets the categoryState.selectedCategoryId to the selects category's id.
     * Sets the form submit method to PUT.
     * @param event
     */
    function categoryTitleClick(event) {
        let categoryTitleContainer = event.target;
        if (event.target.classList.contains('category-title')) {
            categoryTitleContainer = event.target.parentElement;
        }
        toggleActiveTitle(categoryTitleContainer);

        let subcategoryContainers = categoryTitleContainer.parentElement.getElementsByClassName('subcategory-container');

        //if there are subcategories, then we want to display them
        //otherwise, we want to redirect the browser to the category products page.
        if (subcategoryContainers.length > 0) {
            toggleSubcategoryContainer(subcategoryContainers[0]);
        } else {
            goToCategoryProductsPage(categoryTitleContainer.parentElement.categoryData.code);
        }
    }

    /**
     * Recursively checks if the categoryContainer's corresponding category matches the categoryCode.
     * If it does match, sets the categoryContainer's title to be active, returns true
     * and recursively expands all parent subcategoryContainers.
     * @param categoryContainer
     * @param categoryCode
     * @returns {boolean}
     */
    function expandCategories(categoryContainer, categoryCode) {
        if (categoryContainer.categoryData.code === categoryCode) {
            categoryContainer.getElementsByClassName('category-title-container')[0].classList.add('active');
            return true;
        }

        let subcategoryContainers = categoryContainer.getElementsByClassName('subcategory-container');
        if (subcategoryContainers.length === 0) {
            return false;
        }

        for (let subcategoryContainer of subcategoryContainers[0].children) {
            if (expandCategories(subcategoryContainer, categoryCode)) {
                console.log(subcategoryContainer);
                showSubcategoryContainer(subcategoryContainers[0]);
                return true;
            }
        }
    }

    /**
     * Expands the list of categories so when we go to a specific URI
     * it will expand the categories to match the chosen URI.
     * @param categoryTreeContainer
     */
    function initializeActiveCategory(categoryTreeContainer){
        let categoryCode = extractCategoryCodeFromURI(window.location.href);
        if (categoryCode) {
            for (let categoryContainer of categoryTreeContainer.children) {
                if (expandCategories(categoryContainer, categoryCode)) {
                    break;
                }
            }
        }
    }

    window.addEventListener('DOMContentLoaded', function () {
        createCategoryTree(window.categoryTree, document.getElementById('category-tree-container'));

        initializeActiveCategory(document.getElementById('category-tree-container'));

    });
})();