<?php
/** @var array $products */

/** @var int $productsPerPage */
/** @var string $sortBy */
/** @var string $view */
?>
<div class="display-options-container">
    <div class="sort-by-container">
        <label for="sort-by-select">Sort by: </label>
        <select id="sort-by-select" name="sortBy">
            <option value="PRICE_ASC" <?= $sortBy === 'PRICE_ASC' ? 'selected' : '' ?>>Price Ascending</option>
            <option value="PRICE_DESC" <?= $sortBy === 'PRICE_DESC' ? 'selected' : '' ?>>Price Descending</option>
            <option value="TITLE_ASC" <?= $sortBy === 'TITLE_ASC' ? 'selected' : '' ?>>Title Ascending</option>
            <option value="TITLE_DESC" <?= $sortBy === 'TITLE_DESC' ? 'selected' : '' ?>>Title Descending</option>
            <option value="BRAND_ASC" <?= $sortBy === 'BRAND_ASC' ? 'selected' : '' ?>>Brand Ascending</option>
            <option value="BRAND_DESC" <?= $sortBy === 'BRAND_DESC' ? 'selected' : '' ?>>Brand Descending</option>
            <?php if ($view === 'productSearch'): ?>
                <option value="RELEVANCE" <?= $sortBy === 'RELEVANCE' ? 'selected' : '' ?>>Relevance</option>
            <?php endif; ?>
        </select>
    </div>

    <?php include __DIR__ . '/../templates/pagination.php' ?>

    <div class="product-per-page-container">
        <label for="products-per-page-select">Products per page: </label>
        <select id="products-per-page-select" name="productsPerPage">
            <option value="5" <?= $productsPerPage === 5 ? 'selected' : '' ?>>5</option>
            <option value="20" <?= $productsPerPage === 20 ? 'selected' : '' ?>>20</option>
            <option value="30" <?= $productsPerPage === 30 ? 'selected' : '' ?>>30</option>
            <option value="50" <?= $productsPerPage === 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $productsPerPage === 100 ? 'selected' : '' ?>>100</option>
        </select>
    </div>
</div>
<div class="product-container">
    <?php foreach ($products as $product): ?>
        <?php include __DIR__ . '/../templates/productCard.php' ?>
    <?php endforeach; ?>
</div>