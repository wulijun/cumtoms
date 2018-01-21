<?php
namespace app\components;

class ErrorConst
{
    const ERROR_NETWORK = -1;
    const ERROR_UNKNOW = -100;
    
    const ERROR_SUCCESSFULL = 1;
    const ERROR_CANCEL = 2;
    const ERROR_BLACK = 99;
    const ERROR_ANOTHER_LOGIN = 100;
    const ERROR_SIGNATURE = 101;
    const ERROR_AUTHTOKEN = 102;
    const ERROR_NEED_LOGIN = 103;
    
    public static $errDescs = array(
        self::ERROR_SUCCESSFULL => '操作成功',
        self::ERROR_ANOTHER_LOGIN => '您的帐号已在其他设备上登录',
        self::ERROR_SIGNATURE => '请求签名错误',
        self::ERROR_AUTHTOKEN => '请求Token错误',
        self::ERROR_NEED_LOGIN => '用户未登录',
    );
    
    public static function throwException($code, $params=null)
    {
        $code = (int) $code;
        $desc = self::getDesc($code);
        if (! empty($params)) {
            $desc = strtr($desc, $params);
        }
        throw new DlException($desc, $code);
    }
    
    public static function retException($code, $params=null)
    {
        $code = (int) $code;
        $desc = self::getDesc($code);
        if (! empty($params)) {
            $desc = strtr($desc, $params);
        }
        return array('code'=>$code, 'error'=>$desc);
    }
    
    public static function getDesc($code)
    {
        $desc = isset(self::$errDescs[$code]) ? self::$errDescs[$code] : "系统繁忙";
        return $desc;
    }
}