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
    
    public function actionCustomsExcelCheck()
    {
    	$reqParam = [];
    	$errMsg = '';
    	$pdfFiles = [];
    	if ($this->_req->getIsPost()) {
    		if (empty($_FILES['excelfile']) || $_FILES['excelfile']['error'] != UPLOAD_ERR_OK) {
    			$errMsg .= "Excel文件上传失败。";
    		}
    		if (!empty($_FILES['pdffile'])) {
    			foreach ($_FILES['pdffile']['name'] as $k => $v) {
    				if ($_FILES['pdffile']['error'][$k] != UPLOAD_ERR_OK) {
    					$errMsg .= "{$v}上传失败。";
    				} else {
    					$pdfFiles[] = ['name' => $v, 'file' => $_FILES['pdffile']['tmp_name'][$k]];
    				}
    			}
    		}
    		if (!empty($pdfFiles)) {
    			$reqParam['excelFilename'] = $_FILES['excelfile']['name'];
    			$excelParser = new \app\models\CustomsExcelParser();
    			$excelParser->parse($_FILES['excelfile']['tmp_name']);
    			$excelResult = $excelParser->getParsedResult();
    			
    			foreach ($pdfFiles as $k => $v) {
    					$pdfParser = new \app\models\CustomsExportExcelParser();
    					$pdfParser->parse($v['file']);
    					$pdfResult = $pdfParser->getParsedResult();
    					if (empty($pdfResult)) {
    						$errMsg .= $v['name'].'中没有找到商品信息。';
    					} else {
    						$c = new \app\models\CustomsExcelCompare();
    						$pdfFiles[$k]['res'] = $c->compare($pdfResult[0], $excelResult);
    					}
    			}

    		}    		
    	}
    	
    	return $this->render('customsExcelCheck', ['checkRes' => $pdfFiles, 'errMsg' => $errMsg, 'reqParam' => $reqParam]);
    }

    public function actionCustomsPdfCheck()
    {
    	$reqParam = [];
    	$errMsg = '';
    	$pdfFiles = [];
    	if ($this->_req->getIsPost()) {
    		if (empty($_FILES['excelfile']) || $_FILES['excelfile']['error'] != UPLOAD_ERR_OK) {
    			$errMsg .= "Excel文件上传失败。";
    		}
    		if (!empty($_FILES['pdffile'])) {
    			foreach ($_FILES['pdffile']['name'] as $k => $v) {
    				if ($_FILES['pdffile']['error'][$k] != UPLOAD_ERR_OK) {
    					$errMsg .= "{$v}上传失败。";
    				} else {
    					$pdfFiles[] = ['name' => $v, 'file' => $_FILES['pdffile']['tmp_name'][$k]];
    				}
    			}
    		}
    		if (!empty($pdfFiles)) {
    			$reqParam['excelFilename'] = $_FILES['excelfile']['name'];
    			$excelParser = new \app\models\CustomsExcelParser();
    			$excelParser->parse($_FILES['excelfile']['tmp_name']);
    			$excelResult = $excelParser->getParsedResult();
    			
    			$extractTextCmd = 'java -jar '.APP_PATH.'/vendor/pdfbox/pdfbox-app-2.0.11.jar ExtractText -console -sort ';
    			foreach ($pdfFiles as $k => $v) {
    				$tmpCmd = $extractTextCmd . $v['file'];
    				$output = null;
    				exec($tmpCmd, $output, $ret);
    				if (empty($output)) {
    					$errMsg .= $v['name'].'抽取文本失败。';
    				} else {
    					$pdfParser = new \app\models\CustomsPdfParser();
    					$pdfParser->parse($output);
    					$pdfResult = $pdfParser->getParsedResult();
    					if (empty($pdfResult)) {
    						$errMsg .= $v['name'].'中没有找到商品信息。';
    					} else {
    						$c = new \app\models\CustomsPdfCompare();
    						$pdfFiles[$k]['res'] = $c->compare($pdfResult, $excelResult);
    					}
    				}
    			}
    		}
    	}
    	
    	return $this->render('customsPdfCheck', ['checkRes' => $pdfFiles, 'errMsg' => $errMsg, 'reqParam' => $reqParam]);
    }
}
