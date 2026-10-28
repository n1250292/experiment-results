<!-- includes/header.php -->
<?php if (!isset($page_title)) $page_title = '新車販売ECサイト'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
  <h1>新車販売ECサイト</h1>
  <nav>
    <a href="index.php">トップ</a>
    <a href="cart.php">カート</a>
  </nav>
</header>
<main>