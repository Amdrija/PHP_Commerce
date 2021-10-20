<?php
/** @var StatisticsEntity $statistics */

use Andrijaj\DemoProject\Entities\StatisticsEntity;

?>
<div class="dashboard-item">
    <label for="category-count">Categories count: </label>
    <input type="text" disabled id="category-count" class="dashboard-input"
           value="<?php echo $statistics->getCategoriesCount() ?>">
</div>
<div class="dashboard-item">
    <label for="product-count">Products Count: </label>
    <input type="text" disabled id="product-count" class="dashboard-input"
           value="<?php echo $statistics->getProductsCount() ?>">
</div>
<div class="dashboard-item">
    <label for="view-count">Home page views: </label>
    <input type="text" disabled id="view-count" class="dashboard-input"
           value="<?php echo $statistics->getHomepageViewCount() ?>">
</div>
<div class="dashboard-item">
    <label for="most-viewed-product">Most viewed product: </label>
    <input type="text" disabled id="most-viewed-product" class="dashboard-input"
           value="<?php echo $statistics->getMostViewedProduct() ?>">
</div>
<div class="dashboard-item">
    <label for="product-views">Most viewed product views: </label>
    <input type="text" disabled id="product-views" class="dashboard-input"
           value="<?php echo $statistics->getMostViewedProductViews() ?>">
</div>