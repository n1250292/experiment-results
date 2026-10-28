<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$car = ['maker'=>'','name'=>'','grade'=>'','price'=>'','stock'=>0,'image_url'=>'','short_description'=>'','description'=>'','is_published'=>1];
if ($id) { $stmt=$pdo->prepare("SELECT * FROM cars WHERE id=?"); $stmt->execute([$id]); $found=$stmt->fetch(); if (!$found) exit('車両が見つかりません。'); $car=$found; }
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $maker=trim($_POST['maker']??''); $name=trim($_POST['name']??''); $grade=trim($_POST['grade']??'');
    $price=filter_input(INPUT_POST,'price',FILTER_VALIDATE_INT); $stock=filter_input(INPUT_POST,'stock',FILTER_VALIDATE_INT);
    $image=trim($_POST['image_url']??''); $short=trim($_POST['short_description']??''); $desc=trim($_POST['description']??'');
    $published=isset($_POST['is_published'])?1:0;
    if (!$maker || !$name || !$grade || $price===false || $price===null || $price<0 || $stock===false || $stock===null || $stock<0) $error='必須項目と価格・在庫数を確認してください。';
    elseif ($image!=='' && !filter_var($image,FILTER_VALIDATE_URL)) $error='画像URLは正しいURLで入力してください。';
    else {
        if ($id) { $stmt=$pdo->prepare("UPDATE cars SET maker=?,name=?,grade=?,price=?,stock=?,image_url=?,short_description=?,description=?,is_published=? WHERE id=?"); $stmt->execute([$maker,$name,$grade,$price,$stock,$image,$short,$desc,$published,$id]); }
        else { $stmt=$pdo->prepare("INSERT INTO cars (maker,name,grade,price,stock,image_url,short_description,description,is_published,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())"); $stmt->execute([$maker,$name,$grade,$price,$stock,$image,$short,$desc,$published]); }
        header('Location: index.php'); exit;
    }
}
$pageTitle = $id?'車両編集':'車両登録'; require '../includes/header.php';
?>
<h1><?= $id?'車両編集':'車両登録' ?></h1><?php if($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<form class="form-panel" method="post"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>メーカー<input name="maker" required maxlength="80" value="<?= h($_POST['maker']??$car['maker']) ?>"></label>
<label>車名<input name="name" required maxlength="120" value="<?= h($_POST['name']??$car['name']) ?>"></label>
<label>グレード<input name="grade" required maxlength="120" value="<?= h($_POST['grade']??$car['grade']) ?>"></label>
<label>車両本体価格（税込・円）<input type="number" name="price" required min="0" value="<?= h($_POST['price']??$car['price']) ?>"></label>
<label>在庫数（目安）<input type="number" name="stock" required min="0" value="<?= h($_POST['stock']??$car['stock']) ?>"></label>
<label>画像URL<input type="url" name="image_url" maxlength="500" value="<?= h($_POST['image_url']??$car['image_url']) ?>" placeholder="https://..."></label>
<label>短い紹介文<input name="short_description" maxlength="255" value="<?= h($_POST['short_description']??$car['short_description']) ?>"></label>
<label>詳細説明<textarea name="description" rows="6"><?= h($_POST['description']??$car['description']) ?></textarea></label>
<label class="consent"><input type="checkbox" name="is_published" <?= (($_POST && isset($_POST['is_published'])) || (!$_POST && $car['is_published']))?'checked':'' ?>> サイトに公開する</label>
<button class="button" type="submit">保存する</button> <a href="index.php">戻る</a></form>
<?php require '../includes/footer.php'; ?>
