<?php
require_once 'config.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    exit('カートが空です。');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || $email === '') {
        $error = '氏名とメールアドレスは必須です。';
    } else {
        // 商品情報取得
        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();

        $total = 0;
        foreach ($products as $p) {
            $total += $p['price'] * $cart[$p['id']];
        }

        $pdo->beginTransaction();
        try {
            // orders
            $stmt = $pdo->prepare(
                "INSERT INTO orders (customer_name, customer_email, customer_phone, total)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $phone, $total]);
            $order_id = $pdo->lastInsertId();

            // order_items & 在庫更新
            $itemStmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price)
                 VALUES (?, ?, ?, ?)"
            );
            $stockStmt = $pdo->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
            );

            foreach ($products as $p) {
                $pid = $p['id'];
                $qty = $cart[$pid];
                $itemStmt->execute([$order_id, $pid, $qty, $p['price']]);
                $stockStmt->execute([$qty, $pid, $qty]);
            }

            $pdo->commit();
            $_SESSION['cart'] = [];
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = '注文処理中にエラーが発生しました。';
        }
    }
}

$page_title = '購入手続き';
require_once 'includes/header.php';
?>

<h2>購入手続き</h2>

<?php if (!empty($success)): ?>
  <p>ご注文ありがとうございました。確認メールは別途送信されます（メール送信処理は別途実装）。</p>
<?php else: ?>
  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <div>
      <label>氏名: <input type="text" name="name" required></label>
    </div>
    <div>
      <label>メールアドレス: <input type="email" name="email" required></label>
    </div>
    <div>
      <label>電話番号: <input type="text" name="phone"></label>
    </div>
    <button type="submit">注文を確定する</button>
  </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>