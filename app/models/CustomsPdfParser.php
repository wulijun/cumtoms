<?php
namespace app\models;

class CustomsPdfParser
{
	protected $inputLines;
	protected $result = [];
	protected $curItemLineNo = 0;
	protected $curParsedLineNo = -1;
	protected $curItemId;
	protected $reportVer = 0;
	protected $countries = array('中国','蒙古','朝鲜','韩国','日本','菲律宾','越南','老挝','柬埔寨','缅甸','泰国','马来西亚',
		'文莱','新加坡','印度尼西亚','东帝汶','尼泊尔','不丹','孟加拉国','印度','巴基斯坦','斯里兰卡','马尔代夫',
		'哈萨克斯坦','吉尔吉斯斯坦','塔吉克斯坦','乌兹别克斯坦','土库曼斯坦','阿富汗','伊拉克','伊朗','叙利亚','约旦',
		'黎巴嫩','以色列','巴勒斯坦','沙特阿拉伯','巴林','卡塔尔','科威特','阿拉伯联合酋长国','阿曼','也门','格鲁吉亚',
		'亚美尼亚','阿塞拜疆','土耳其','塞浦路斯','芬兰','瑞典','挪威','冰岛','丹麦','爱沙尼亚','拉脱维亚','立陶宛',
		'摩尔多瓦','白俄罗斯','俄罗斯','乌克兰','波兰','捷克','斯洛伐克','匈牙利','德国','奥地利','瑞士','列支敦士登',
		'英国','爱尔兰','荷兰','比利时','卢森堡','法国','摩纳哥','罗马尼亚','保加利亚','塞尔维亚','北马其顿','斯洛文尼亚',
		'克罗地亚','黑山','波斯尼亚和黑塞哥维那','波黑','阿尔巴尼亚','希腊','意大利','马耳他','梵蒂冈','圣马力诺','西班牙',
		'葡萄牙','安道尔','埃及','利比亚','突尼斯','阿尔及利亚','摩洛哥','苏丹','南苏丹','埃塞俄比亚','厄立特里亚','索马里',
		'吉布提','肯尼亚','坦桑尼亚','乌干达','卢旺达','布隆迪','塞舌尔','乍得','中非','喀麦隆','赤道几内亚','加蓬',
		'刚果共和国','刚果民主共和国','圣多美和普林西比','毛里塔尼亚','塞内加尔','冈比亚','马里','布基纳法索','几内亚',
		'几内亚比绍','佛得角','塞拉利昂','利比里亚','科特迪瓦','加纳','多哥','贝宁','尼日尔','尼日利亚 ','赞比亚',
		'安哥拉','津巴布韦','马拉维','莫桑比克','博茨瓦纳','纳米比亚','南非','斯威士兰','莱索托','马达加斯加','科摩罗',
		'毛里求斯','加拿大','美国','墨西哥','危地马拉','伯利兹','萨尔瓦多','洪都拉斯','尼加拉瓜','哥斯达黎加','巴拿马',
		'巴哈马','古巴','牙买加','海地','多米尼加','安提瓜和巴布达','圣基茨和尼维斯','多米尼克','圣卢西亚','圣文森特和格林纳丁斯',
		'格林纳达','巴巴多斯','特立尼达和多巴哥','哥伦比亚','委内瑞拉','圭亚那','苏里南','厄瓜多尔','秘鲁','玻利维亚','巴西',
		'智利','阿根廷','乌拉圭','巴拉圭','澳大利亚','新西兰','帕劳','密克罗尼西亚联邦','马绍尔群岛','基里巴斯','瑙鲁',
		'巴布亚新几内亚','所罗门群岛','瓦努阿图','斐济','图瓦卢','萨摩亚','汤加','库克群岛','纽埃');
	
	public function parse($content) {
		if (!is_array($content)) {
			$this->inputLines = explode("\n", $content);
		} else {
			$this->inputLines = $content;
		}
		
		if (empty($this->inputLines)) return;
		do {
			$hasNewItem = $this->findNewItem();
			if (!$hasNewItem) break;
			
			$this->findExportedCountry();
			$this->findItemNum();
		} while (isset($this->inputLines[$this->curParsedLineNo + 1]));
	}
	
	protected function findNewItem() {
		$this->curParsedLineNo++;
		while (isset($this->inputLines[$this->curParsedLineNo])) {
			$res = $this->isNewItem($this->inputLines[$this->curParsedLineNo]);
			if (!empty($res)) {
				$this->curItemId = $res['id'];
				$this->result[$res['id']] = $res;
				$this->curItemLineNo = $this->curParsedLineNo;
				return true;
			}
			$this->curParsedLineNo++;
		}
		
		return false;
	}
	
