<?php
// تحديد مسار مجلد السجلات
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$year = date('Y');
$log_dir = $log_dir . "/" . $year;
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$month = date('Y-m');
$log_dir = $log_dir . "/" . $month;
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// دالة تسجيل إلى ملف السجل
function write_log($type, $message, $context = []) {
    global $log_dir;
    $time = date('Y-m-d H:i:s');
    $context_str = $context ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
    $log_entry = "[$time] [$type] $message$context_str" . PHP_EOL;
    $log_file = $log_dir . '/log-' . date('Y-m-d') . '.log';
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// تسجيل الأخطاء الاعتيادية
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return;
    write_log('PHP_ERROR', "$errstr in $errfile on line $errline", ['errno' => $errno]);
    return true;
});

// تسجيل الاستثناءات
set_exception_handler(function($exception) {
    write_log('EXCEPTION', $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});

// تسجيل الأخطاء القاتلة
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        write_log('FATAL_ERROR', "{$error['message']} in {$error['file']} on line {$error['line']}");
    }
});

// تسجيل نوع العملية إن تم تعريفها
$operation = $_SERVER['OPERATION_TYPE'] ?? (defined('OPERATION_TYPE') ? OPERATION_TYPE : 'unknown');

// تسجيل الطلب الحالي
write_log('REQUEST', $_SERVER['REQUEST_METHOD'] . ' ' . ($_SERVER['REQUEST_URI'] ?? ''), [
    'Operation' => $operation,
    'IP' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
    'UserAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
    'Time' => date('H:i:s')
]);

// تسجيل عملية داخلية بشكل نمطي
function log_operation($action, $status, $details = []) {
    $message = strtoupper($action) . ' - ' . strtoupper($status);
    write_log('OPERATION', $message, $details);
}
