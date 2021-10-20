<?php
/** @var array $featuredProducts */
?>
<div class="product-container">
    <?php foreach ($featuredProducts as $product): ?>
        <?php include __DIR__ . '/../templates/productCard.php' ?>
    <?php endforeach; ?>
</div>
