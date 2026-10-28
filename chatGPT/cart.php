<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
    if ($action === 'add' && $id) {
        $check = $pdo->prepare("SELECT id FROM cars WHERE id = ? AND is_published = 1");
        $check->execute([$id]);
        if ($check->fetch()) $_SESSION['cart'][$id] = min(1, (int)($_SESSION['cart'][$id] ?? 0) + 1);
    } elseif ($action === 'remove' && $id) {
        unset($_SESSION['cart'][$id]);
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
    }
    header('Location: cart.php'); exit;
}
$cart = $_SESSION['cart'] ?? [];
$items = []; $total = 0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, maker, name, grade, price, image_url FROM cars WHERE is_published = 1 AND id IN ($marks)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $item) { $items[] = $item; $total += (float)$item['price']; }
}
$pageTitle = 'カート';
require 'includes/header.php';
?>
<h1>ショッピングカート</h1>
<?php if (!$items): ?><p class="notice">カートに車両はありません。</p><a class="button" href="cars.php">新車を探す</a>
<?php else: ?>
<div class="cart-list"><?php foreach ($items as $item): ?><div class="cart-item">
<?php if ($item['image_url']): ?><img src="<?= h($item['image_url']) ?>" alt="<?= h($item['name']) ?>"><?php else: ?><div class="image-placeholder">CAR</div><?php endif; ?>
<div class="cart-item-info"><p class="muted"><?= h($item['maker']) ?> / <?= h($item['grade']) ?></p><h3><?= h($item['name']) ?></h3><strong class="price"><?= yen($item['price']) ?></strong></div>
<form method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="car_id" value="<?= (int)$item['id'] ?>"><button class="button secondary" type="submit">削除</button></form>
</div><?php endforeach; ?></div>
<div class="total-panel"><p>車両本体価格合計（概算）</p><strong class="price large"><?= yen($total) ?></strong><p class="fine-print">税金・登録費用・保険料・オプション等は含まれません。</p><a class="button wide" href="checkout.php">購入申し込みへ進む</a></div>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
