# PHP AutoLogger 🚀

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Stars](https://img.shields.io/github/stars/ismailalbriki/php-autologger?style=social)](https://github.com/ismailalbriki/php-autologger)

A lightweight, feature-rich automatic logging system for PHP applications with built-in performance monitoring.

## ✨ Features

- 📝 Automatic request logging
- ⏱ Performance tracking with execution time and memory usage
- 🔍 Detailed error and exception capturing
- 📊 Operation-level performance analytics
- 🔒 Sensitive data redaction
- 📂 Automatic log rotation and cleanup (10MB max, 30-day retention)
- 📈 Performance bottleneck identification
- 🔄 Zero-config auto-loading support

## 📦 Installation

### Using Composer

```bash
composer require ismailalbriki/php-autologger
```

### Manual Installation

1. Download the `PerformanceLogger.php` file
2. Include it in your project:

```php
require_once 'path/to/PerformanceLogger.php';
```

## 🚀 Quick Start

### Basic Usage

```php
// Initialize the logger
$logger = new PerformanceLogger();

// Track an operation
$logger->startTimer('database_query');
// Your code...
usleep(200000); // Simulate work
$logger->endTimer('database_query');
$logger->logOperation('query', 'success', ['table' => 'users'], 'database_query');
```

### Automatic Loading (Recommended)

#### Option 1: Using php.ini (Production)
```ini
auto_prepend_file = "/full/path/to/PerformanceLogger.php"
```

#### Option 2: Using .htaccess (Apache)
```apache
php_value auto_prepend_file "/full/path/to/PerformanceLogger.php"
```

#### Option 3: Via autoload.php (Advanced)
Create `autoload.php`:
```php
<?php
require_once 'PerformanceLogger.php';

$logger = new PerformanceLogger();

// Auto-detect operation type
if (!defined('OPERATION_TYPE') && !isset($_SERVER['OPERATION_TYPE'])) {
    $_SERVER['OPERATION_TYPE'] = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_FILENAME);
}

// Register shutdown for performance summary
register_shutdown_function(function() use ($logger) {
    $logger->logPerformanceSummary();
});
```
Then reference this file in your `php.ini` or `.htaccess`.

## 📚 Documentation

### Core Methods

| Method | Description |
|--------|-------------|
| `startTimer($name)` | Begin tracking an operation |
| `endTimer($name)` | Stop tracking an operation |
| `logOperation($action, $status, $details, $timerName)` | Log a completed operation |
| `getPerformanceReport()` | Get detailed performance metrics |

### Automatic Logging Captures

- All HTTP requests (method + URI)
- PHP errors/warnings/notices
- Uncaught exceptions with stack traces
- Fatal errors
- Memory usage and peak memory
- Request duration

### Configuration

Extend the class to customize:

```php
class CustomLogger extends PerformanceLogger {
    protected $maxFileSize = 5242880; // 5MB max log size
    protected $retentionDays = 14; // Keep logs for 14 days
    protected $logDir = '/custom/logs/path'; // Custom log directory
}
```

## 🛡 Security Features

- Automatic redaction of sensitive data (passwords, tokens, etc.)
- Secure file permissions (0755)
- Context sanitization for error traces
- Minimal server data collection (only essential headers)
- CLI mode detection

## 🌐 Log File Structure

```
/logs
  /2023               # Year
    /2023-05          # Month
      log-2023-05-15.log       # Today's log
      log-2023-05-15-1234567890.log  # Rotated log
```

Example log entry:
```
[2023-05-15 14:30:00] [REQUEST] POST /api/login 
  | Context: {"Operation":"login","IP":"192.168.1.1","ResponseTime":"0.45 sec"}

[2023-05-15 14:30:00] [OPERATION] DB_QUERY - SUCCESS 
  | Context: {"table":"users","duration_sec":0.12,"memory_usage":"0.8 MB"}
```

## 🛠 Troubleshooting

**Logs not appearing?**
1. Verify file paths are absolute
2. Check directory permissions (755 for dirs, 644 for files)
3. Ensure PHP can write to log directory

**Disable in development:**
```php
if (getenv('APP_ENV') === 'development') {
    $logger->disableLogging();
}
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📜 License

MIT License. See `LICENSE` for details.

## 📬 Contact

Ismail Albriki - [GitHub](https://github.com/ismailalbriki)  
Project Link: [https://github.com/ismailalbriki/php-autologger](https://github.com/ismailalbriki/php-autologger)

---

💡 **Pro Tip**: For best results, combine with error monitoring services and configure log rotation policies matching your storage capacity.
```

Key improvements in this version:

1. **Complete Auto-Loading Instructions** - All three methods with examples
2. **New `autoload.php` Recommendation** - For advanced configuration
3. **Troubleshooting Section** - Common issues and solutions
4. **Enhanced Security Documentation** - Clearer explanation of protections
5. **Pro Tip** - Suggested integrations and best practices
6. **Better Structure** - More organized method documentation
7. **Visual Examples** - Sample log structure and entries

The README now provides everything needed for:
- Basic implementation
- Production deployment
- Custom configuration
- Troubleshooting
- Contribution guidelines
