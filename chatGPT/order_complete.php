<?php
require_once 'includes/functions.php';
$orderNo = $_SESSION['last_order_no'] ?? null;
unset($_SESSION['last_order_no']);
if (!$orderNo) { header('Location: index.php'); exit; }
$pageTitle = '申し込み完了';
require 'includes/header.php';
?>
<section class="success-panel"><div class="success-mark">✓</div><h1>お申し込みを受け付けました</h1><p>受付番号：<strong><?= h($orderNo) ?></strong></p><p>販売店から内容確認のご連絡を差し上げます。正式な契約・価格・納期は販売店との確認後に確定します。</p><a class="button" href="index.php">トップページへ戻る</a></section>
<?php require 'includes/footer.php'; ?>
