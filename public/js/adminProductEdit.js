(function () {
    function showUploadedPicture(imageElement, imageInputElement) {
        imageElement.src = URL.createObjectURL(imageInputElement.files[0]);
    }

    window.addEventListener('DOMContentLoaded', function () {
        let imageInputElement = document.getElementById('product-input-image');
        imageInputElement.addEventListener('change', function (event) {
            showUploadedPicture(document.getElementById('image'), event.target);
        });

        let selectElement = document.getElementById('product-category-input');
        initializeCategorySelect(selectElement, window.categories, window.productCategoryId || window.categories[0].id || 0);
    });
})();