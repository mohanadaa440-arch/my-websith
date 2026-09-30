<?php
require __DIR__ . '/includes/bootstrap.php';

$error = '';
$success = '';
if (setup_complete()) {
    $success = 'تم إعداد المشروع مسبقًا. يمكنك الآن فتح الصفحة الرئيسية أو لوحة الإدارة.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if ($username === '' || mb_strlen($username) < 3) {
        $error = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل.';
    } elseif (strlen($password) < 10) {
        $error = 'كلمة المرور يجب أن تكون 10 أحرف على الأقل.';
    } elseif ($password !== $confirm) {
        $error = 'تأكيد كلمة المرور غير مطابق.';
    } else {
        try {
            $server = db(true);
            $dbName = str_replace('`', '``', (string)app_config('db_name'));
            $server->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = db();
            $schema = file_get_contents(__DIR__ . '/database/schema.sql');
            foreach (preg_split('/;\s*(?:\r?\n|$)/', $schema) as $sql) {
                $sql = trim($sql);
                if ($sql !== '') $pdo->exec($sql);
            }
            $count = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
            if ($count > 0) {
                throw new RuntimeException('يوجد حساب مشرف بالفعل.');
            }
            $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            file_put_contents(__DIR__ . '/.setup_complete', date(DATE_ATOM));
            $success = 'تم إنشاء قاعدة البيانات وحساب المشرف بنجاح.';
        } catch (Throwable $e) {
            $error = 'تعذر الإعداد: ' . $e->getMessage();
        }
    }
}
?><!doctype html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>إعداد النظام</title><link rel="stylesheet" href="<?=e(base_url('/assets/admin.css'))?>"></head>
<body class="admin-body"><main class="login-card setup-card"><h1>إعداد النظام لأول مرة</h1><p class="muted">هذه الصفحة تنشئ قاعدة MySQL والجداول وحساب المشرف على جهازك.</p>
<?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<?php if ($success): ?><div class="alert success"><?=e($success)?></div><div class="actions"><a class="btn" href="<?=e(base_url('/'))?>">الصفحة الرئيسية</a><a class="btn secondary" href="<?=e(base_url('/admin/login.php'))?>">دخول المشرف</a></div><?php else: ?>
<form method="post" class="stack"><label>اسم مستخدم المشرف<input name="username" required minlength="3" autocomplete="username"></label><label>كلمة المرور<input name="password" type="password" required minlength="10" autocomplete="new-password"></label><label>تأكيد كلمة المرور<input name="confirm_password" type="password" required minlength="10" autocomplete="new-password"></label><button class="btn" type="submit">إنشاء قاعدة البيانات والحساب</button></form>
<?php endif; ?></main></body></html>
