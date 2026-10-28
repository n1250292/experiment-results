<?php
require_once 'includes/db.php';
$pageTitle = '新車一覧';
$q = trim($_GET['q'] ?? '');
$maker = trim($_GET['maker'] ?? '');
$min = filter_input(INPUT_GET, 'min', FILTER_VALIDATE_INT);
$max = filter_input(INPUT_GET, 'max', FILTER_VALIDATE_INT);
$sql = "SELECT id, maker, name, grade, price, image_url, short_description FROM cars WHERE is_published = 1";
$params = [];
if ($q !== '') { $sql .= " AND (name LIKE ? OR maker LIKE ? OR grade LIKE ?)"; $like = "%$q%"; array_push($params, $like, $like, $like); }
if ($maker !== '') { $sql .= " AND maker = ?"; $params[] = $maker; }
if ($min !== false && $min !== null) { $sql .= " AND price >= ?"; $params[] = $min; }
if ($max !== false && $max !== null) { $sql .= " AND price <= ?"; $params[] = $max; }
$sql .= " ORDER BY price ASC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $cars = $stmt->fetchAll();
$makers = $pdo->query("SELECT DISTINCT maker FROM cars WHERE is_published = 1 ORDER BY maker")->fetchAll(PDO::FETCH_COLUMN);
require 'includes/header.php';
?>
<h1>新車を探す</h1>
<form class="search-panel" method="get">
  <label>キーワード<input name="q" value="<?= h($q) ?>" placeholder="車名・メーカー・グレード"></label>
  <label>メーカー<select name="maker"><option value="">すべて</option><?php foreach ($makers as $m): ?><option value="<?= h($m) ?>" <?= $maker === $m ? 'selected' : '' ?>><?= h($m) ?></option><?php endforeach; ?></select></label>
  <label>価格下限（円）<input type="number" name="min" min="0" value="<?= h($_GET['min'] ?? '') ?>"></label>
  <label>価格上限（円）<input type="number" name="max" min="0" value="<?= h($_GET['max'] ?? '') ?>"></label>
  <button class="button" type="submit">検索する</button>
</form>
<p class="muted"><?= count($cars) ?> 件の車両</p>
<div class="car-grid">
<?php foreach ($cars as $car): ?><article class="car-card"><a href="car.php?id=<?= (int)$car['id'] ?>">
<?php if ($car['image_url']): ?><img src="<?= h($car['image_url']) ?>" alt="<?= h($car['name']) ?>"><?php else: ?><div class="image-placeholder">CAR</div><?php endif; ?>
<div class="car-card-body"><p class="muted"><?= h($car['maker']) ?> / <?= h($car['grade']) ?></p><h3><?= h($car['name']) ?></h3><p class="price"><?= yen($car['price']) ?></p><p><?= h($car['short_description']) ?></p></div></a></article><?php endforeach; ?>
</div>
<?php if (!$cars): ?><p class="notice">条件に一致する車両はありません。</p><?php endif; ?>
<?php require 'includes/footer.php'; ?>
