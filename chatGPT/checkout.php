<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { header('Location: cart.php'); exit; }
$ids = array_map('intval', array_keys($cart)); $marks = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, maker, name, grade, price FROM cars WHERE is_published = 1 AND id IN ($marks)");
$stmt->execute($ids); $items = $stmt->fetchAll();
if (!$items) { $_SESSION['cart'] = []; header('Location: cart.php'); exit; }
$total = array_sum(array_column($items, 'price')); $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? ''); $postal = trim($_POST['postal_code'] ?? '');
    $address = trim($_POST['address'] ?? ''); $note = trim($_POST['note'] ?? '');
    if ($name === '' || mb_strlen($name) > 100) $errors[] = '氏名を入力してください（100文字以内）。';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = '正しいメールアドレスを入力してください。';
    if ($phone === '' || mb_strlen($phone) > 30) $errors[] = '電話番号を入力してください。';
    if ($address === '' || mb_strlen($address) > 255) $errors[] = '住所を入力してください（255文字以内）。';
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $orderNo = 'AS' . date('YmdHis') . random_int(100,999);
            $stmt = $pdo->prepare("INSERT INTO orders (order_no, customer_name, email, phone, postal_code, address, note, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '新規受付', NOW())");
            $stmt->execute([$orderNo, $name, $email, $phone, $postal, $address, $note, $total]);
            $orderId = (int)$pdo->lastInsertId();
            $line = $pdo->prepare("INSERT INTO order_items (order_id, car_id, car_name, unit_price, quantity) VALUES (?, ?, ?, ?, 1)");
            foreach ($items as $item) $line->execute([$orderId, $item['id'], $item['maker'].' '.$item['name'].' '.$item['grade'], $item['price']]);
            $pdo->commit();
            $_SESSION['cart'] = [];
            $_SESSION['last_order_no'] = $orderNo;
            header('Location: order_complete.php'); exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage()); $errors[] = '申し込みを処理できませんでした。時間をおいて再度お試しください。';
        }
    }
}
$pageTitle = '購入申し込み';
require 'includes/header.php';
?>
<h1>購入申し込み</h1><p>入力内容を送信すると、販売店への購入申し込みとして登録されます。送信時点で売買契約は成立しません。</p>
<?php foreach ($errors as $error): ?><p class="error"><?= h($error) ?></p><?php endforeach; ?>
<div class="checkout-grid"><form method="post" class="form-panel"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>氏名 <span class="required">必須</span><input name="name" maxlength="100" required value="<?= h($_POST['name'] ?? '') ?>"></label>
<label>メールアドレス <span class="required">必須</span><input type="email" name="email" maxlength="254" required value="<?= h($_POST['email'] ?? '') ?>"></label>
<label>電話番号 <span class="required">必須</span><input name="phone" maxlength="30" required value="<?= h($_POST['phone'] ?? '') ?>"></label>
<label>郵便番号<input name="postal_code" maxlength="10" value="<?= h($_POST['postal_code'] ?? '') ?>"></label>
<label>住所 <span class="required">必須</span><input name="address" maxlength="255" required value="<?= h($_POST['address'] ?? '') ?>"></label>
<label>ご質問・ご要望<textarea name="note" rows="4" maxlength="2000"><?= h($_POST['note'] ?? '') ?></textarea></label>
<label class="consent"><input type="checkbox" required> 個人情報の取り扱いと、販売店から連絡があることを確認しました。</label>
<button class="button wide" type="submit">購入を申し込む</button></form>
<aside class="summary-panel"><h2>申し込み内容</h2><?php foreach ($items as $item): ?><p><strong><?= h($item['maker'].' '.$item['name']) ?></strong><br><span class="muted"><?= h($item['grade']) ?></span><br><?= yen($item['price']) ?></p><?php endforeach; ?><hr><p>車両本体価格合計</p><strong class="price large"><?= yen($total) ?></strong><p class="fine-print">諸費用等を含む確定見積もりではありません。</p></aside></div>
<?php require 'includes/footer.php'; ?>
