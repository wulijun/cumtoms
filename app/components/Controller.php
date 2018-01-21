<?php
namespace app\components;

use Yii;

class Controller extends \yii\web\Controller
{
	const ITEM_PAGE_LIMIT = 24;
	
    /**
     * @var \yii\web\Request
     */
    protected $_req;
    
    public function init()
    {
        parent::init();
        $this->_req = Yii::$app->getRequest();
    }
    
    public function getReqParam($name, $defaultValue = null)
    {
        $v = $this->_req->get($name, null);
        if ($v === null && $this->_req->getIsPost()) {
            $v = $this->_req->post($name, null);
        }
        if ($v === null) {
            $v = $defaultValue;
        }
        return $v;
    }
    
    /**
     * Returns all GET and POST parameter value.
     *
     * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
     *
     * @return array
     */
    public function getAllReqParam()
    {
        if (empty($_GET)) {
            $ret = array();
        } else {
            $ret = $_GET;
        }
        if (! empty($_POST)) {
            $ret = array_merge($_POST, $ret);
        }
        return $ret;
    }
    
    public function renderJson($data)
    {
        $contentType = 'Content-type: application/json;charset=utf-8';
        $option = JSON_UNESCAPED_UNICODE;
        if (! empty($_GET['_gdb_'])) {
            $cb = null;
            if (! empty($_GET['callback'])) {
                $cb = $_GET['callback'];
            }
            $res = json_encode($data, $option);
            if ($cb) {
                $contentType = 'Content-type: text/javascript;charset=utf-8';
                $cb = preg_replace('/[^\w_\.]/', '', $cb);
                $res = "$cb($res);";
            }
        } else {
            $res = json_encode($data, $option);
        }
        header($contentType);
        echo $res;
    }
}