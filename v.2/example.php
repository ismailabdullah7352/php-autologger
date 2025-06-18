<?php
// #########################################
// ## مثال للاستخدام (يمكن حذفه في الإنتاج) ##
// #########################################

// تمثيل نوع العملية
$_SERVER['OPERATION_TYPE'] = 'user_registration';

// إنشاء الكائن
$logger = new PerformanceLogger();

// بدء قياس العملية الرئيسية
$logger->startTimer('user_registration');

// عملية إدخال قاعدة البيانات
$logger->startTimer('db_insert');
usleep(200000); // محاكاة عملية تستغرق 200 مللي ثانية
$logger->endTimer('db_insert');
$logger->logOperation('db_insert', 'success', [
    'rows_affected' => 1,
    'table' => 'users',
    'user_email' => 'test@example.com',
    'password' => 'should_be_hidden'
], 'db_insert');

// عملية إرسال البريد
$logger->startTimer('email_sending');
usleep(300000); // محاكاة عملية تستغرق 300 مللي ثانية
$logger->endTimer('email_sending');
$logger->logOperation('email_sending', 'success', [
    'email' => 'test@example.com',
    'template' => 'welcome'
], 'email_sending');

// إنهاء العملية الرئيسية
$logger->endTimer('user_registration');
$logger->logOperation('user_registration', 'completed', [
    'user_id' => 123,
    'status' => 'active'
], 'user_registration');

// الحصول على تقرير الأداء
$performanceReport = $logger->getPerformanceReport();
print_r($performanceReport);
