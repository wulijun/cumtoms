<?php
namespace app\models;

class CustomsPdfCompare {
	
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
			if (abs($v['unit_price'] - $excelRow['unit_price']) > 0.00001) {
				$v['diff_col']['unit_price'] = 'unit_price';
			}
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
		$tmp = strncmp($pdfItemCountry, $excelItemCountry, strlen($excelItemCountry));
		return $tmp == 0;
	}
}
/**
 * //pdf
    array (size=8)
      'id' => string '50' (length=2)
      'no' => string '4016939000' (length=10)
      'weight' => string '6.645千克' (length=11)
      'country' => string '德国(DEU)' (length=11)
      'name' => string '汽车用硫化橡胶密封件' (length=30)
      'unit_price' => string '296.8133' (length=8)
      'total_price' => string '890.4400' (length=8)
      'num' => int 3
      
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
