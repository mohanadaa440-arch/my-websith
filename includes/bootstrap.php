<?php
$config = require __DIR__ . '/../config/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($config['session_name']);
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Strict',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'path' => $config['base_path'] ?: '/',
    ]);
    session_start();
}

function app_config(?string $key = null) {
    global $config;
    return $key === null ? $config : ($config[$key] ?? null);
}

function base_url(string $path = ''): string {
    $base = rtrim((string)app_config('base_path'), '/');
    $path = '/' . ltrim($path, '/');
    return ($base === '' ? '' : $base) . ($path === '/' ? '/' : $path);
}

function db(bool $withoutDb = false): PDO {
    static $pdo = null;
    if (!$withoutDb && $pdo instanceof PDO) return $pdo;

    $host = app_config('db_host');
    $port = (int)app_config('db_port');
    $name = app_config('db_name');
    $user = app_config('db_user');
    $pass = app_config('db_pass');

    $dsn = $withoutDb
        ? "mysql:host={$host};port={$port};charset=utf8mb4"
        : "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    if (!$withoutDb) $pdo = $conn;
    return $conn;
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    $token = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(419);
        exit('انتهت صلاحية الطلب. أعد تحميل الصفحة وحاول مرة أخرى.');
    }
}

function is_admin(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: ' . base_url('/admin/login.php'));
        exit;
    }
}

function audit(string $action, ?int $recordId = null, array $details = []): void {
    try {
        $stmt = db()->prepare('INSERT INTO audit_logs (admin_id, action, record_id, details, ip_address) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $_SESSION['admin_id'] ?? null,
            $action,
            $recordId,
            $details ? json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (Throwable $e) {
        // لا نوقف العملية الأساسية إذا تعذر تسجيل السجل.
    }
}

function duration_days(string $from, string $to): int {
    $a = new DateTimeImmutable($from);
    $b = new DateTimeImmutable($to);
    return max(1, $a->diff($b)->days + 1);
}

function setup_complete(): bool {
    return file_exists(__DIR__ . '/../.setup_complete');
}
