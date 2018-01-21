<?php
define('SYSTEM_PHP_LIBS_PATH', '/usr/local/php5.6/lib/php-libs');
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//define('YII_ENV', 'dev');
//ini_set('display_errors', true);
$_SiteMainConf = require(__DIR__.'/setting.php');

$_SiteMainConf['runtimePath'] = '/home/work/logs/app/runtime';
// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
$_SiteMainConf['components']['request']['cookieValidationKey'] = 'a secret key';

return $_SiteMainConf;

