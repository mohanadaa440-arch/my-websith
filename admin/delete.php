<?php
require __DIR__ . '/../includes/bootstrap.php'; require_admin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
verify_csrf();$id=(int)($_POST['id']??0);$s=db()->prepare('SELECT service_code FROM sick_leaves WHERE id=?');$s->execute([$id]);$code=$s->fetchColumn();if($code===false){http_response_code(404);exit('السجل غير موجود.');}audit('delete_record',$id,['service_code'=>$code]);db()->prepare('DELETE FROM sick_leaves WHERE id=?')->execute([$id]);$_SESSION['flash']='تم حذف السجل نهائيًا.';header('Location: '.base_url('/admin/index.php'));exit;
