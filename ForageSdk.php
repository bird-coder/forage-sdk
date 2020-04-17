<?php

if (!defined('FORAGE_SDK_WORK_DIR')) {
    define('FORAGE_SDK_WORK_DIR', '/tmp/');
}

if (!defined('FORAGE_SDK_DEV_MODE')) {
    define('FORAGE_SDK_DEV_MODE', true);
}

if (!defined('FORAGE_AUTOLOADER_PATH')) {
    define('FORAGE_AUTOLOADER_PATH', dirname(__FILE__));
}

require('Autoloader.php');