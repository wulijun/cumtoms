<?php
namespace app\applibs;

use Yii;

class Util
{
    const ID_ENCODE_CIPHER = 0xA26259EB;
    
    /**
     * 字符串预处理
     *
     * - 去除首尾空格
     * - 多个空格合并为一个
     * - 多个换行合并为一个
     *
     * @param string $str string
     *
     * @return string
     */
    public static function strfmt($str)
    {
        //UTF-8下这两项是安全的，其它编码需要注意
        $str = str_replace(chr(0xC2).chr(0xA0), ' ', $str);//non-breaking space
        $str = mb_ereg_replace('　', ' ', $str);//全角空格
    
        $str = trim($str);
        $str = preg_replace("/ +/", " ", $str);
        $str = preg_replace("/\t+/", "\t", $str);
        $str = preg_replace("/\n+/", "\n", $str);
        $str = preg_replace("/\r+/", "\r", $str);
        return $str;
    }
    
    public static function fmtPhoneNum($num)
    {
        $num = preg_replace("/[^0-9]/", "", $num);
        if (strlen($num) != 11 || $num[0] != '1') {
            return false;
        }
        return $num;
    }
    
    public static function trimUtf8Bom($str)
    {
        if (strncmp($str, "\xEF\xBB\xBF", 3) === 0) {
            $str = substr($str, 3);
        }
        return $str;
    }
    
    public static function time2Str($ts)
    {
        $curTime = time();
        $delta = $curTime - $ts;
        if ($delta <= 60) {
            return '刚刚';
        }
        $dt1 = new \DateTime();
        $dt1->setTimestamp($ts);
        $dt2 = new \DateTime();
        $dt2->setTimestamp($curTime);
        $tmp = $dt1->diff($dt2);
        if ($tmp->y > 0) {
            $tmp = "{$tmp->y}年前";
        } elseif ($tmp->m > 0) {
            $tmp = "{$tmp->m}月前";
        } elseif ($tmp->d > 0) {
            if ($tmp->d == 1) {
                $tmp = "昨天";
            } elseif ($tmp->d == 2) {
                $tmp = "前天";
            } else {
                $tmp = "{$tmp->d}天前";
            }
        } elseif ($tmp->h > 0) {
            $tmp = "{$tmp->h}小时前";
        } elseif ($tmp->i > 0) {
            $tmp = "{$tmp->i}分钟前";
        } else {
            $tmp = '刚刚';
        }
        return $tmp;
    }
    
    public static function strlenAsGBK($str)
    {
        $str = mb_convert_encoding($str, 'GBK', 'UTF-8');
    
        return strlen($str);
    }
    
    public static function cronLog($message, $level = \yii\log\Logger::LEVEL_INFO)
    {
        if (defined('YII_CMD') && YII_CMD) {
            echo date('Y-m-d H:i:s')."\t$message\n";
        } else {
            \Yii::getLogger()->log($message, $level);
        }
    }
    
    public static function uuidHashId($uuid)
    {
        return dj_cityhash64($uuid);
    }
    
    /**
     * 对整数进行可逆加密
     *
     * @param int $id
     * @return int
     */
    public static function encodeId($id)
    {
        $id = (int) $id;
        return dj_encode_id($id, self::ID_ENCODE_CIPHER);
    }
    
    /**
     * 对XUtils::encodeId加密结果进行解密
     *
     * @param int $id
     * @return int
     */
    public static function decodeId($id)
    {
        $id = (int) $id;
        return dj_decode_id($id, self::ID_ENCODE_CIPHER);
    }
    
    public static function escapeJs($string)
    {
        return strtr($string, array('\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/'));
    }
    
    public static function echoInputVal($key, $default = '', $OnlyVal = false)
    {
        if (is_array($_GET) && isset($_GET[$key])) {
            $v = $_GET[$key];
        } elseif (is_array($_POST) && isset($_POST[$key])) {
            $v = $_POST[$key];
        } else {
            $v = $default;
        }
        $v = \yii\helpers\Html::encode($v);
        if (! $OnlyVal) {
            $v = ' value="'.$v.'"';
        }
        echo $v;
    }
    
    /**
     * 生成Zip文件
     * 
     * @param string $filename 生成的zip文件名
     * @param array $files zip文件内容，array中key为文件名，value为文件内容
     * @return 成功返回true，失败返回false
     */
    public static function createZipFile($filename, $files, &$errmsg = null)
    {
        if (! class_exists('\ZipArchive')) {
            $errmsg = 'zip ext not enabled';
            return false;
        }
        $zip = new \ZipArchive();
        $res = $zip->open($filename, \ZipArchive::OVERWRITE);
        if ($res !== true) {
            $errmsg = "open file error {$res}";
            return false;
        }
        foreach ($files as $k => $v) {
            $res = $zip->addFromString($k, $v);
            if (! $res) {
                $errmsg = "add file $k fail";
                $zip->unchangeAll();
                break;
            }
        }
        $zip->close();
        return $res;
    }
    
    public static function imgDataUri($imgFile)
    {
        if (empty($imgFile)) {
            return false;
        }
        $imgData = @file_get_contents($imgFile);
        if (empty($imgData)) {
            return false;
        }
        
        $imgInfo = getimagesizefromstring($imgData);
        if (empty($imgInfo)) {
            return false;
        }

        return "data:{$imgInfo['mime']};base64,".base64_encode($imgData);
    }
    
    public static function sendMail($subject, $body, $addresses)
    {
        require_once(SYSTEM_PHP_LIBS_PATH.'/swiftmailer5.4/lib/swift_required.php');
        $smtpConf = Yii::$app->params['admin.smtp.server'];
        $transport = \Swift_SmtpTransport::newInstance($smtpConf['host'], $smtpConf['port'])
            ->setTimeout(60)
            ->setUsername($smtpConf['user'])
            ->setPassword($smtpConf['passwd']);
        $mailer = \Swift_Mailer::newInstance($transport);
        $message = \Swift_Message::newInstance($subject)
            ->setFrom($smtpConf['from'])
            ->setTo($addresses)
            ->setBody($body, 'text/html');
        $result = 0;
        for ($i = 0; $i < 3; $i++) {
            try {
                $result = $mailer->send($message);
                break;
            } catch (\Swift_TransportException $e) {
                Util::cronLog(__METHOD__ . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    public static function verifyTmpAccessToken($paramKeys)
    {
        if (empty($_REQUEST['_as']) || empty($_REQUEST['_at']) || $_REQUEST['_at'] + 600 < time()) {
            return false;
        }
        $paramStr = Yii::$app->params['admini.admin.salt'];
        foreach ($paramKeys as $k) {
            if (isset($_REQUEST[$k])) {
                $paramStr .= $_REQUEST[$k];
            }
        }
        $paramStr .= $_REQUEST['_at'];
        
        return $_REQUEST['_as'] == md5($paramStr);
    }
    
    public static function getTmpAccessToken($paramKeys, $data)
    {
        $curTime = time();
        $paramStr = Yii::$app->params['admini.admin.salt'];
        foreach ($paramKeys as $k) {
            if (isset($data[$k])) {
                $paramStr .= $data[$k];
            }
        }
        $paramStr .= $curTime;
        
        return "_at={$curTime}&_as=" . md5($paramStr);
    }
}
