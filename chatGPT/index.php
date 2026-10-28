<?php
require_once 'includes/db.php';
$pageTitle = '新車を探す';
require 'includes/header.php';
$stmt = $pdo->query("SELECT id, maker, name, grade, price, image_url, short_description FROM cars WHERE is_published = 1 ORDER BY created_at DESC LIMIT 6");
$cars = $stmt->fetchAll();
?>
<section class="hero">
  <div class="hero-copy">
    <p class="eyebrow">YOUR NEXT CAR STARTS HERE</p>
    <h1>あなたにぴったりの<br>新車を見つけよう。</h1>
    <p>気になる車を比較して、購入申し込みまでオンラインで。</p>
    <a class="button" href="cars.php">新車を探す</a>
  </div>
</section>
<div class="section-heading"><div><p class="eyebrow">LINE UP</p><h2>おすすめの新車</h2></div><a href="cars.php">すべて見る →</a></div>
<?php if (!$cars): ?><p class="notice">車両はまだ登録されていません。管理画面から車両を登録してください。</p><?php endif; ?>
<div class="car-grid">
<?php foreach ($cars as $car): ?>
  <article class="car-card">
    <a href="car.php?id=<?= (int)$car['id'] ?>">
      <?php if ($car['image_url']): ?><img src="<?= h($car['image_url']) ?>" alt="<?= h($car['name']) ?>"><?php else: ?><div class="image-placeholder">CAR</div><?php endif; ?>
      <div class="car-card-body"><p class="muted"><?= h($car['maker']) ?> / <?= h($car['grade']) ?></p><h3><?= h($car['name']) ?></h3><p class="price"><?= yen($car['price']) ?><small>（車両本体価格・税込）</small></p><p><?= h($car['short_description']) ?></p></div>
    </a>
  </article>
<?php endforeach; ?>
</div>
<section class="info-band"><h2>購入まで、わかりやすく。</h2><p>車両を選択 → 申し込み → 販売店から内容確認のご連絡</p></section>
<?php require 'includes/footer.php'; ?>
