<?php
/**
 * @var string $categories
 * @var string $view
 */

?>
<div class="admin-category-container">
    <div class="category-tree">
        <div id="category-tree-container">
            <!-- Category tree content is dynamically render via JavaScript -->
        </div>
        <div class="category-management-container">
            <button class="button" id="root-category-button">Add root category</button>
            <button class="button" id="subcategory-button">Add subcategory</button>
        </div>
    </div>

    <div class="category-form-container shadow not-displayed">
        <h2 class="category-form-title">Selected Category</h2>
        <form class="category-form">
            <label for="category-title-input">Title</label>
            <input type="text" name="title" id="category-title-input" required>
            <label for="parent-category-input">Parent Category</label>
            <select name="parent-category" id="parent-category-input" required></select>
            <label for="category-code-input">Code</label>
            <input type="text" name="code" id="category-code-input" required>
            <label for="category-description">Description</label>
            <textarea id="category-description" required></textarea>
            <button class="button danger" id="delete-button">Delete</button>
            <div class="form-button-container">
                <button class="button cancel" id="cancel-button">Cancel</button>
                <button class="button" id="submit-button">Save</button>
            </div>
        </form>
    </div>
</div>
<script>
    window.categories = <?= $categories?>
</script>
<script src="../../public/js/categorySelect.js"></script>