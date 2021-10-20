<?php

use Andrijaj\DemoProject\Entities\ProductEntity;

?>
<div class="button-container">
    <a href="/admin/products/create">
        <button class="button inline">Add new product</button>
    </a>
    <button class="button inline danger" id="delete-selected">Delete selected</button>
    <button class="button inline" id="enable-selected">Enable selected</button>
    <button class="button inline" id="disable-selected">Disable selected</button>
</div>
<div class="table">
    <div class="table-row table-header">
        <div class="table-cell ">Selected</div>
        <div class="table-cell">Title</div>
        <div class="table-cell">SKU</div>
        <div class="table-cell">Brand</div>
        <div class="table-cell">Category</div>
        <div class="table-cell">Short description</div>
        <div class="table-cell">Price</div>
        <div class="table-cell shrink">Enabled</div>
        <div class="table-cell shrink"></div>
        <div class="table-cell shrink"></div>
    </div>
    <?php /** @var array $products */ ?>
    <?php foreach ($products as $product): ?>
        <?php /** @var ProductEntity $product */ ?>

        <div class="table-row">
            <div class="table-cell "><input type="checkbox" name="<?= $product->SKU ?>" class="select-checkbox"></div>
            <div class="table-cell"><?= $product->title ?></div>
            <div class="table-cell"><?= $product->SKU ?></div>
            <div class="table-cell"><?= $product->brand ?></div>
            <div class="table-cell"><?= $product->categoryTitle ?></div>
            <div class="table-cell"><?= $product->shortDescription ?></div>
            <div class="table-cell"><?= $product->price ?></div>
            <div class="table-cell shrink"><input type="checkbox" name="enabled" <?= ($product->enabled ? 'checked' : '') ?>
                                           disabled></div>
            <div class="table-cell table-cell-button shrink">
                <a href="/admin/products/<?= $product->SKU ?>">
                    <button class="button">&#9998;</button>
                </a>
            </div>
            <div class="table-cell table-cell-button shrink">
                <button class="button danger delete-button" sku="<?= $product->SKU ?>"> &#128465;</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../templates/pagination.php' ?>