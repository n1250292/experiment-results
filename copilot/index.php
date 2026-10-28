<?php
require_once 'config.php';
$page_title = '新車一覧';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<h2>新車一覧</h2>
<div class="product-list">
  <?php foreach ($products as $p): ?>
    <div class="product">
      <h3><?= htmlspecialchars($p['name']) ?></h3>
      <p>メーカー: <?= htmlspecialchars($p['maker']) ?></p>
      <p>価格: <?= number_format($p['price']) ?> 円</p>
      <?php if (!empty($p['image'])): ?>
        <img src="assets/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="240">
      <?php endif; ?>
      <p><a href="product.php?id=<?= $p['id'] ?>">詳細を見る</a></p>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>