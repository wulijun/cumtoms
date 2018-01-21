<?php
namespace app\commands;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '7200');
ini_set('ignore_user_abort', 'on');
ini_set('display_errors', true);

if (!defined('YII_CMD')) define('YII_CMD', true);

/**
 * 后台任务的公共父类
*/
abstract class CommonCommand extends \yii\console\Controller {
    protected $mutexFile = null; //需要保证单进程执行时flock的文件句柄
    protected $logId = '';
    
    public function runAction($id, $params = [])
    {
        $this->logId = uniqid('NS_');
        return parent::runAction($id, $params);
    }
    
    protected function _echoHelper($str, $sep="\t")
    {
        echo date('Y-m-d H:i:s')."{$sep}{$this->logId}{$sep}{$str}\n";
    }

    /**
     * 使用锁机制保证单进程执行任务
     *
     * @param string $strFilename 进行flock操作的文件
     * @return boolean 加锁成功返回true，否则false
     */
    protected function _getMutex($strFilename) {
        $bolRet = false;
        if ($strFilename == '') {
            return $bolRet;
        }
        $strMutexFile = \Yii::$app->getRuntimePath() . "/crontab_lock_{$strFilename}.txt";
        $resFile = fopen($strMutexFile, 'a');
        if ($resFile) {
            $bolRet = flock($resFile, LOCK_EX | LOCK_NB);
            if ($bolRet) {
                $this->mutexFile = $resFile;
            } else {
                fclose($resFile);
            }
        }
         
        return $bolRet;
    }
    
    public function afterAction($action, $result)
    {
        if ($this->mutexFile) {
            flock($this->mutexFile, LOCK_UN);
            fclose($this->mutexFile);
            $this->mutexFile = null;
        }
        return parent::afterAction($action, $result);
    }
}