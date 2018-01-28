<?php
namespace app\models;

class CustomsCompare {
	
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
			if (!$this->compareCountry($v['weight_country'], $excelRow['country'])) {
				$v['diff_col']['weight_country'] = 'country';
			}
			if (abs($v['unit_price'] - $excelRow['unit_price']) > 0.01) {
				$v['diff_col']['unit_price'] = 'unit_price';
			}
			if (abs($v['total_price'] - $excelRow['total_price']) > 0.01) {
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
		$tmp = substr_compare($pdfItemCountry, $excelItemCountry, strlen($excelItemCountry) * -1);
		return $tmp == 0;
	}
}
/**
 * //pdf
    array (size=7)
      'id' => string '1' (length=1)
      'no' => string '85122010.00' (length=11)
      'weight_country' => string '2个德国' (length=10)
      'name' => string '汽车用大灯/BMW牌' (length=22)
      'unit_price' => string '4621.0250' (length=9)
      'total_price' => string '9242.05' (length=7)
      'num' => string '2' (length=1)
      
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