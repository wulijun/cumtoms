<?php
define('YII2_PATH', SYSTEM_PHP_LIBS_PATH.'/yii2.0');

return array(
    'basePath' => __DIR__.'/..',
    'id' => 'customs',
    'name' => 'Customs',
    'language' => 'zh_cn',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => array('log'),
	'aliases' => array(
		'@Elasticsearch' => SYSTEM_PHP_LIBS_PATH . '/elasticsearch-php-2.3.0/src/Elasticsearch',
		'@Psr/Log' => SYSTEM_PHP_LIBS_PATH . '/elasticsearch-php-2.3.0/vendor/psr/log/Psr/Log',
		'@GuzzleHttp/Ring' => SYSTEM_PHP_LIBS_PATH . '/elasticsearch-php-2.3.0/vendor/guzzlehttp/ringphp/src',
		'@GuzzleHttp/Stream' => SYSTEM_PHP_LIBS_PATH . '/elasticsearch-php-2.3.0/vendor/guzzlehttp/streams/src',
		'@React/Promise' => SYSTEM_PHP_LIBS_PATH . '/elasticsearch-php-2.3.0/vendor/react/promise/src',	
	),
    'modules' => array(
        'admini' => array(
            'class' => 'app\modules\admini\Module',
        ),
        'guide' => array(
            'class' => 'app\modules\guide\Module',
        ),
        'grpc' => array(
            'class' => 'app\modules\grpc\Module',
        ),
    ),
    'components' => array(
       'request' => array(

        ),
        'cache' => array(
           'class' => 'yii\caching\FileCache',
        ),
        'urlManager' => array(
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => array(),
        ),
        'log' => array(
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => array(
                array(
                    'class' => 'yii\log\FileTarget',
                    'levels' => array('error', 'warning'),
                ),
            ),
        ),
    ),
    'params' => array_merge(array(
        'admini.admin.iter' => 57800,
    ), require(__DIR__.'/params.php')),
);
