<?php
/**
 * PerformanceLogger - نظام متكامل لتسجيل العمليات وقياس الأداء
 * 
 * @package Logger
 * @version 2.0
 * @license MIT
 */
class PerformanceLogger {
    // إعدادات السجل
    private $logDir;
    private $currentLogFile;
    private $maxFileSize = 10485760; // 10MB
    private $retentionDays = 30; // 30 يوم
    
    // معلومات الطلب
    private $operationType;
    private $requestStartTime;
    
    // تتبع الأداء
    private $timers = [];
    private $performanceStats = [
        'total_operations' => 0,
        'total_time' => 0,
        'slow_operations' => []
    ];

    public function __construct() {
        $this->requestStartTime = microtime(true);
        $this->initializeLogDirectory();
        $this->setOperationType();
        $this->registerHandlers();
        $this->logRequest();
    }

    /**
     * تهيئة مجلد السجلات
     */
    private function initializeLogDirectory() {
        $this->logDir = __DIR__ . '/logs/' . date('Y') . '/' . date('Y-m');
        
        if (!is_dir($this->logDir)) {
            if (!mkdir($this->logDir, 0755, true)) {
                throw new RuntimeException("Failed to create log directory: {$this->logDir}");
            }
        }
        
        $this->currentLogFile = $this->logDir . '/log-' . date('Y-m-d') . '.log';
        $this->rotateLogIfNeeded();
    }

    /**
     * تدوير ملف السجل عند الوصول للحجم الأقصى
     */
    private function rotateLogIfNeeded() {
        if (file_exists($this->currentLogFile) && 
            filesize($this->currentLogFile) >= $this->maxFileSize) {
            $backupFile = $this->logDir . '/log-' . date('Y-m-d') . '-' . time() . '.log';
            rename($this->currentLogFile, $backupFile);
        }
        
        $this->cleanOldLogs();
    }

