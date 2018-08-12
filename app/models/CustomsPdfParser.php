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
		$n = count($tmp);
		if ($n < 7) return;
		$this->result[$this->curItemId]['weight'] = $tmp[1];
		$this->result[$this->curItemId]['country'] = $tmp[2];
		$this->result[$this->curItemId]['name'] = $tmp[0];
		$this->result[$this->curItemId]['unit_price'] = $tmp[3];
		$this->result[$this->curItemId]['total_price'] = $tmp[4];
	}
	
	protected function findItemNum() {
		if (isset($this->result[$this->curItemId]['unit_price']) && isset($this->result[$this->curItemId]['total_price'])) {
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