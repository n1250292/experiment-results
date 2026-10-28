<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'logout') { unset($_SESSION['admin_id'], $_SESSION['admin_name']); session_regenerate_id(true); header('Location: login.php'); exit; }
    if ($action === 'toggle' && ($id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT))) {
        $stmt = $pdo->prepare("UPDATE cars SET is_published = IF(is_published = 1, 0, 1) WHERE id = ?"); $stmt->execute([$id]);
    }
    header('Location: index.php'); exit;
}
$cars = $pdo->query("SELECT id, maker, name, grade, price, stock, is_published FROM cars ORDER BY created_at DESC")->fetchAll();
$orders = $pdo->query("SELECT id, order_no, customer_name, email, phone, total_price, status, created_at FROM orders ORDER BY id DESC LIMIT 30")->fetchAll();
$pageTitle = '管理画面'; require '../includes/header.php';
?>
<div class="admin-heading"><h1>管理画面</h1><form method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="logout"><button class="button secondary">ログアウト</button></form></div>
<p><a class="button" href="car_edit.php">＋ 車両を新規登録</a></p>
<h2>車両管理</h2><div class="table-wrap"><table><thead><tr><th>車名</th><th>価格</th><th>在庫</th><th>公開</th><th>操作</th></tr></thead><tbody>
<?php foreach ($cars as $car): ?><tr><td><?= h($car['maker'].' '.$car['name'].' '.$car['grade']) ?></td><td><?= yen($car['price']) ?></td><td><?= (int)$car['stock'] ?></td><td><?= $car['is_published'] ? '公開中' : '非公開' ?></td><td><a href="car_edit.php?id=<?= (int)$car['id'] ?>">編集</a> <form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="car_id" value="<?= (int)$car['id'] ?>"><button class="text-button"><?= $car['is_published'] ? '非公開にする' : '公開する' ?></button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<h2>最近の申し込み（最新30件）</h2><div class="table-wrap"><table><thead><tr><th>受付番号</th><th>氏名</th><th>連絡先</th><th>合計</th><th>状態</th><th>日時</th></tr></thead><tbody>
<?php foreach ($orders as $o): ?><tr><td><?= h($o['order_no']) ?></td><td><?= h($o['customer_name']) ?></td><td><?= h($o['email']) ?><br><?= h($o['phone']) ?></td><td><?= yen($o['total_price']) ?></td><td><?= h($o['status']) ?></td><td><?= h($o['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require '../includes/footer.php'; ?>
