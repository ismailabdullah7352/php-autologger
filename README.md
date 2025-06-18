# PHP AutoLogger

A lightweight PHP logging module that captures:

- All requests automatically
- PHP warnings, notices, and fatal errors
- Uncaught exceptions
- Internal operational steps like `save_user`, `send_email`, etc.

## 📦 Features

- Logs saved to daily log files under `/logs`
- Easy `log_operation($action, $status, $details)` function
- Compatible with projects not using frameworks
- Auto-prepend support via `.htaccess` or `php.ini`

## 📂 Project Structure

autologger.php → Core logger file
example-register.php → Demo usage of the logger
logs/ → Where logs are stored



## 🛠 How to Use

### 1. Include logger in all requests (via `.htaccess` or `php.ini`):

php_value auto_prepend_file "/full/path/to/autologger.php"



### 2. Define operation type at the top of your script:

```php
$_SERVER['OPERATION_TYPE'] = 'register_user';


3. Log internal operations:

log_operation('save_user', 'success', ['user' => 'test@example.com']);
log_operation('send_email', 'failed', ['error' => 'SMTP Timeout']);


📁 Example Log Output:

[2025-06-18 19:50:33] [REQUEST] POST /api/register-user.php | Context: {...}
[2025-06-18 19:50:33] [OPERATION] SAVE_USER - SUCCESS | Context: {...}
[2025-06-18 19:50:34] [OPERATION] SEND_EMAIL - FAILED | Context: {...}


🔐 Notes
All logs are written to /logs/log-YYYY-MM-DD.log
Make sure the logs/ folder is writable
Best used under HTTPS environments
MIT License. Developed with ❤️ for real-time visibility and debugging.
