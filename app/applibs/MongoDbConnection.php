<?php
namespace app\applibs;

use Yii;
use MongoClient;

class MongoDbConnection
{
    private static $_conns;
    private static $_dbs;
    
    /**
     * 获取mongodb的client对象
     * @param string $configKey mongodb的配置项名
     * @return MongoClient
     * @throws Exception
     */
    public static function getDb($configKey)
    {
        if (! isset(self::$_conns[$configKey])) {
            $config = Yii::$app->params[$configKey];
            if (empty($config)) {
                throw new \Exception("not find mongo config $configKey");
            }
            if (empty($config['options']['db'])) {
                throw new \Exception("not find mongo db option");
            }
            self::$_conns[$configKey] = new MongoClient($config['server'], $config['options']);
            self::$_dbs[$configKey] = $config['options']['db'];
        }
        return self::$_conns[$configKey];
    }
    
    public static function getName($configKey)
    {
        if (! isset(self::$_dbs[$configKey])) {
            $config = Yii::app()->params[$configKey];
            if (empty($config['options']['db'])) {
                throw new \Exception("not find db name");
            }
            self::$_dbs[$configKey] = $config['options']['db'];
        }
        return self::$_dbs[$configKey];
    }
    
    /**
     * 获取db及其collections的stats信息
     * 
     * @param string $configKey
     * @return array
     */
    public static function getStats($configKey)
    {
        $ret = [];
        try {
            $client = self::getDb($configKey);
        } catch (\Exception $e) {
            return $ret;
        }
        $db = $client->selectDB(self::getName($configKey));
        $res = $db->command(['dbstats' => true]);
        if (empty($res['ok'])) {
            return $ret;
        }
        $ret[] = $res;
        
        $collNames = $db->getCollectionNames();
        foreach ($collNames as $v) {
            $res = $db->command(['collStats' => $v]);
            if (empty($res['ok'])) {
                continue;
            }
            $ret[] = $res;
        }
        
        return $ret;
    }
}