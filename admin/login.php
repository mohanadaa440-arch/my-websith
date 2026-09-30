<?php
require __DIR__ . '/../includes/bootstrap.php';
if (is_admin()) { header('Location: ' . base_url('/admin/index.php')); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $username=trim($_POST['username']??''); $password=(string)($_POST['password']??'');
    try{
        $stmt=db()->prepare('SELECT id, username, password_hash FROM admins WHERE username=? LIMIT 1'); $stmt->execute([$username]); $admin=$stmt->fetch();
        if($admin && password_verify($password,$admin['password_hash'])){
            session_regenerate_id(true); $_SESSION['admin_id']=(int)$admin['id']; $_SESSION['admin_username']=$admin['username'];
            db()->prepare('UPDATE admins SET last_login_at=NOW() WHERE id=?')->execute([$admin['id']]); audit('login');
            header('Location: '.base_url('/admin/index.php')); exit;
        }
        $error='اسم المستخدم أو كلمة المرور غير صحيحة.';
    }catch(Throwable $e){$error='تعذر الاتصال بقاعدة البيانات. شغّل MySQL ونفذ setup.php أولًا.';}
}
?><!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>دخول المشرف</title><link rel="stylesheet" href="<?=e(base_url('/assets/admin.css'))?>"></head><body class="admin-body"><main class="login-card"><h1>دخول المشرف</h1><p class="muted">لوحة إدارة النموذج التجريبي المحلي.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post" class="stack"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>اسم المستخدم<input name="username" required autocomplete="username"></label><label>كلمة المرور<input type="password" name="password" required autocomplete="current-password"></label><button class="btn" type="submit">تسجيل الدخول</button><a class="btn secondary" href="<?=e(base_url('/'))?>">العودة للموقع</a></form></main></body></html>
