<?php

// Load environment variables
require_once __DIR__ . '/env_loader.php';

//  ENVIRONMENT CONFIGURATION 
define('PAYMENT_MODE', EnvLoader::get('PAYMENT_MODE', 'test'));

//  XENDIT API KEYS 
// TEST MODE API Key (Sandbox)
define('XENDIT_API_KEY_TEST', EnvLoader::get('XENDIT_API_KEY_TEST', ''));

// PRODUCTION MODE API Key
define('XENDIT_API_KEY_LIVE', EnvLoader::get('XENDIT_API_KEY_LIVE', ''));

//  WEBHOOK CONFIGURATION 
// TEST MODE Webhook Verification Token
define('XENDIT_WEBHOOK_TOKEN_TEST', EnvLoader::get('XENDIT_WEBHOOK_TOKEN_TEST', ''));

// PRODUCTION MODE Webhook Verification Token
define('XENDIT_WEBHOOK_TOKEN_LIVE', EnvLoader::get('XENDIT_WEBHOOK_TOKEN_LIVE', ''));

//  REDIRECT URLS 
// TEST MODE URLs
define('SUCCESS_URL_TEST', EnvLoader::get('SUCCESS_URL_TEST', 'http://localhost/PSI/auth/payment_success.html'));
define('FAILURE_URL_TEST', EnvLoader::get('FAILURE_URL_TEST', 'http://localhost/PSI/auth/payment_failed.html'));

// PRODUCTION MODE URLs
define('SUCCESS_URL_LIVE', EnvLoader::get('SUCCESS_URL_LIVE', 'https://yourdomain.com/auth/payment_success.html'));
define('FAILURE_URL_LIVE', EnvLoader::get('FAILURE_URL_LIVE', 'https://yourdomain.com/auth/payment_failed.html'));

//  PAYMENT SETTINGS 
define('INVOICE_DURATION', EnvLoader::get('INVOICE_DURATION', 86400)); 
define('CURRENCY', EnvLoader::get('CURRENCY', 'PHP'));

// Automatically use correct settings based on PAYMENT_MODE
define('XENDIT_API_KEY', PAYMENT_MODE === 'production' ? XENDIT_API_KEY_LIVE : XENDIT_API_KEY_TEST);
define('XENDIT_WEBHOOK_TOKEN', PAYMENT_MODE === 'production' ? XENDIT_WEBHOOK_TOKEN_LIVE : XENDIT_WEBHOOK_TOKEN_TEST);
define('SUCCESS_REDIRECT_URL', PAYMENT_MODE === 'production' ? SUCCESS_URL_LIVE : SUCCESS_URL_TEST);
define('FAILURE_REDIRECT_URL', PAYMENT_MODE === 'production' ? FAILURE_URL_LIVE : FAILURE_URL_TEST);

//  XENDIT API ENDPOINTS 
define('XENDIT_INVOICE_URL', 'https://api.xendit.co/v2/invoices');

//  VALIDATION 
// Warn if production mode is enabled but production keys are not set
if (PAYMENT_MODE === 'production') {
    if (XENDIT_API_KEY_LIVE === 'YOUR_PRODUCTION_API_KEY_HERE') {
        error_log('[PAYMENT CONFIG ERROR] Production mode is enabled but XENDIT_API_KEY_LIVE is not configured!');
        if (php_sapi_name() !== 'cli') {
            die('Payment system configuration error. Please contact administrator.');
        }
    }
    
    if (XENDIT_WEBHOOK_TOKEN_LIVE === 'YOUR_PRODUCTION_WEBHOOK_TOKEN_HERE') {
        error_log('[PAYMENT CONFIG WARNING] Production mode is enabled but XENDIT_WEBHOOK_TOKEN_LIVE is not configured!');
    }
    
    // Check if URLs still point to localhost
    if (strpos(SUCCESS_URL_LIVE, 'localhost') !== false || strpos(FAILURE_URL_LIVE, 'localhost') !== false) {
        error_log('[PAYMENT CONFIG WARNING] Production mode is enabled but redirect URLs still contain localhost!');
    }
}

//  HELPER FUNCTIONS 

function getPaymentModeDisplay() {
    return PAYMENT_MODE === 'production' ? '🔴 LIVE MODE' : '🟢 TEST MODE';
}

function isProductionReady() {
    if (PAYMENT_MODE !== 'production') {
        return ['ready' => true, 'message' => 'Currently in test mode'];
    }
    
    $issues = [];
    
    if (XENDIT_API_KEY_LIVE === 'YOUR_PRODUCTION_API_KEY_HERE') {
        $issues[] = 'Production API key not configured';
    }
    
    if (XENDIT_WEBHOOK_TOKEN_LIVE === 'YOUR_PRODUCTION_WEBHOOK_TOKEN_HERE') {
        $issues[] = 'Production webhook token not configured';
    }
    
    if (strpos(SUCCESS_URL_LIVE, 'localhost') !== false) {
        $issues[] = 'Success URL still points to localhost';
    }
    
    if (strpos(FAILURE_URL_LIVE, 'localhost') !== false) {
        $issues[] = 'Failure URL still points to localhost';
    }
    
    if (!empty($issues)) {
        return ['ready' => false, 'issues' => $issues];
    }
    
    return ['ready' => true, 'message' => 'Production configuration is valid'];
}

// Log current mode on file load (helpful for debugging)
if (php_sapi_name() !== 'cli') {
    error_log('[PAYMENT CONFIG] Running in ' . getPaymentModeDisplay() . ' mode');
}

