<?php
$config = require __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? $config['site_name'];
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($pageTitle) ?> | <?= h($config['site_name']) ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
  <a class="logo" href="index.php">AUTO <span>SELECT</span></a>
  <nav><a href="cars.php">新車を探す</a><a href="cart.php">カート (<?= cart_count() ?>)</a></nav>
</header>
<main class="container">
