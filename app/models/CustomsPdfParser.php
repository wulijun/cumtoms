<?php
namespace app\models;

class CustomsPdfParser
{
	protected $inputLines;
	protected $result = [];
	protected $curItemLineNo = 0;
	protected $curParsedLineNo = -1;
	protected $curItemId;
	
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
		}
		$n = count($tmp);
		if ($n < 7 || $countryIndex < 0) return;
		
		if ($countryIndex > 0) {
			$this->result[$this->curItemId]['weight'] = $tmp[$countryIndex - 1];
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
		if ($countryIndex + 2 < $n) {
			$this->result[$this->curItemId]['unit_price'] = $tmp[$countryIndex + 1];
			$this->result[$this->curItemId]['total_price'] = $tmp[$countryIndex + 2];
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
			$n = preg_match('/(\d+)(个|台)/u', $line, $matches);
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