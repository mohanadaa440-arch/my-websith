<?php
require __DIR__ . '/includes/bootstrap.php';
$error='';$success='';
try{
  $pdo=db();
  $dbName=(string)app_config('db_name');
  $check=$pdo->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='sick_leaves' AND COLUMN_NAME IN ('companion_name','relationship')");
  $check->execute([$dbName]);
  $existing=array_column($check->fetchAll(),'COLUMN_NAME');
  if(!in_array('companion_name',$existing,true)) $pdo->exec("ALTER TABLE sick_leaves ADD COLUMN companion_name VARCHAR(180) NULL AFTER employer_name");
  if(!in_array('relationship',$existing,true)) $pdo->exec("ALTER TABLE sick_leaves ADD COLUMN relationship VARCHAR(120) NULL AFTER companion_name");
  $success='تم تحديث قاعدة البيانات بنجاح. يمكنك الآن استخدام الحقول الجديدة من لوحة الإدارة.';
}catch(Throwable $e){$error='تعذر تحديث قاعدة البيانات: '.$e->getMessage();}
?><!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>تحديث قاعدة البيانات</title><link rel="stylesheet" href="<?=e(base_url('/assets/admin.css'))?>"></head><body class="admin-body"><main class="login-card setup-card"><h1>تحديث قاعدة البيانات</h1><?php if($success):?><div class="alert success"><?=e($success)?></div><div class="actions"><a class="btn" href="<?=e(base_url('/admin/'))?>">لوحة الإدارة</a><a class="btn secondary" href="<?=e(base_url('/'))?>">عرض الموقع</a></div><?php else:?><div class="alert error"><?=e($error)?></div><?php endif;?></main></body></html>
