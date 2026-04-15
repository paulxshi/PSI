<?php

require_once __DIR__ . '/env_loader.php';

/*
|--------------------------------------------------------------------------
| PAYMENT_MODE
|--------------------------------------------------------------------------
*/

define('PAYMENT_MODE', EnvLoader::get('PAYMENT_MODE', 'production'));


/*
|--------------------------------------------------------------------------
| API KEYS
|--------------------------------------------------------------------------
*/

define('XENDIT_API_KEY_TEST', EnvLoader::get('XENDIT_API_KEY_TEST'));
define('XENDIT_API_KEY_LIVE', EnvLoader::get('XENDIT_API_KEY_LIVE'));

/*
|--------------------------------------------------------------------------
| WEBHOOK TOKENS
|--------------------------------------------------------------------------
*/

define('XENDIT_WEBHOOK_TOKEN_TEST', EnvLoader::get('XENDIT_WEBHOOK_TOKEN_TEST'));
define('XENDIT_WEBHOOK_TOKEN_LIVE', EnvLoader::get('XENDIT_WEBHOOK_TOKEN_LIVE'));

/*
|--------------------------------------------------------------------------
| REDIRECT URLS
|--------------------------------------------------------------------------
*/

define('SUCCESS_URL_TEST', EnvLoader::get('SUCCESS_URL_TEST'));
define('FAILURE_URL_TEST', EnvLoader::get('FAILURE_URL_TEST'));

define('SUCCESS_URL_LIVE', EnvLoader::get('SUCCESS_URL_LIVE'));
define('FAILURE_URL_LIVE', EnvLoader::get('FAILURE_URL_LIVE'));

/*
|--------------------------------------------------------------------------
| ACTIVE CONFIG
|--------------------------------------------------------------------------
*/

define('XENDIT_API_KEY', PAYMENT_MODE === 'production'
    ? XENDIT_API_KEY_LIVE
    : XENDIT_API_KEY_TEST);

define('XENDIT_WEBHOOK_TOKEN', PAYMENT_MODE === 'production'
    ? XENDIT_WEBHOOK_TOKEN_LIVE
    : XENDIT_WEBHOOK_TOKEN_TEST);

define('SUCCESS_REDIRECT_URL', PAYMENT_MODE === 'production'
    ? SUCCESS_URL_LIVE
    : SUCCESS_URL_TEST);

define('FAILURE_REDIRECT_URL', PAYMENT_MODE === 'production'
    ? FAILURE_URL_LIVE
    : FAILURE_URL_TEST);

/*
|--------------------------------------------------------------------------
| OTHER SETTINGS
|--------------------------------------------------------------------------
*/

define('INVOICE_DURATION', 86400);
define('CURRENCY', 'PHP');

define('XENDIT_INVOICE_URL', 'https://api.xendit.co/v2/invoices');

function getPaymentModeDisplay() {
    return PAYMENT_MODE === 'production'
        ? 'LIVE MODE'
        : 'TEST MODE';
}