<?php
/** @var ProductEntity $product */

use Andrijaj\DemoProject\Entities\ProductEntity;

?>
<div class="product-container">
    <img class="product-image shadow" src="<?= $product->image ?>" alt="product image">
    <div class="product-details">
        <h3 class="product-SKU"><?= $product->SKU ?></h3>
        <h1 class="product-title"><?= $product->title ?></h1>
        <h3 class="product-brand"><?= $product->brand ?></h3>
        <p class="product-short-description"><?= $product->shortDescription ?></p>
        <p class="product-description"><?= $product->description ?></p>
        <h2 class="product-price">Price: <?= $product->price ?> $</h2>
    </div>
</div>
