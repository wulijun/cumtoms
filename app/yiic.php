<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);

error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('APP_PATH', __DIR__);

$config = require(APP_PATH.'/config/main.php');
$config['controllerNamespace'] = 'app\commands';
unset($config['components']['request']);

register_shutdown_function(function () {
    $e = error_get_last();
    if (empty($e)) {
        return true;
    }
    $line = isset($e['line']) ? $e['line'] : 0;
    $arrLog = array(date('Y/m/d H:i:s').' [info]', "error_get_last {$e['file']}:{$line} {$e['type']} {$e['message']} {$_SERVER['REQUEST_URI']}");
    echo implode("\t", $arrLog);
});

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

require(YII2_PATH.'/Yii.php');

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