	protected function isNewItem($content) {
		$res = null;
		$n = preg_match('/(\d+)\s+(\d{10})/u', $content, $matches);
		if ($n > 0) {
			$res = ['id' => $matches[1], 'no' => $matches[2]];
		} else if ($this->reportVer < 1) {
			if (strpos($content, '单价/总价/币制') !== false) {
				$this->reportVer = 2;
			} else if (strpos($content, '单价') !== false && strpos($content, '商品编号') !== false) {
				$this->reportVer = 1;
			}
		}

		return $res;
	}
	
	protected function findExportedCountry() {
		$line = $this->inputLines[$this->curItemLineNo];
		$curItemNo = $this->result[$this->curItemId]['no'];
		$pos = strpos($line, $curItemNo);
		$line = trim(substr($line, $pos + strlen($curItemNo)));
		$tmp = preg_split('/\s+/u', $line);
		$countryIndex = -1;
		foreach ($tmp as $k => $v) {
			if (preg_match('/\([A-Z]+\)$/', $v) > 0) {
				$countryIndex = $k;
				$this->result[$this->curItemId]['country'] = $v;
				break;
			}
		        foreach ($this->countries as $v2) {
			    if (str_starts_with($v2, $v)) {
				$countryIndex = $k;
				$this->result[$this->curItemId]['country'] = $v;
				break 2;
			    }
			}
		}
		$n = count($tmp);
		if ($n < 5 || $countryIndex < 0) return;
		
		if ($countryIndex > 0) {
			$tmpWeightStr = $tmp[$countryIndex - 1];
			if (strpos($tmpWeightStr, '克') !== false) {
				$this->result[$this->curItemId]['weight'] = $tmpWeightStr;
			}
		}
		if ($countryIndex > 2) {
			$weightIndex = $countryIndex - 1;
			$name = $tmp[0];
			for ($i = 1; $i < $weightIndex; $i++) {
				$name .= ' ' . $tmp[$i];
			}
			$this->result[$this->curItemId]['name'] = $name;
		} else {
			$this->result[$this->curItemId]['name'] = $tmp[0];
		}
		if ($countryIndex + 1 < $n) {
			$this->result[$this->curItemId]['unit_price'] = $tmp[$countryIndex + 1];
		}
		if ($this->reportVer < 2 && $countryIndex + 2 < $n) {
			$this->result[$this->curItemId]['total_price'] = $tmp[$countryIndex + 2];
		} elseif ($this->reportVer == 2) {
			// 这个版本的总价在下面一行
			if (isset($this->inputLines[$this->curItemLineNo + 1])) {
				$nextLineStr = $this->inputLines[$this->curItemLineNo + 1];
				$tmp = explode('目的国', trim($nextLineStr), 2);
				if (count($tmp) == 2) {
					$tmpPart1 = preg_split('/\s+/u', trim($tmp[0]));
					$lastElem = $tmpPart1[count($tmpPart1) - 1];
					if (strpos($lastElem, '克') !== false) {
						$this->result[$this->curItemId]['weight'] = $lastElem;
					}

					$tmpPart2 = preg_split('/\s+/u', trim($tmp[1]));
					foreach ($tmpPart2 as $v) {
						if (is_numeric($v)) {
							$this->result[$this->curItemId]['total_price'] = $v;
							break;
						}
					}
				}
			}
		}
	}
	
	protected function findItemNum() {
		if (isset($this->result[$this->curItemId]['unit_price']) && isset($this->result[$this->curItemId]['total_price'])
			&& $this->result[$this->curItemId]['unit_price'] > 0 && $this->result[$this->curItemId]['total_price'] >= $this->result[$this->curItemId]['unit_price']
		) {
			
			$this->result[$this->curItemId]['num'] = $this->result[$this->curItemId]['total_price'] / $this->result[$this->curItemId]['unit_price'];
			$this->result[$this->curItemId]['num'] = (int) round($this->result[$this->curItemId]['num'], 0);
			return;
		}
		do {
			$line = $this->inputLines[$this->curParsedLineNo];
			$matches = null;
			$n = preg_match('/(\d+)(个|台|套)/u', $line, $matches);
			if ($n > 0) {
				$this->result[$this->curItemId]['num'] = trim($matches[1]);
			}
			
			$nextLineNo = $this->curParsedLineNo + 1;
			if (isset($this->inputLines[$nextLineNo]) && $this->isNewItem($this->inputLines[$nextLineNo])) {
				return;
			}
			$this->curParsedLineNo++;
		} while (isset($this->inputLines[$this->curParsedLineNo]));
	}
	
	public function getParsedResult() {
		return $this->result;
	}
}
