<?php
require __DIR__ . '/../includes/bootstrap.php'; require_admin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
verify_csrf();$id=(int)($_POST['id']??0);$s=db()->prepare('SELECT is_active FROM sick_leaves WHERE id=?');$s->execute([$id]);$current=$s->fetchColumn();if($current===false){http_response_code(404);exit('السجل غير موجود.');}$new=$current?0:1;db()->prepare('UPDATE sick_leaves SET is_active=? WHERE id=?')->execute([$new,$id]);audit($new?'activate_record':'deactivate_record',$id);$_SESSION['flash']=$new?'تم تفعيل السجل.':'تم تعطيل السجل.';header('Location: '.base_url('/admin/index.php'));exit;
