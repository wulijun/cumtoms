<?php
use yii\helpers\Html;

$colNameMap = [
	'pdfonly' => ['id' => 'ID', 'name' => '中文描述', 'no' => '商品编码', 'num' => '数量', 'unit_price' => '单价', 'total_price' => 'CIF总价', 'country' => '原产国'],
	'excelonly' => ['id' => 'ID', 'name' => '中文描述', 'no' => '商品编码', 'num' => '数量', 'unit_price' => '单价', 'total_price' => 'CIF总价', 'country' => '原产国'],
];

function _tpl_display_same($data, $key, $name, $header, $excelFile, $pdfFile) {
	if (empty($data[$key])) return;
	$headerKey = [];
	$headerName = [];
	foreach ($header as $k => $v) {
		$headerKey[] = $k;
		$headerName[] = $v;
	}
	$name = sprintf('%s(%s 对比 %s)', $name, Html::encode($excelFile), Html::encode($pdfFile));
	echo <<<EOT
    <div class="panel panel-default same-item-div">
      <div class="panel-heading">{$name}</div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover">
            <tbody>
			<tr>
EOT;
	foreach ($headerName as $v) {
		echo "<th>{$v}</th>";
	}
	echo "</tr>\n";
	foreach ($data[$key] as $v) {
		echo '<tr>';
		foreach ($headerKey as $v2) {
			echo "<td>" . Html::encode($v[$v2]) . '</td>';
		}
		echo "</tr>\n";
	}
	echo <<<EOT
            </tbody>
          </table>
        </div>
      </div>
    </div>
EOT;
}
	
function _tpl_display_diff($data, $key, $name, $header, $excelFile, $pdfFile) {
	if (empty($data[$key])) return;
	$headerKey = [];
	$headerName = [];
	foreach ($header as $k => $v) {
		$headerKey[] = $k;
		$headerName[] = $v;
	}
	$name = sprintf('%s(%s 对比 %s)', $name, Html::encode($excelFile), Html::encode($pdfFile));
	echo <<<EOT
    <div class="panel panel-default">
      <div class="panel-heading"><p class="h4 text-danger">{$name}</p></div>
      <div class="panel-body">
        <div class="table-responsive bg-warning">
          <table class="table table-bordered table-hover">
            <tbody>
			<tr>
EOT;
	foreach ($headerName as $v) {
		echo "<th>{$v}</th>";
	}
	echo "</tr>\n";
	foreach ($data[$key] as $v) {
		echo '<tr>';
		foreach ($headerKey as $v2) {
			$tmp = Html::encode($v[$v2]);
			if (isset($v['diff_col'][$v2])) {
				$tmp2 = $v['excel_row'][$v['diff_col'][$v2]];
				$tmp .= '<p class="bg-danger">Excel: '.Html::encode($tmp2).'</p>';
			}			
			echo "<td>{$tmp}</td>";
		}
		echo "</tr>\n";
	}
	echo <<<EOT
            </tbody>
          </table>
        </div>
      </div>
    </div>
EOT;
}

?>
<div class="row">
  <div class="col-lg-12">
    <h4 class="page-header">报关清单对账(PDF)</h4>
  </div>
</div>
<div class="well">
<div class="media">
  <div class="media-body search-box">
      <?= Html::beginForm('/site/customs-pdf-check', 'post', array(
          'enctype' => 'multipart/form-data', 'id' => 'form-search', 'class' => 'form-inline')) ?>
          <div class="form-group">
            Excel: <input accept="application/vnd.ms-excel,application/excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="excel-file-uploader" type="file" name="excelfile">
          </div>
		  <div class="form-group">
            PDF: <input accept="application/pdf" id="pdf-file-uploader" type="file" name="pdffile[]" multiple="multiple">
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary search-btn">开始比对</button>
            <button type="button" id="hide-same-item" class="btn btn-primary" data-toggle-text="显示信息一致的商品">隐藏信息一致的商品</button>
          </div>                   
        </form>
  </div>
  <?php if ($errMsg != '') {
  	echo '<p class="bg-danger" style="margin-top:15px">'.Html::encode($errMsg).'</p>';
  }?>
</div>
</div>
<div class="row">
  <div class="col-lg-12">
  <?php
  foreach ($checkRes as $v) {
      _tpl_display_diff($v['res'], 'pdfonly', '只在PDF中出现的商品', $colNameMap['pdfonly'], $reqParam['excelFilename'], $v['name']);
      _tpl_display_diff($v['res'], 'excelonly', '只在Excel中出现的商品', $colNameMap['excelonly'], $reqParam['excelFilename'], $v['name']);
      _tpl_display_diff($v['res'], 'diff', '不一致的商品', $colNameMap['pdfonly'], $reqParam['excelFilename'], $v['name']);
      _tpl_display_same($v['res'], 'same', '信息一致的商品', $colNameMap['pdfonly'], $reqParam['excelFilename'], $v['name']);
  }
  ?>
  </div>
</div>
<script>
$(function() {
	$('#hide-same-item').click(function() {
		var curObj = $(this);
		var tmpTxt = curObj.text();
		curObj.text(curObj.data("toggle-text"));
		curObj.data("toggle-text", tmpTxt);
		$('.same-item-div').toggle();
	});
});
</script>
