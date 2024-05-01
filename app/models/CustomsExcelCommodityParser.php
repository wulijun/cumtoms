<?php
namespace app\models;

use Yii;

class CustomsExcelCommodityParser
{
	protected $result;
	protected $colNameMap = ['S/N' => 's/n', 'Type' => 'type', 'Cust order NO' => 'custorderno', 'CHN Prod Desc' => 'chnproddesc', 'Hs Code' => 'hscode',
        'Orginal country' => 'orginalcountry', 'QTY' => 'qty', 'Unit Price' => 'unitprice', 'Total Amount' => 'totalamount',
        'Currency' => 'currency', 'PART NO' => 'partno'];
    protected $colNameMap2 = ['S/N' => 's/n', 'Type' => 'type', 'Cust order NO' => 'custorderno', 'CHN Prod Desc' => 'chnproddesc', 'Hs Code' => 'hscode',
        'QTY' => 'qty', 'N.W (kg)' => 'n.w(kg)', 'PART NO' => 'partno'];
	protected $colIndexInfo = [];
	protected $colNameAlias = ['CIF总价' => ['总价']];
    protected $soldTo = '';						
	
	public function __construct() {
		Yii::setAlias('@PhpOffice/PhpSpreadsheet', APP_PATH.'/vendor/PhpSpreadsheet-1.0.0/src/PhpSpreadsheet/');
		Yii::setAlias('@Psr/SimpleCache', APP_PATH.'/vendor/simple-cache-1.0.0/src/');
		Yii::setAlias('@ZipStream', APP_PATH.'/vendor/ZipStream-PHP-3.0.2/src/');
	}
	
	public function parse($filename) {
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filename);
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
        if ($spreadsheet->getSheetCount() < 2) {
            return;
        }

		$worksheet = $spreadsheet->getSheet(0);
		$curInTable = false;
		$curTableData = [];
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

            if (!empty($colData[0])) {
                $tmpCol0 = $this->transName($colData[0]);
                if (strncmp($tmpCol0, "soldto", 6) == 0) {
                    $this->soldTo = trim($colData[1]);
                    continue;
                }
            }
			
