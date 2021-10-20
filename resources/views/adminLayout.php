<?php
/**
 * @var string $view
 * @var string $title
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" type="text/css" href="../../public/css/main.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/adminLayout.css">
    <link rel="stylesheet" type="text/css" href="../../public/css/<?= $view ?>.css?v=<? time() ?>">
</head>
<body>
<div class="dashboard-container">
    <div class="dashboard-menu">
        <a href="/admin">
            <div class="dashboard-menu-item <?= $view === 'dashboard' ? 'active' : '' ?>">
                <h2>Dashboard</h2>
            </div>
        </a>
        <a href="/admin/categories">
            <div class="dashboard-menu-item <?= $view === 'adminCategories' ? 'active' : '' ?>">
                <h2>Categories</h2>
            </div>
        </a>
        <a href="/admin/products">
            <div class="dashboard-menu-item <?= preg_match('/adminProduct*/', $view) ? 'active' : '' ?>">
                <h2>Products</h2>
            </div>
        </a>
    </div>

    <div class="dashboard shadow">
        <?php include $view . '.php' ?>
    </div>

    <script src="../../public/js/createElement.js"></script>
    <?php if (file_exists(__DIR__ . "/../../public/js/{$view}.js")): ?>
        <script src="../../public/js/<?= $view ?>.js"></script>
    <?php endif; ?>
    <script src="../../public/js/ajax.js"></script>
</body>
</html>
