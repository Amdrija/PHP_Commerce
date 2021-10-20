<?php
/**
 * @var string $view
 * @var ProductEntity|null $product
 * @var string $categories
 * @var string|null $error
 */

use Andrijaj\DemoProject\Entities\ProductEntity;

?>

<?php if (!empty($error)): ?>
    <div class="alert-container">
        <div class="alert-message"><?= $error ?></div>
        <div class="alert-button">×</div>
    </div>
<?php endif; ?>

<div class="admin-product-edit-container">
    <h1 class="product-details-title">Product Details</h1>
    <form method="POST" class="product-form" enctype="multipart/form-data">
        <div class="left-column">
            <div class="input-row-container">
                <label for="product-input-SKU">SKU:</label>
                <input type="text" name="SKU" id="product-input-SKU"
                       required <?= isset($product) ? "value=\"{$product->SKU}\"" : "" ?>>
            </div>
            <div class="input-row-container">
                <label for="product-input-title">Title:</label>
                <input type="text" name="title" id="product-input-title"
                       required <?= isset($product) ? "value=\"{$product->title}\"" : "" ?>>
            </div>
            <div class="input-row-container">
                <label for="product-input-brand">Brand:</label>
                <input type="text" name="brand" id="product-input-brand"
                       required <?= isset($product) ? "value=\"{$product->brand}\"" : "" ?>>
            </div>
            <div class="input-row-container">
                <label for="product-category-input">Category</label>
                <select name="categoryId" id="product-category-input"
                        required <?= isset($product) ? "value=\"{$product->categoryId}\"" : "" ?>></select>
            </div>
            <div class="input-row-container">
                <label for="product-input-price">Price:</label>
                <input type="number" name="price" id="product-input-price"
                       required <?= isset($product) ? "value=\"{$product->price}\"" : "" ?>>
            </div>
            <div class="input-row-container textarea-container">
                <label for="product-input-short-description">Short description:</label>
                <textarea name="short-description" id="product-input-short-description"
                          required><?= isset($product) ? "{$product->shortDescription}" : "" ?></textarea>
            </div>
            <div class="input-row-container textarea-container">
                <label for="product-input-long-description">Long description:</label>
                <textarea name="long-description" id="product-input-long-description"
                          required><?= isset($product) ? "{$product->description}" : "" ?></textarea>
            </div>
            <div class="input-row-container">
                <div class="checkbox-container">
                    <input type="checkbox" name="enabled"
                           id="product-input-enabled" <?= isset($product) && $product->enabled ? "checked" : "" ?>>
                    <label for="product-input-enabled">Enabled in shop</label>
                </div>
            </div>
            <div class="input-row-container">
                <div class="checkbox-container">
                    <input type="checkbox" name="featured"
                           id="product-input-featured" <?= isset($product) && $product->featured ? "checked" : "" ?>>
                    <label for="product-input-featured">Featured</label>
                </div>
            </div>
        </div>
        <div class="right-column">
            <div class="input-row-container image-container">
                <label for="image">Image:</label>
                <img id="image"
                     src="<?= isset($product) ? $product->image : "https://via.placeholder.com/600" ?>"
                     alt="product image">
            </div>
            <label for="product-input-image" class="button">Upload</label>
            <input type="file" name="image" id="product-input-image" accept="image/jpeg, image/png"
                <?= isset($product) ? "" : "required" ?>>
            <input type="submit" value="Save" class="button">
        </div>
    </form>
</div>
<script>
    //this is needed for populating the category select
    window.categories = <?= $categories?>;
    <?php if($product !== null && $product->categoryId !== null):?>
    window.productCategoryId = <?= $product->categoryId?>;
    <?php endif;?>
</script>
<script src="../../public/js/categorySelect.js"></script>
<script src="../../public/js/alert.js"></script>

