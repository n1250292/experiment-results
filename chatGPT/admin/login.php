<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!empty($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
    $stmt->execute([trim($_POST['username'] ?? '')]); $admin = $stmt->fetch();
    if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) {
        session_regenerate_id(true); $_SESSION['admin_id'] = (int)$admin['id']; $_SESSION['admin_name'] = $admin['username'];
        header('Location: index.php'); exit;
    }
    $error = 'ユーザー名またはパスワードが違います。';
}
$pageTitle = '管理者ログイン'; require '../includes/header.php';
?>
<h1>管理者ログイン</h1><?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<form class="form-panel narrow" method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><label>ユーザー名<input name="username" required autocomplete="username"></label><label>パスワード<input type="password" name="password" required autocomplete="current-password"></label><button class="button wide">ログイン</button></form>
<?php require '../includes/footer.php'; ?>
