<?php

// Load unified payment configuration (handles test/production mode automatically)
require_once __DIR__ . '/../config/payment_config.php';

// Load Xendit SDK if available
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    if (class_exists('Xendit\Configuration')) {
        Xendit\Configuration::setXenditKey(XENDIT_API_KEY);
    }
}
