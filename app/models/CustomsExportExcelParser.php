<?php
namespace app\models;

use Yii;

class CustomsExportExcelParser
{
	protected $result;
	protected $colNameMap = ['备案序号' => 'id', '商品编码/HS编码' => 'no', '商品名称' => 'name', '成交总价' => 'total_price', '原产国代码（海关）' => 'country', '成交数量' => 'num'];
	protected $colIndexInfo = [];

	public function __construct() {
		Yii::setAlias('@PhpOffice/PhpSpreadsheet', APP_PATH.'/vendor/PhpSpreadsheet-1.0.0/src/PhpSpreadsheet/');
		Yii::setAlias('@Psr/SimpleCache', APP_PATH.'/vendor/simple-cache-1.0.0/src/');	
	}
	
	public function parse($filename) {
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filename);
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$curInTable = false;
		$curTableData = [];
		$rowIndex = 1;
		foreach ($worksheet->getRowIterator() as $row) {
			$colData = [];
			$cellIterator = $row->getCellIterator();
			/**
			 * This loops through all cells, even if a cell value is not set.
			 * By default, only cells that have a value  set will be iterated.
			 */
			//$cellIterator->setIterateOnlyExistingCells(FALSE); 
			foreach ($cellIterator as $cell) {
				$colData[] = $cell->getValue();
			}
			
			if ($curInTable) {
				if (!$this->isTableDataRow($colData)) {
					$this->result[] = $curTableData;
					$curTableData = [];
					$curInTable = false;
					$this->colIndexInfo = [];
				} else {
					$curRowData = [];
					foreach ($this->colIndexInfo as $v) {
						$curRowData[$v['k']] = $colData[$v['i']];
					}
					if (empty($curRowData['id'])) {
						$curRowData['id'] = $rowIndex++;
					}
					if (!empty($curRowData['country']) && is_numeric($curRowData['country'])) {
						if (isset(CountryCode::$COUNTRY_MAP[$curRowData['country']])) {
							$curRowData['country'] = CountryCode::$COUNTRY_MAP[$curRowData['country']]['cn'];
						}
					}
					$curTableData[] = $curRowData;
				}
			} elseif ($this->isNewTable($colData)) {
				$curInTable = true;
				$rowIndex = 1;
			}
		}
		if (!empty($curTableData)) {
			$this->result[] = $curTableData;
		}
	}
	
	public function getParsedResult() {
		return $this->result;
	}
	
	protected function isNewTable($colData) {
		if (count($this->colNameMap) > count($colData)) {
			return false;
		}
		foreach ($this->colNameMap as $k => $v) {
			foreach ($colData as $k2 => $v2) {
				$tmpLen = strlen($k);
				if (strncmp($k, $v2, $tmpLen) == 0) {
					$this->colIndexInfo[$k] = ['k' => $v, 'i' => $k2];
					break;
				}
			}
		}
		if (count($this->colIndexInfo) != count($this->colNameMap)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 判断是不是一个商品数据的行
	 * 
	 * @param array $colData
	 * @return boolean
	 */
	protected function isTableDataRow($colData) {
		if (count($colData) < count($this->colNameMap)
			|| !is_numeric($colData[$this->colIndexInfo['备案序号']['i']])
			|| empty($colData[$this->colIndexInfo['商品编码/HS编码']['i']])
		) {
			return false;
		}
		
		return true;
	}
}
