<?php
// SSL Commerz Configuration for Sandbox Environment
define('STORE_ID', 'testbox');
define('STORE_PASSWORD', 'qwerty');

// SSL Commerz Sandbox URLs
define('SSLCZ_SESSION_API', 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php');
define('SSLCZ_VALIDATION_API', 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php');

// Currency
define('CURRENCY', 'BDT');
define('CURRENCY_SYMBOL', '৳');

// Success/Fail/Cancel URLs
define('SUCCESS_URL', 'http://localhost/car-rentall/carrental/payment-success.php');
define('FAIL_URL', 'http://localhost/car-rentall/carrental/payment-error.php');
define('CANCEL_URL', 'http://localhost/car-rentall/carrental/payment-cancel.php');
define('IPN_URL', 'http://localhost/car-rentall/carrental/payment-ipn.php');

// Enable/Disable SSL Commerz
define('SSLCZ_ENABLED', true);
?>
