<?php
namespace app\models;

class CustomsExcelCompare {
	
	public function compare($pdfRes, $excelRes) {
		$lastSameNum = 0;
		$lastRes = null;
		foreach ($excelRes as $oneTable) {
			$tmpRes = $this->compareOneTable($pdfRes, $oneTable);
			if (empty($tmpRes['same'])) {
				continue;
			}
			if (count($tmpRes['same']) > $lastSameNum) {
				$lastRes = $tmpRes;
				$lastSameNum = count($tmpRes['same']);
			}
		}
		if (empty($lastRes)) {
			$lastRes = ['pdfonly' => $pdfRes];
		}
		
		return $lastRes;
	}
	
	protected function compareOneTable($pdfRes, $targetList) {
		if (empty($targetList)) {
			return ['pdfonly' => $pdfRes];
		}
		
		$tmpPdfOnlyData = [];
		$tmpDiffData = [];
		$tmpSameData = [];
		$tmpExcelData = [];
		foreach ($targetList as $v) {
			$tmpExcelData[$v['id']] = $v;
		}
		foreach ($pdfRes as $k => $v) {
			if (empty($tmpExcelData[$v['id']])) {
				$tmpPdfOnlyData[] = $v;
				continue;
			} else {
				$excelRow = $tmpExcelData[$v['id']];
				unset($tmpExcelData[$v['id']]);
			}
			
			//compare fields
			if (!$this->compareNo($v['no'], $excelRow['no'])) {
				$v['diff_col']['no'] = 'no';
			}
			if ($v['name'] != $excelRow['name']) {
				$v['diff_col']['name'] = 'name';
			}
			if ($v['num'] != $excelRow['num']) {
				$v['diff_col']['num'] = 'num';
			}
			if (!$this->compareCountry($v['country'], $excelRow['country'])) {
				$v['diff_col']['country'] = 'country';
			}
			//if (abs($v['unit_price'] - $excelRow['unit_price']) > 0.00001) {
			//	$v['diff_col']['unit_price'] = 'unit_price';
			//}
			if (abs($v['total_price'] - $excelRow['total_price']) > 0.00001) {
				$v['diff_col']['total_price'] = 'total_price';
			}
			
			$v['excel_row'] = $excelRow;
			if (!empty($v['diff_col'])) {
				$tmpDiffData[] = $v;
			} else {
				$tmpSameData[] = $v;
			}
		}
		
		return ['pdfonly' => $tmpPdfOnlyData, 'diff' => $tmpDiffData, 'same' => $tmpSameData,
				'excelonly' => array_values($tmpExcelData)];
	}
	
	protected function compareNo($pdfItemNo, $excelItemNo) {
		return str_replace('.', '', $pdfItemNo) == $excelItemNo;
	}
	
	protected function compareCountry($pdfItemCountry, $excelItemCountry) {
		$minLen = min(strlen($pdfItemCountry), strlen($excelItemCountry));
		if ($minLen > 0) {
			return strncmp($pdfItemCountry, $excelItemCountry, $minLen) == 0;
		} else {
			return $pdfItemCountry == $excelItemCountry;
		}
	}
}
/**
 * //pdf
        array (size=6)
          'id' => int 50
          'no' => string '8708295500' (length=10)
          'name' => string '汽车用行李箱盖' (length=21)
          'total_price' => string '4136.76' (length=7)
          'country' => string '304' (length=3)
          'num' => string '16' (length=2)
      
      //excel
        array (size=8)
          'id' => string '24' (length=2)
          'no' => string '8512209000' (length=10)
          'name' => string '汽车用尾灯' (length=15)
          'unit_price' => float 836.3657
          'total_price' => float 5854.56
          'country' => string '墨西哥' (length=9)
          'num' => float 7
          'weight' => float 9.1	
*/