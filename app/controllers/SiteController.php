<?php
namespace app\controllers;

use Yii;
use app\components\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionCustomsCheck()
    {
    	$reqParam = [];
    	$errMsg = '';
    	$res = [];
    	if ($this->_req->getIsPost()) {
    		if (empty($_FILES['excelfile']) || $_FILES['excelfile']['error'] != UPLOAD_ERR_OK) {
    			$errMsg .= "Excel文件上传失败。";
    		}
    		if (empty($_FILES['pdffile']) || $_FILES['pdffile']['error'] != UPLOAD_ERR_OK) {
    			$errMsg .= "PDF文件上传失败。";
    		}
    		if ($errMsg == '') {
    			$reqParam['excelFilename'] = $_FILES['excelfile']['name'];
    			$reqParam['pdfFilename'] = $_FILES['pdffile']['name'];
    			$extractTextCmd = 'java -jar '.APP_PATH.'/vendor/pdfbox/pdfbox-app-2.0.8.jar ExtractText -console -sort ';
    			$extractTextCmd .= $_FILES['pdffile']['tmp_name'];
    			$output = null;
    			exec($extractTextCmd, $output, $ret);
    			if (empty($output)) {
    				$errMsg = 'PDF文件抽取文本失败';
    			} else {
    				$excelParser = new \app\models\CustomsExcelParser();
    				$excelParser->parse($_FILES['excelfile']['tmp_name']);
    				$excelResult = $excelParser->getParsedResult();
    				if (!is_array($excelResult)) {
    					$errMsg = 'Excel文件中没有找到商品信息';
    				} elseif (count($excelResult) > 1) {
    					$errMsg = 'Excel文件中有多个表格，只会用第一个表格比较';
    				}
    				$pdfParser = new \app\models\CustomsPdfParser();
    				$pdfParser->parse($output);
    				$pdfResult = $pdfParser->getParsedResult();
    				if (empty($pdfResult)) {
    					$errMsg = 'PDF文件中没有找到商品信息';
    				} else {
    					$c = new \app\models\CustomsCompare();
    					$res = $c->compare($pdfResult, $excelResult);
    				}
    			}
    		}    		
    	}
    	
    	return $this->render('customsCheck', ['checkRes' => $res, 'errMsg' => $errMsg, 'reqParam' => $reqParam]);
    }

}
