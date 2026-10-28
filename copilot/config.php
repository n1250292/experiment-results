<?php
// ロリポップの管理画面で確認した値を入れてください
$db_host = 'mysql403.phy.lolipop.lan';
$db_name = 'LAA1705340-tamazarasi';
$db_user = 'LAA1705340';
$db_pass = 'Sakito3103';

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}

session_start();