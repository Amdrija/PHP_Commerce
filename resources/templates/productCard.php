<?php
/** @var ProductEntity $product */

use Andrijaj\DemoProject\Entities\ProductEntity;

?>
<a href="/product/<?= $product->SKU ?>" class="card-container">
    <div class="card-image">
        <img src="<?= $product->image ?>" alt="product image">
    </div>
    <div class="card-body">
        <div class="card-content">
            <h2 class="card-title"><?= $product->title ?></h2>
            <div class="card-description"><?= $product->shortDescription ?></div>
        </div>
        <div class="card-footer">
            <?= $product->price ?>$
        </div>
    </div>
</a>