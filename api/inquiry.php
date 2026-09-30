<?php
require __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'طريقة الطلب غير مسموحة.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$code = trim((string)($input['code'] ?? ''));
$identity = trim((string)($input['identity'] ?? ''));

if ($code === '' || $identity === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'أدخل رمز الخدمة ورقم الهوية/الإقامة.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = db()->prepare('SELECT service_code, identity_number, patient_name, companion_name, relationship, issue_date, leave_from, leave_to, duration_days, physician_name, physician_specialty FROM sick_leaves WHERE service_code = ? AND identity_number = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$code, $identity]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['ok' => false, 'message' => 'لم يتم العثور على سجل تجريبي مطابق للبيانات المدخلة.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(['ok' => true, 'record' => $row], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'تعذر الاتصال بقاعدة البيانات. تأكد من تشغيل MySQL وإتمام setup.php.'], JSON_UNESCAPED_UNICODE);
}
