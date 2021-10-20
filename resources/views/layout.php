<?php
/**
 * @var string $title Page title
 * @var string $view The view that the page should render
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" type="text/css" href="../../public/css/main.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/productCard.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/layout.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/navigation.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/<?php echo $view ?>.css">
</head>
<body>
<div class="container">
    <div class="title-container">
        <a href="/"><h1 class="shop-title">Demo Shop</h1></a>
    </div>
    <div class="body-container">
        <div class="nav-container">
            <?php include __DIR__ . '/../templates/navigation.php' ?>
        </div>
        <div class="content">
            <?php include $view . '.php' ?>
        </div>
    </div>
</div>
<script src="../../public/js/createElement.js"></script>
<script src="../../public/js/extractCategoryCodeFromURI.js"></script>
<?php if (file_exists(__DIR__ . "/../../public/js/{$view}.js")): ?>
    <script src="../../public/js/<?= $view ?>.js"></script>
<?php endif; ?>
</body>
</html>
