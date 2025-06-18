# `php-autologger` - نظام تسجيل تلقائي متقدم لـ PHP

![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Build Status](https://img.shields.io/badge/build-passing-brightgreen)

نظام متكامل لتسجيل الأخطاء والاستثناءات وقياس أداء التطبيقات PHP مع إمكانية تتبع العمليات والوقت المستغرق لكل عملية.

## المميزات الرئيسية

- 🚀 تسجيل تلقائي للأخطاء والاستثناءات
- ⏱ قياس الأداء والوقت المستغرق لكل عملية
- 📊 تتبع استخدام الذاكرة
- 🔍 كشف العمليات البطيئة تلقائياً
- 🔒 حماية البيانات الحساسة (كلمات المرور، التوكنات)
- 📂 تدوير السجلات وحذف القديم تلقائياً
- 📝 تكامل سهل مع أي مشروع PHP

## التثبيت

### باستخدام Composer

```bash
composer require ismailalbriki/php-autologger
```

### التثبيت اليدوي

1. انسخ ملف `autologger.php` لمجلد مشروعك
2. قم بتضمينه في تطبيقك:

```php
require_once 'path/to/autologger.php';
```

## الاستخدام الأساسي

### البدء السريع

```php
// إنشاء الكائن (سيبدأ التسجيل التلقائي فوراً)
$logger = new PerformanceLogger();

// تعريف نوع العملية
$_SERVER['OPERATION_TYPE'] = 'user_registration';

// مثال لقياس عملية
$logger->startTimer('db_operation');
// كود العملية...
$logger->endTimer('db_operation');
$logger->logOperation('database', 'success', ['rows' => 5], 'db_operation');
```

### إعدادات متقدمة

```php
// إنشاء كائن مع إعدادات مخصصة
class MyLogger extends PerformanceLogger {
    protected $maxFileSize = 5242880; // 5MB
    protected $retentionDays = 14; // احتفظ بالسجلات لمدة أسبوعين
}

$logger = new MyLogger();
```

## التوثيق الكامل

### الطرق الرئيسية

| الطريقة | الوصف |
|---------|-------|
| `startTimer($name)` | بدء قياس وقت ومقدار الذاكرة لعملية |
| `endTimer($name)` | إنهاء القياس وإحصاء الأداء |
| `logOperation($action, $status, $details, $timerName)` | تسجيل عملية مع تفاصيلها |
| `getPerformanceReport()` | الحصول على تقرير مفصل بالأداء |

### أمثلة متقدمة

**تسجيل عملية مع قياس الأداء:**

```php
$logger->startTimer('complex_calculation');

// كود العملية المعقدة
usleep(500000); // محاكاة عملية تستغرق نصف ثانية

$logger->endTimer('complex_calculation');
$logger->logOperation('calculation', 'completed', [
    'iterations' => 1000,
    'precision' => 0.01
], 'complex_calculation');
```

**تسجيل خطأ مع تفاصيل:**

```php
try {
    // كود قد يسبب خطأ
} catch (Exception $e) {
    $logger->logOperation('file_upload', 'failed', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
```

## إعدادات السجل

يتم حفظ السجلات تلقائياً في مسار `/logs/YYYY/YYYY-MM/log-YYYY-MM-DD.log` مع:

- تدوير السجلات عند الوصول للحجم الأقصى (10MB افتراضياً)
- حذف السجلات الأقدم من 30 يوماً (قابلة للتخصيص)

## الأمان

- يتم إخفاء البيانات الحساسة تلقائياً (كلمات مرور، توكنات، إلخ)
- صلاحيات الملفات 0755 افتراضياً
- يدعم HTTPS بشكل كامل

## المساهمة

المساهمات مرحب بها! يرجى اتباع الخطوات التالية:

1. عمل Fork للمستودع
2. إنشاء فرع للميزة الجديدة (`git checkout -b feature/AmazingFeature`)
3. عمل Commit للتغييرات (`git commit -m 'Add some AmazingFeature'`)
4. Push إلى الفرع (`git push origin feature/AmazingFeature`)
5. فتح طلب دمج (Pull Request)

## الرخصة

هذا المشروع مرخص تحت رخصة MIT - انظر ملف [LICENSE](LICENSE) للتفاصيل.

## الدعم

إذا واجهتك أي مشكلة، يرجى فتح Issue في المستودع.

---

✨ **نصيحة**: لمشاهدة السجلات في الوقت الحقيقي يمكنك استخدام:
```bash
tail -f logs/$(date +%Y)/$(date +%Y-%m)/log-$(date +%Y-%m-%d).log
```