			if ($curInTable) {
				if (!$this->isTableDataRow($colData)) {
					break;
				} else {
					$curRowData = ['n.w(kg)' => '0'];
					foreach ($this->colIndexInfo as $v) {
						$curRowData[$v['k']] = $colData[$v['i']];
					}
					$curTableData[] = $curRowData;
				}
			} elseif ($this->isNewTable($colData)) {
				$curInTable = true;
			}
		}
		if (!empty($curTableData)) {
			$this->result = $curTableData;
		}

        $worksheet = $spreadsheet->getSheet(1);
		$curInTable = false;
		$curTableData = [];
        $this->colIndexInfo = [];
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
				if (!$this->isTableDataRow2($colData)) {
					break;
				} else {
					$curRowData = [];
					foreach ($this->colIndexInfo as $v) {
						$curRowData[$v['k']] = $colData[$v['i']];
					}
                    $tmpk = "{$curRowData['type']}-{$curRowData['partno']}-{$curRowData['chnproddesc']}-{$curRowData['hscode']}";
					if (array_key_exists($tmpk, $curTableData)) {
						$curTableData[$tmpk]['qty'] += $curRowData['qty'];
						$curTableData[$tmpk]['n.w(kg)'] += $curRowData['n.w(kg)'];
					} else {
						$curTableData[$tmpk] = $curRowData;
					}
				}
			} elseif ($this->isNewTable2($colData)) {
				$curInTable = true;
			}
		}
		if (!empty($curTableData)) {
			//print_r($curTableData);
            foreach ($this->result as $k => $v) {
                $tmpk = "{$v['type']}-{$v['partno']}-{$v['chnproddesc']}-{$v['hscode']}";
                if (!empty($curTableData[$tmpk])) {
                    $this->result[$k]["n.w(kg)"] = $curTableData[$tmpk]["n.w(kg)"];
					// 因为存在sheet1中的一条拆成sheet2中的两条，或者sheet1中的1条对应sheet2的多条，现在$curTableData
					// 的数据是累加后的，放在sheet1中的一条数据里
					unset($curTableData[$tmpk]);
                }/* else {
					print_r($v);
					echo "not find $tmpk<br>\n";
				}*/
            }
			//print_r($this->result);
		}

        if (!empty($this->result)) {
            $modelNames = $this->getModelNames();
            $mergedata = [];
            foreach($this->result as $v) {
                $tmpk = "{$v['chnproddesc']}-{$v['hscode']}-{$v['orginalcountry']}-{$v['currency']}";
                if (!empty($mergedata[$tmpk])) {
                    $tmpv = $mergedata[$tmpk];
                    $tmpv['n.w(kg)'] += $v['n.w(kg)'];
                    $tmpv['qty'] += $v['qty'];
                    $tmpv['totalamount'] += $v['totalamount'];
					$tmpv['unitprice'] = round($tmpv['totalamount'] / $tmpv['qty'], 4);
                    $mergedata[$tmpk] = $tmpv;
                } else {
                    if (!empty($modelNames[$v['chnproddesc']])) {
                        $v["modelname"] = $modelNames[$v['chnproddesc']];
                    } else {
                        $v["modelname"] = '';
                    }
					if ($v['currency'] == 'JPY') {
						$v['currencyname'] = '日本元';
					} else if ($v['currency'] == 'USD') {
						$v['currencyname'] = '美元';
					} else {
						$v['currencyname'] = $v['currency'];
					}
					$v['unitprice'] = round($v['totalamount'] / $v['qty'], 4);
                    $mergedata[$tmpk] = $v;
                }
            }
            $this->result = $mergedata;
			foreach($this->result as $k => $v) {
				$tmpv = $this->getFirstUnit($v);
				$v['qty_0_name'] = $tmpv[0];
				$v['qty_0_val'] = $tmpv[1];

				$tmpv = $this->getSecondUnit($v);
				$v['qty_1_name'] = $tmpv[0];
				$v['qty_1_val'] = $tmpv[1];

				$this->result[$k] = $v;
			}
        }
	}

	public function gen2007ExcelFile($origname) {
		$tpl = APP_PATH.'/web/static/download/tpl-2007.xlsx';
		$savefile = '2007-'.date('YmdHis-').md5($origname).'.xlsx';
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tpl);
		$spreadsheet = $reader->load($tpl);
		$worksheet = $spreadsheet->getSheet(0);
		$i = 1;
		foreach ($this->result as $v) {
			$row = $i + 3;
			$worksheet->setCellValueByColumnAndRow(1, $row, $i);
			$worksheet->setCellValueByColumnAndRow(6, $row, $v['hscode']);
			$worksheet->setCellValueByColumnAndRow(8, $row, $v['chnproddesc']);
			$worksheet->setCellValueByColumnAndRow(9, $row, $v['modelname']);
			$worksheet->setCellValueByColumnAndRow(10, $row, $v['qty']);
			$worksheet->setCellValueByColumnAndRow(11, $row, '个');
			$worksheet->setCellValueByColumnAndRow(12, $row, $v['unitprice']);
			$worksheet->setCellValueByColumnAndRow(13, $row, $v['totalamount']);
			$worksheet->setCellValueByColumnAndRow(14, $row, $v['currencyname']);
			$worksheet->setCellValueByColumnAndRow(15, $row, $v['qty_0_val']);
			$worksheet->setCellValueByColumnAndRow(16, $row, $v['qty_0_name']); // 第一计量单位
			$worksheet->setCellValueByColumnAndRow(20, $row, $v['qty_1_val']);
			$worksheet->setCellValueByColumnAndRow(21, $row, $v['qty_1_name']); // 第二计量单位
			$worksheet->setCellValueByColumnAndRow(23, $row, $v['orginalcountry']);
			$worksheet->setCellValueByColumnAndRow(27, $row, 1); // 征免方式
			$i++;
		}
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
		$writer->save(APP_PATH.'/web/static/download/'.$savefile);

		return $savefile;
	}

	public function getParsedResult() {
		return $this->result;
	}

	protected function getFirstUnit($row) {
		$tmpn = '个';
		$tmpv = $row['qty'];
		$tmphscode = trim($row['hscode']);
		if (in_array($tmphscode, array('8532241000', '8548000001', '8517799000', '8532230000', '8533400000',
			'8548000090'))) {
			$tmpn = '千克';
			$tmpv = $row['n.w(kg)'];
		} else if ($row['hscode'] == '8543709990') { // 磁性开关
			$tmpn = '台';
		}
		return array($tmpn, $tmpv);
	}

	protected function getSecondUnit($row) {
		$tmpn = '千克';
		$tmpv = $row['n.w(kg)'];
		$tmphscode = trim($row['hscode']);
		if (in_array($tmphscode, array('8532241000', '8532230000', '8533400000'))) {
			$tmpn = '千个';
			$tmpv = $row['qty'] / 1000;
		} else if (in_array($tmphscode, array('8548000001', '8517799000', '8548000090'))) {
			$tmpn = '';
			$tmpv = '';
		}
		return array($tmpn, $tmpv);
	}

	protected function isNewTable($colData) {
		if (count($this->colNameMap) > count($colData)) {
			return false;
		}
		foreach ($this->colNameMap as $k => $v) {
			foreach ($colData as $k2 => $v2) {
				$tmpLen = strlen($k);
				if (strncmp($this->transName($k), $this->transName($v2), $tmpLen) == 0) {
					$this->colIndexInfo[$k] = ['k' => $v, 'i' => $k2];
					break;
				} elseif (isset($this->colNameAlias[$k])) {
					foreach ($this->colNameAlias[$k] as $v3) {
						if (strncmp($v3, $this->transName($v2), strlen($v3)) == 0) {
							$this->colIndexInfo[$k] = ['k' => $v, 'i' => $k2];
							break 2;
						}
					}
				}
			}
		}
		if (count($this->colIndexInfo) != count($this->colNameMap)) {
			return false;
		}
		
		return true;
	}

    protected function isNewTable2($colData) {
		if (count($this->colNameMap2) > count($colData)) {
			return false;
		}
		foreach ($this->colNameMap2 as $k => $v) {
			foreach ($colData as $k2 => $v2) {
				$tmpLen = strlen($k);
				if (strncmp($this->transName($k), $this->transName($v2), $tmpLen) == 0) {
					$this->colIndexInfo[$k] = ['k' => $v, 'i' => $k2];
					break;
				} elseif (isset($this->colNameAlias[$k])) {
					foreach ($this->colNameAlias[$k] as $v3) {
						if (strncmp($v3, $this->transName($v2), strlen($v3)) == 0) {
							$this->colIndexInfo[$k] = ['k' => $v, 'i' => $k2];
							break 2;
						}
					}
				}
			}
		}
		if (count($this->colIndexInfo) != count($this->colNameMap2)) {
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
			|| !is_numeric($colData[$this->colIndexInfo['S/N']['i']])
			|| empty($colData[$this->colIndexInfo['Type']['i']])
		) {
			return false;
		}
		
		return true;
	}

    protected function isTableDataRow2($colData) {
		if (count($colData) < count($this->colNameMap2)
			|| !is_numeric($colData[$this->colIndexInfo['S/N']['i']])
			|| empty($colData[$this->colIndexInfo['Type']['i']])
		) {
			return false;
		}
		
		return true;
	}

    protected function transName($name) {
        if (is_null($name)) return '';

        $name = trim($name);
        $name = str_replace(' ', '', $name);
        return strtolower($name);
    }

    protected function getModelNames() {
        $d = array(
            '雅创' => array('电感' => '4|3|汽车中控娱乐导航中电磁波噪声滤除用|中文品牌：村田牌 英文品牌：MURATA |LQG15WH5N1C02D等',
                '共模电感' => '4|3|汽车中控娱乐导航中电磁波噪声滤除用|中文品牌：村田牌 英文品牌：MURATA |DLW21SZ900HQ2L',
                '蜂鸣器' => '4|3|电子设备等的确认声音|把警示电信号转换成人耳能感知的电、声转换装置|适用广汽A88 PSD车型|中文品牌：村田牌 英文品牌：MURATA|PKM22EPPH4007-B0',
                '平衡转换器' => '4|3|适用大唐品牌的基站用，用于平衡和非平衡信号的相互转换以及阻 抗变换|中文品牌：村田牌 英文品牌：MURATA |DXW21BZ7511TL',
                '小型蜂鸣器' => '4|3|电子设备等的确认声音|70-100分贝|中文品牌：村田牌 英文品牌：MURATA|PKLCS1212E40A1-R1',
                '片式多层瓷介电容器' => '4|3|片式多层|陶瓷介质|中文品牌：村田牌 英文品牌：MURATA |GRT155C80J475ME13D等',
                '热敏电阻器' => '4|3|中文品牌：村田牌 英文品牌：MURATA|NCU18WF104E6SRB',
                '热敏电阻' => '4|3|中文品牌：村田牌 英文品牌：MURATA|PRG21AR4R7MS5RA',
                '振荡子' => '4|3|已装配压电晶体|中文品牌：村田牌 英文品牌：MURATA|CSTNE8M00GH5C000R0',
                '电磁干扰滤波器' => '4|3|干扰电路中的杂波|中文品牌：村田牌 英文品牌：MURATA |BLM18KN700EH1D等',
                '单层瓷介电容器' => '4|3|单层非片式|陶瓷介质|MURATA 村田牌|DE1E3RA222MN4AP01F等',
                '超声波传感器' => '4|3|打印机纸张重叠判断|利用超声波在固体和气体传输衰减特性，将通过纸张变化后的超声波传递到相应元器件，用以判定是否纸张重叠|打印机进纸重叠的错误判断|中文品牌：村田牌 英文品牌：MURATA |MA300D1-1',
                '电源模块' => '4|3|用途:工业控制（隔离模块）|直流稳压电源|小于1千瓦|小于万分之一|中文品牌：村田牌 英文品牌：MURATA |NCM6D1215C',
                '高频模块' => '4|3|适用于大唐移动品牌的公网通讯|中文品牌：村田牌 英文品牌：MURATA |LFL152G45TC4C249',
                '硅电容' => '4|3|多层，片式|有机电解液、活性炭、金属薄板等 固体材料|中文品牌：村田牌 英文品牌：MURATA|935148521410-T3T',
                '声表面波滤波器' => '4|3|已装配压电晶体|中文品牌：村田牌 英文品牌：MURATA|SAFEB1G57KE0F00R15',
                '陶瓷电容器' => '4|3|片式多层|陶瓷介质|MURATA 村田牌|RDER71H104K0M1H03A',
                '陶瓷振荡子(内置电容)' => '4|3|用于空调，洗衣机，冰箱等电器产品，给 芯片提供时钟频率|中文品牌：村田牌 英文品牌：MURATA |CSTCR4M00G55B-R0',
                '陀螺仪加速度传感集成电路' => '4|3|用于需要测量加速度的机械设备等|测量加速度时起到感应作用|已封装|已切割|中文品牌：MURATA 外文品牌：村田牌|SCC3234-D10-004|量产',
            ),
            '信利康' => array('电感' => '4|3|导航仪控制板滤波用|中文品牌：村田牌 英文品牌：MURATA |FDSD0420-H-100M=P3等',
                '单层瓷介电容器' => '4|3|单层非片式|陶瓷|中文品牌：村田牌 英文品牌：MURATA|DE1E3KX222MA4BP01F等',
                '片式多层瓷介电容器' => '4|3|片式多层|陶瓷介质|中文品牌：村田牌 英文品牌：MURATA |GMA05XR72A102MA01T',
                '电磁干扰滤波器' => '4|3|工控主板上抑制电磁干扰|中文品牌：村田牌 英文品牌：MURATA |BLM18AG102SN1D等',
                '高频模块' => '4|3|适用于大唐移动品牌的公网通讯|中文品牌：村田牌 英文品牌：MURATA |LFB322G59CMLF082',
                '平衡转换器' => '4|3|适用大唐品牌的基站用，用于平衡和非平衡信号的相互转换以及阻 抗变换|中文品牌：村田牌 英文品牌：MURATA |LDB181G7AAAEA044',
                '共模电感' => '4|3|导航仪控制板滤波用|中文品牌：村田牌 英文品牌：MURATA |DLW21SN371SQ2L',
                '加速度传感集成电路' => '4|3|应用场景:汽车、通用款等|功能:测量加速度时 起到感应作用|已封装|已切割|中文品牌：MURATA 外文品牌：村田牌|SCA2120-D06|量产',
                '片式铝电解电容' => '4|3|多层片式|高分子铝|村田牌/MURATA|ECASD31E226M040KA0',
                '陀螺仪加速度传感集成电路' => '4|3|主要运用于汽车领域的角速度的测量，可根据正弦定理检测角速度变化，将测量数据传输给控制器加以控制|检测角速度变化，并将测量数据转换成电信号输出|已封装|已切割|中文品牌：MURATA 外文品牌：村田牌|SCC2230-B14|量产',
                '微型风扇' => '4|3|用于雾化器,美容器,空气净化器等设备 中,驱动空气用,非散热用 微处理器 |微型风扇|0.18W|村田牌MURATA |MZBX304',
                '压力传感器' => '4|3|压力传感器|屏幕触控压力|中文品牌：村田牌 英文品牌：MURATA |FMPS-SAMPLE-A013',
                '振荡子' => '4|3|已装配压电晶体|中文品牌：村田牌 英文品牌：MURATA |CSTNE10M0G550000R0',
            ),
            '广信联' => array('单层瓷介电容器' => '4|3|单层非片式|陶瓷|MURATA 村田牌|DE2E3SA222MA3BT02F',
                '片式多层瓷介电容器' => '4|3|多层片式|陶瓷|MURATA 村田牌|GCM1885C1H471JA16D等',
                '电磁干扰滤波器' => '4|3|干扰电路中的杂波|中文品牌：村田牌 英文品牌：MURATA |BLM18BB470SH1D',
                '电感' => '4|3|手机通用零件|MURATA 村田牌|LQW15AN56NH00D',
                '电源模块' => '4|3|工业控制（隔离模块）|直流稳压电源|小于1千瓦|小于万分之一|MURATA 村田牌|CMR100PC',
                '共模电感' => '4|3|手机通用零件|MURATA 村田牌|DLW43SH510XK2L',
                '振荡子' => '4|3|已装配压电晶体|中文品牌：村田牌 英文品牌：MURATA |CSTNE8M00GH5C000R0',
                '陶瓷震荡子(内置电容)' => '4|3|用途:用于空调，洗衣机，冰箱等电器产品，给 芯片提供时钟频率|中文品牌：村田牌 英文品牌：MURATA |CSTCR4M00G55B-R0',
                '陀螺仪加速度传感集成电路' => '4|3|用于需要测量加速度和角速度的机械设备|测量加速度和角速度|封装|已切割|MURATA 村田牌|SCHA63T-K03-004|量产',
            ),
            '聪超' => array('共模电感' => '4|3|用于电源电路噪声信号滤除 |中文品牌：村田牌 英文品牌：MURATA |DLW5BSM351SQ2L等',
                '电感' => '4|3|用于电源电路噪声信号滤除 |中文品牌：村田牌 英文品牌：MURATA |LQW15AN2N7G80D等',
                '蜂鸣片' => '4|3|适用于海尔等品牌冰箱、洗衣机等通过产品的压电效应给冰箱等家电提供声音报警信号|中文品牌：村田牌 英文品牌：MURATA |7BB-20-3',
                '片式铝电解电容' => '4|3|多层片式|高分子铝|村田牌/MURATA|ECASD40E337M006KA0',
                '单层瓷介电容器' => '4|3|单层非片式|陶瓷|村 田牌/MURATA|DE1E3RA472MA4BQ01F',
                '片式多层瓷介电容器' => '4|3|片式多层|陶瓷介质|中文品牌：村田牌 英文品牌：MURATA |GJM1555C1H2R3WB01D等',
                '热敏电阻器' => '4|3|中文品牌：村田牌 英文品牌：MURATA |NCU18WF104F6SRB',
                '振荡子' => '4|3|已装配压电晶体|中文品牌：村田牌 英文品牌：MURATA|CSTNE8M00GH5C000R0',
                '电磁干扰滤波器' => '4|3|干扰电路中的杂波|中文品牌：村田牌 英文品牌：MURATA |DSS1NB32A103Q55B等',
                '电感器' => '4|3|用于电源电路噪声信号滤除 |中文品牌：村田牌 英文品牌：MURATA |LQH32PN101MN0L',
                '高频模块' => '4|3|适用于信通品牌的智能手持终端PDA或者各类通信终端设备，对电磁波进行过滤，允许一定频段的电磁波通过以达到滤波的目的|中文品牌：村田牌 英文品牌：MURATA |LFB182G45CGFD436',
                '热敏电阻' => '4|3|中文品牌：村田牌 英文品牌：MURATA |PRF18BA471QB5RB',
                '双工器' => '4|3|通用于移动设备|中文品牌：村田牌 英文品牌：MURATA |LFD182G45MJ5E355',
                '陶瓷电容器' => '4|3|片式多层|陶瓷介质|中文品牌：村田牌 英文品牌：MURATA |RDER73A104K5E1H03A',
                '小型蜂鸣器' => '4|3|广泛应用于计算机、报警器等电子产品中作发声器件|79dB|中文品牌：村田牌 英文品牌：MURATA|PKM22EPPH2002-B0',
            ),
            '立坤' => array('电感' => '4|3|手机通用零件|英文名称：MURATA |LQM2MPNR68MGHL',
                '片式多层瓷介电容器' => '4|3|多层片式|陶瓷介质|英文品牌：MURATA |GRM21BC72A105KE01L等',
                '电磁干扰滤波器' => '4|3|干扰电路中的杂波|英文名称：MURATA|BLM21SN300SN1D等',
            ),
        );
        foreach ($d as $k => $v) {
            // echo "{$this->soldTo}==={$k}\n";
            if (strpos($this->soldTo, $k) !== false) {
                return $v;
            }
        }

        return array();
    }
}
/**
 * 
    [电感-8504500000-日本-JPY] => Array
        (
            [n.w(kg)] => 17.033
            [s/n] => 1
            [type] => LQG15WH5N1C02D+05-01
            [custorderno] => S0174LJ-PO20230807007
            [chnproddesc] => 电感
            [hscode] => 8504500000
            [orginalcountry] => 日本
            [qty] => 491500
            [unitprice] => =L10/J10
            [totalamount] => 5014348
            [currency] => JPY
            [modelname] => 4|3|汽车中控娱乐导航中电磁波噪声滤除用|中文品牌：村田牌 英文品牌：MURATA |LQG15WH5N1C02D等
        )

 */