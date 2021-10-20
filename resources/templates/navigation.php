<?php
/** @var string $categoryTree */

?>

<div class="navigation">
    <form class="search-container" method="get" action="/search">
        <input type="text" name="keyword" placeholder="Search product..." required>
        <input type="submit" class="button cancel" value="&#128269;">
    </form>
    <div id="category-tree-container">
    </div>
</div>
<script>
    window.categoryTree = <?= $categoryTree ?>
</script>
<script src="../../public/js/navigation.js"></script>