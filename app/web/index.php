<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('APP_PATH', dirname(__DIR__));
$config = require(APP_PATH.'/config/main.php');
$config['components']['urlManager']['rules'] = array(
    'guide/guide-<fname:[a-zA-Z0-9\-]+>.html'=>'guide/guide/index',
);

if (defined('YII_DEBUG') && YII_DEBUG) {
    register_shutdown_function(function () {
        $e = error_get_last();
        if (empty($e)) {
            return true;
        }
        $line = isset($e['line']) ? $e['line'] : 0;
        $arrLog = array(date('Y/m/d H:i:s').' [info]', "error_get_last {$e['file']}:{$line} {$e['type']} {$e['message']} {$_SERVER['REQUEST_URI']}");
        echo implode("\t", $arrLog);
    });
} else {

}

require(YII2_PATH.'/Yii.php');

(new yii\web\Application($config))->run();
