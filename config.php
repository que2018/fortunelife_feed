<?php

// Website Name
define('HTTP_URL', 'http://18.224.40.17/fortunelife/');
define('HTTP_FRONTEND_URL', 'http://localhost:4200/');

// Allow Access URL List
define('ALLOW_ACCESS_URL', 'http://localhost:4200');

// BASE DIR
define('DIR_BASE', str_replace('\\', '/', dirname(__FILE__)));

// SYSTEM DIR
define('DIR_SYSTEM', DIR_BASE . '/system/');

// MVC DIR
define('DIR_API', DIR_BASE . '/api/');
define('DIR_MODEL', DIR_BASE . '/model/');
define('DIR_LIBRARY', DIR_BASE . '/library/');


// Debug flag
define('FOR_DEBUG', true);

// Error Log File
define('DIR_LOGS', DIR_BASE . '/log/error_log.txt');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOST', 'thefortunelife-db.cygygoamjdjc.us-west-2.rds.amazonaws.com');
define('DB_USER', 'root');
define('DB_PASSWORD', 'z8mAwZQycTqK1jt7');
define('DB_NAME', 'thefortunelife');
define('DB_PORT', '3306');
define('DB_PREFIX', 'fl_'); 

