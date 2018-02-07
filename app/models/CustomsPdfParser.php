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
		$n = preg_match('/(\d+)\s+(\d{8}\.\d{2})/u', $content, $matches);
		if ($n > 0) {
			$res = ['id' => $matches[1], 'no' => $matches[2]];
		}

		return $res;
	}
	
	protected function findExportedCountry() {
		$line = $this->inputLines[$this->curItemLineNo];
		$curItemNo = $this->result[$this->curItemId]['no'];
		$pos = strpos($line, $curItemNo);
		$n = preg_match('/\d+[^\s]+/u', $line, $matches, 0, $pos + strlen($curItemNo));
		if ($n < 1) return;		
		$weightAndCountry = $matches[0];
		$this->result[$this->curItemId]['weight_country'] = $weightAndCountry;
		
		$nameEnd = strpos($line, $weightAndCountry);
		$nameStart = $pos + strlen($curItemNo);
		$itemName = substr($line, $nameStart, $nameEnd - $nameStart);
		$this->result[$this->curItemId]['name'] = trim($itemName);
		
		//可能有单价和总价
		$matches = null;
		$n = preg_match('/(\s+\d+[\d\.]*)\s+(\d+[\d\.]*)/u', $line, $matches, 0, $nameEnd+ strlen($weightAndCountry));
		if ($n > 0) {
			$this->result[$this->curItemId]['unit_price'] = trim($matches[1]);
			$this->result[$this->curItemId]['total_price'] = trim($matches[2]);
		}
	}
	
	protected function findItemNum() {
		do {
			$line = $this->inputLines[$this->curParsedLineNo];
			$matches = null;
			$n = preg_match('/(\d+)(个|台)\s+(\d+[\d\.]*)\s+(\d+[\d\.]*)/u', $line, $matches);			
			if ($n > 0) {
				$this->result[$this->curItemId]['num'] = trim($matches[1]);
				$this->result[$this->curItemId]['unit_price'] = trim($matches[3]);
				$this->result[$this->curItemId]['total_price'] = trim($matches[4]);
				return;
			} else {
				$matches = null;
				$n = preg_match('/(\d+)(个|台)/u', $line, $matches);
				if ($n > 0) {
					$this->result[$this->curItemId]['num'] = trim($matches[1]);
				}
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