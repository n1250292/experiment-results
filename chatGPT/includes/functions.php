<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function yen($value): string {
    return '¥' . number_format((float)$value);
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('不正なリクエストです。ページを再読み込みしてください。');
    }
}
function cart_count(): int {
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}
