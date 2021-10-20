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
    <?php include $view . '.php' ?>
</div>
</body>
</html>