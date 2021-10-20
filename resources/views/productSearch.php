<?php
/** @var array $products */

/** @var int $productsPerPage */
/** @var string $sortBy */
/** @var string $categories */
/** @var string $selectedCategoryId */
/** @var string $keyword */
/** @var string $minPrice */
/** @var string $maxPrice */
?>
<h2 class="search-title">Search criteria</h2>
<form class="search-box" method="GET">
    <div class="form-input-container">
        <label for="keyword-input">Keyword:</label>
        <input type="text" name="keyword" placeholder="keyword" id="keyword-input" <?= $keyword !== '' ?
            "value=" . $keyword : '' ?>>
    </div>
    <div class="form-input-container">
        <label for="product-category-input">Category: </label>
        <select name="categoryId"
                id="product-category-input" <?= isset($product) ? "value=\"{$product->categoryId}\"" : "" ?>></select>
    </div>
    <div class="form-input-container">
        <label for="max-price-input">Min price: </label>
        <input type="number" min="0" name="minPrice" id="max-price-input" <?= $minPrice !== '' ?
            "value=" . $minPrice : '' ?>>
    </div>
    <div class="form-input-container">
        <label for="max-price-input">Max price: </label>
        <input type="number" min="0" name="maxPrice" id="max-price-input" <?= $maxPrice !== '' ?
            "value=" . $maxPrice : '' ?>>
    </div>
    <label for="products-per-page-select-form" hidden></label>
    <select name="productsPerPage" hidden id="products-per-page-select-form">
        <option value="5" <?= $productsPerPage === 5 ? 'selected' : '' ?>>5</option>
        <option value="20" <?= $productsPerPage === 20 ? 'selected' : '' ?>>20</option>
        <option value="30" <?= $productsPerPage === 30 ? 'selected' : '' ?>>30</option>
        <option value="50" <?= $productsPerPage === 50 ? 'selected' : '' ?>>50</option>
        <option value="100" <?= $productsPerPage === 100 ? 'selected' : '' ?>>100</option>
    </select>
    <input type="submit" class="button" value="🔍" id="search-submit">
</form>
<?php include __DIR__ . '/productList.php' ?>
<?php if (isset($error)): ?>
    <p class="error-message"> <?= $error ?></p>
<?php endif; ?>

<script>

    window.categories = <?= $categories ?>;
    window.selectedCategoryId = '<?= $selectedCategoryId ?>';
</script>
<script src="../../public/js/productList.js"></script>
<script src="../../public/js/categorySelect.js"></script>
