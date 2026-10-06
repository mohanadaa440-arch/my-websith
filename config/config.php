<?php
$projectRoot = realpath(__DIR__ . '/..') ?: (__DIR__ . '/..');
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ($_SERVER['DOCUMENT_ROOT'] ?? '');

$projectRoot = rtrim(str_replace('\\', '/', $projectRoot), '/');
$documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
$basePath = '';

if ($documentRoot !== '' && strncasecmp($projectRoot, $documentRoot, strlen($documentRoot)) === 0) {
    $relative = trim(substr($projectRoot, strlen($documentRoot)), '/');
    $basePath = $relative === '' ? '' : '/' . $relative;
}

return [
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'seha_local',
    'db_user' => 'root',
    'db_pass' => '',
    // يُكتشف تلقائياً من اسم المجلد داخل htdocs، لذلك يمكن إعادة تسمية المشروع دون كسر الروابط.
    'base_path' => $basePath,
    'session_name' => 'seha_demo_admin',
];