    /**
     * تنظيف السجلات القديمة
     */
    private function cleanOldLogs() {
        $files = glob($this->logDir . '/log-*.log');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) >= ($this->retentionDays * 86400)) {
                unlink($file);
            }
        }
    }

    /**
     * تحديد نوع العملية
     */
    private function setOperationType() {
        $this->operationType = $_SERVER['OPERATION_TYPE'] ?? (defined('OPERATION_TYPE') ? OPERATION_TYPE : 'unknown');
    }

    /**
     * تسجيل معالجي الأخطاء
     */
    private function registerHandlers() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * معالجة الأخطاء
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) return false;
        
        $this->writeLog('PHP_ERROR', "$errstr in $errfile on line $errline", [
            'errno' => $errno,
            'type' => $this->getErrorType($errno)
        ]);
        return true;
    }

    /**
     * معالجة الاستثناءات
     */
    public function handleException($exception) {
        $this->writeLog('EXCEPTION', $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->sanitizeTrace($exception->getTrace())
        ]);
    }

    /**
     * معالجة الأخطاء القاتلة
     */
    public function handleShutdown() {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->writeLog('FATAL_ERROR', "{$error['message']} in {$error['file']} on line {$error['line']}");
        }
        
        // تسجيل إحصاءات الأداء عند انتهاء الطلب
        $this->logPerformanceSummary();
    }

    /**
     * بدء قياس وقت العملية
     */
    public function startTimer($operationName) {
        $this->timers[$operationName] = [
            'start' => microtime(true),
            'end' => null,
            'duration' => null,
            'memory_start' => memory_get_usage(),
            'memory_end' => null,
            'memory_usage' => null
        ];
        return $this;
    }

    /**
     * إنهاء قياس وقت العملية
     */
    public function endTimer($operationName) {
        if (isset($this->timers[$operationName])) {
            $timer = &$this->timers[$operationName];
            $timer['end'] = microtime(true);
            $timer['duration'] = $timer['end'] - $timer['start'];
            $timer['memory_end'] = memory_get_usage();
            $timer['memory_usage'] = $timer['memory_end'] - $timer['memory_start'];
            
            // تحديث الإحصاءات
            $this->performanceStats['total_operations']++;
            $this->performanceStats['total_time'] += $timer['duration'];
            
            // تسجيل العمليات البطيئة (أكثر من 500 مللي ثانية)
            if ($timer['duration'] > 0.5) {
                $this->performanceStats['slow_operations'][$operationName] = $timer['duration'];
            }
            
            return $timer['duration'];
        }
        return null;
    }

    /**
     * تسجيل العملية مع الأداء
     */
    public function logOperation($action, $status, $details = [], $timerName = null) {
        $message = strtoupper($action) . ' - ' . strtoupper($status);
        
        if ($timerName && isset($this->timers[$timerName])) {
            $timer = $this->timers[$timerName];
            $details['duration_sec'] = round($timer['duration'] ?? 0, 4);
            $details['memory_usage'] = $this->formatMemory($timer['memory_usage']);
            $details['start_time'] = date('Y-m-d H:i:s', (int)$timer['start']);
        }
        
        $this->writeLog('OPERATION', $message, $this->sanitizeDetails($details));
    }

    /**
     * تسجيل طلب HTTP
     */
    private function logRequest() {
        $sanitizedServer = $this->sanitizeServerData($_SERVER);
        $requestTime = round(microtime(true) - $this->requestStartTime, 4);
        
        $this->writeLog('REQUEST', ($_SERVER['REQUEST_METHOD'] ?? 'CLI') . ' ' . ($_SERVER['REQUEST_URI'] ?? ''), [
            'Operation' => $this->operationType,
            'IP' => $sanitizedServer['REMOTE_ADDR'] ?? 'CLI',
            'UserAgent' => $sanitizedServer['HTTP_USER_AGENT'] ?? 'CLI',
            'ResponseTime' => $requestTime . ' sec',
            'MemoryUsage' => $this->formatMemory(memory_get_usage())
        ]);
    }

    /**
     * تسجيل ملخص الأداء
     */
    private function logPerformanceSummary() {
        $totalTime = round(microtime(true) - $this->requestStartTime, 4);
        $memoryPeak = $this->formatMemory(memory_get_peak_usage());
        
        $this->writeLog('PERFORMANCE', 'Request completed', [
            'TotalOperations' => $this->performanceStats['total_operations'],
            'TotalTime' => $totalTime . ' sec',
            'MemoryPeak' => $memoryPeak,
            'SlowOperations' => $this->performanceStats['slow_operations'] ?: 'None'
        ]);
    }

    /**
     * كتابة السجل
     */
    private function writeLog($type, $message, $context = []) {
        $time = date('Y-m-d H:i:s');
        $contextStr = $context ? ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
        $logEntry = "[$time] [$type] $message$contextStr" . PHP_EOL;
        
        $result = @file_put_contents($this->currentLogFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($result === false) {
            error_log("Failed to write to log file: {$this->currentLogFile}");
        }
    }

    /**
     * تنظيف بيانات السيرفر
     */
    private function sanitizeServerData($server) {
        $sanitized = [];
        $allowedKeys = [
            'REMOTE_ADDR', 'HTTP_USER_AGENT', 'REQUEST_METHOD', 
            'REQUEST_URI', 'HTTP_REFERER'
        ];
        
        foreach ($allowedKeys as $key) {
            if (isset($server[$key])) {
                $sanitized[$key] = $server[$key];
            }
        }
        
        return $sanitized;
    }

    /**
     * تنظيف البيانات الحساسة
     */
    private function sanitizeDetails($details) {
        $sensitiveKeys = ['password', 'token', 'secret', 'credit_card', 'api_key'];
        
        foreach ($details as $key => $value) {
            $lowerKey = strtolower($key);
            foreach ($sensitiveKeys as $sensitive) {
                if (strpos($lowerKey, $sensitive) !== false) {
                    $details[$key] = '***REDACTED***';
                    break;
                }
            }
        }
        
        return $details;
    }

    /**
     * تنظيف الـ Trace
     */
    private function sanitizeTrace($trace) {
        $cleanTrace = [];
        foreach (array_slice($trace, 0, 5) as $item) {
            $cleanItem = [
                'file' => $item['file'] ?? null,
                'line' => $item['line'] ?? null,
                'function' => $item['function'] ?? null,
                'class' => $item['class'] ?? null
            ];
            $cleanTrace[] = $cleanItem;
        }
        return $cleanTrace;
    }

    /**
     * تنسيق حجم الذاكرة
     */
    private function formatMemory($bytes) {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }

    /**
     * الحصول على نوع الخطأ
     */
    private function getErrorType($errno) {
        $types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED'
        ];
        
        return $types[$errno] ?? 'UNKNOWN';
    }

    /**
     * الحصول على تقرير الأداء
     */
    public function getPerformanceReport() {
        $report = [
            'total_time' => round(microtime(true) - $this->requestStartTime, 4) . ' sec',
            'operations' => []
        ];
        
        foreach ($this->timers as $name => $timer) {
            $report['operations'][$name] = [
                'duration' => round($timer['duration'] ?? 0, 4) . ' sec',
                'memory_usage' => $this->formatMemory($timer['memory_usage'] ?? 0),
                'start' => date('Y-m-d H:i:s', (int)$timer['start']),
                'end' => date('Y-m-d H:i:s', (int)$timer['end'])
            ];
        }
        
        return $report;
    }
}
