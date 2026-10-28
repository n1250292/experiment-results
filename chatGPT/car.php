<?php
require_once 'includes/db.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND is_published = 1");
$stmt->execute([$id ?: 0]); $car = $stmt->fetch();
if (!$car) { http_response_code(404); exit('車両が見つかりません。<a href="cars.php">一覧へ戻る</a>'); }
$pageTitle = $car['name'];
require 'includes/header.php';
?>
<p class="breadcrumb"><a href="cars.php">新車一覧</a> / <?= h($car['name']) ?></p>
<div class="detail-grid">
  <div><?php if ($car['image_url']): ?><img class="detail-image" src="<?= h($car['image_url']) ?>" alt="<?= h($car['name']) ?>"><?php else: ?><div class="image-placeholder detail-image">CAR</div><?php endif; ?></div>
  <section class="detail-info"><p class="muted"><?= h($car['maker']) ?> / <?= h($car['grade']) ?></p><h1><?= h($car['name']) ?></h1><p class="price large"><?= yen($car['price']) ?><small>（車両本体価格・税込）</small></p><p><?= nl2br(h($car['description'] ?: $car['short_description'])) ?></p>
  <dl class="specs"><dt>メーカー</dt><dd><?= h($car['maker']) ?></dd><dt>車名</dt><dd><?= h($car['name']) ?></dd><dt>グレード</dt><dd><?= h($car['grade']) ?></dd><dt>在庫目安</dt><dd><?= (int)$car['stock'] > 0 ? '在庫あり（要確認）' : '取り寄せ・納期要確認' ?></dd></dl>
  <form action="cart.php" method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="car_id" value="<?= (int)$car['id'] ?>"><button class="button wide" type="submit">カートに入れる</button></form>
  <p class="fine-print">表示価格は車両本体価格です。税金・保険料・登録費用・オプション等は含まれない場合があります。正式な見積もりは販売店にご確認ください。</p>
  </section>
</div>
<?php require 'includes/footer.php'; ?>
