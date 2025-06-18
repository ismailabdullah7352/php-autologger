<?php

$_SERVER['OPERATION_TYPE'] = 'install_plugin';
// أو:
define('OPERATION_TYPE', 'install_plugin');

// محاولة حفظ المستخدم
try {
    $user_data = [  'email' => 'test@example.com',
                    'user_email' => 'test@example.com'];
                    
    $saved = 'save_user_to_db($user_data)';
    if ($saved) {
        log_operation('save_user', 'success', ['user_email' => $user_data['email']]);
    } else {
        log_operation('save_user', 'failed', ['reason' => 'DB insert failed']);
    }

    $sent = 'send_verification_email($user_data['email'])';
    if ($sent) {
        log_operation('send_verification_email', 'success', ['email' => $user_data['email']]);
    } else {
        log_operation('send_verification_email', 'failed', ['email' => $user_data['email']]);
    }

} catch (Exception $e) {
    log_operation('register_user', 'failed', ['error' => $e->getMessage()]);
}
