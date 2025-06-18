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
- 📂 Automatic log rotation and cleanup
- 📈 Performance bottleneck identification

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

```php
// Initialize the logger
$logger = new PerformanceLogger();

// Start tracking an operation
$logger->startTimer('database_query');

// Your code here...
usleep(200000); // Simulate work

// End tracking and log the operation
$logger->endTimer('database_query');
$logger->logOperation('query', 'success', ['table' => 'users'], 'database_query');
```

## 📚 Documentation

### Basic Usage

#### Initialize the Logger
```php
$logger = new PerformanceLogger();
```

#### Track Operations
```php
$logger->startTimer('operation_name');
// Your code...
$logger->endTimer('operation_name');
$logger->logOperation('action', 'status', $details, 'operation_name');
```

#### Automatic Logging
The logger automatically captures:
- All HTTP requests
- PHP errors and warnings
- Uncaught exceptions
- Fatal errors
- Performance summary at request end

### Advanced Features

#### Performance Monitoring
```php
$report = $logger->getPerformanceReport();
/*
Returns:
[
    'total_time' => '0.5037 sec',
    'operations' => [
        'operation_name' => [
            'duration' => '0.2012 sec',
            'memory_usage' => '1.5 MB',
            'start' => '2023-05-15 14:30:00',
            'end' => '2023-05-15 14:30:00'
        ]
    ]
]
*/
```

#### Configuration
Extend the class to modify defaults:
```php
class CustomLogger extends PerformanceLogger {
    protected $maxFileSize = 5242880; // 5MB
    protected $retentionDays = 7; // Keep logs for 7 days
}
```

### Log File Structure
Logs are organized by date:
```
/logs
  /2023
    /2023-05
      log-2023-05-15.log
      log-2023-05-15-1234567890.log (rotated)
```

## 🛡 Security Features

- Automatically redacts sensitive data (passwords, tokens, etc.)
- Secure file permissions (0755)
- Context sanitization for error traces
- Minimal server data collection

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📬 Contact

Ismail Albriki - [@ismailalbriki](https://github.com/ismailalbriki)

Project Link: [https://github.com/ismailalbriki/php-autologger](https://github.com/ismailalbriki/php-autologger)

## 🙌 Acknowledgments

- Inspired by various PHP logging packages
- Thanks to all open-source contributors
- Coffee ☕ for keeping developers awake
```

## Key Features of This README:

1. **Professional Header** with badges for PHP version, license, and GitHub stars
2. **Clear Feature List** highlighting the main capabilities
3. **Multiple Installation Options** including Composer
4. **Quick Start** section for immediate implementation
5. **Comprehensive Documentation** with code examples
6. **Security Section** to reassure users
7. **Contribution Guidelines** to encourage community involvement
8. **Contact Information** with your GitHub profile
9. **Clean Structure** with emoji headings for better readability

To use this README:

1. Save it as `README.md` in your repository root
2. Update the contact information if needed
3. Add any additional sections specific to your project
4. Commit and push to GitHub

The README will automatically render nicely on GitHub and provide all necessary information for potential users and contributors.
