<?php
require_once 'config.php';
$page_title = 'カート';
require_once 'includes/header.php';

$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['qty'] as $pid => $qty) {
        $pid = (int)$pid;
        $qty = max(0, (int)$qty);
        if ($qty === 0) {
            unset($cart[$pid]);
        } else {
            $cart[$pid] = $qty;
        }
    }
    $_SESSION['cart'] = $cart;
    header('Location: cart.php');
    exit;
}

if (empty($cart)) {
    echo '<p>カートは空です。</p>';
    require_once 'includes/footer.php';
    exit;
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

$total = 0;
?>

<h2>カート</h2>
<form method="post">
  <table>
    <tr>
      <th>車種</th><th>メーカー</th><th>価格</th><th>数量</th><th>小計</th>
    </tr>
    <?php foreach ($products as $p): 
      $pid = $p['id'];
      $qty = $cart[$pid];
      $subtotal = $p['price'] * $qty;
      $total += $subtotal;
    ?>
      <tr>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['maker']) ?></td>
        <td><?= number_format($p['price']) ?> 円</td>
        <td>
          <input type="number" name="qty[<?= $pid ?>]" value="<?= $qty ?>" min="0">
        </td>
        <td><?= number_format($subtotal) ?> 円</td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p>合計: <?= number_format($total) ?> 円</p>
  <button type="submit">数量を更新</button>
</form>

<p><a href="checkout.php">購入手続きへ進む</a></p>

<?php require_once 'includes/footer.php'; ?>