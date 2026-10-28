<?php
// このファイルは管理者パスワードのハッシュ生成専用です。
// 実行後、表示されたSQLをphpMyAdminで実行し、このファイルは必ず削除してください。
if (PHP_SAPI !== 'cli' && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'], true)) {
    http_response_code(403); exit('ローカル環境から実行してください。');
}
if (PHP_SAPI === 'cli') {
    $username = $argv[1] ?? '';
    $password = $argv[2] ?? '';
} else {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
}
if ((($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') || PHP_SAPI === 'cli') {
    if ($username === '' || strlen($password) < 12) exit('ユーザー名と12文字以上のパスワードが必要です。');
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "INSERT INTO admins (username, password_hash) VALUES ("
        . "'" . str_replace("'", "''", $username) . "', "
        . "'" . $hash . "');";
    exit;
}
?>
<!doctype html><html lang="ja"><meta charset="utf-8"><title>管理者ハッシュ生成</title>
<p>ローカル環境でのみ使用してください。管理者パスワードは12文字以上を設定してください。</p>
<form method="post"><label>ユーザー名 <input name="username" required></label><br><label>パスワード <input type="password" name="password" minlength="12" required></label><br><button>SQLを生成</button></form></html>
