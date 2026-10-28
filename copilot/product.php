<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    exit('商品が見つかりません。');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 0;
    }
    $_SESSION['cart'][$id] += $qty;
    header('Location: cart.php');
    exit;
}

$page_title = $product['name'] . ' | 詳細';
require_once 'includes/header.php';
?>

<h2><?= htmlspecialchars($product['name']) ?></h2>
<p>メーカー: <?= htmlspecialchars($product['maker']) ?></p>
<p>価格: <?= number_format($product['price']) ?> 円</p>
<p>在庫: <?= (int)$product['stock'] ?> 台</p>
<?php if (!empty($product['image'])): ?>
  <img src="assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="320">
<?php endif; ?>
<p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

<form method="post">
  <label>台数:
    <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
  </label>
  <button type="submit">カートに入れる</button>
</form>

<?php require_once 'includes/footer.php'; ?>