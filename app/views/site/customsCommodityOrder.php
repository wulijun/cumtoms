<?php
use yii\helpers\Html;

$colNameMap = [
	'car' => ['id' => '项号', 'hscode' => '商品编号', 'chnproddesc' => '商品名称', 'modelname' => '规格型号',
        'qty' => '成交数量', 'unitprice' => '单价', 'totalamount' => '总价', 'currencyname' => '币制',
        'qty_0_val' => '法定第一数量', 'qty_0_name' => '法定第一计量单位', 'qty_1_val' => '法定第二数量',
        'qty_1_name' => '法定第二计量单位', 'orginalcountry' => '原产国（地区）',],
];

function _tpl_display_same($data, $name, $header) {
	if (empty($data)) return;
	$headerKey = [];
	$headerName = [];
	foreach ($header as $k => $v) {
		$headerKey[] = $k;
		$headerName[] = $v;
	}
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
    $i = 1;
	foreach ($data as $v) {
        $v['id'] = $i;
		echo '<tr>';
		foreach ($headerKey as $v2) {
			echo "<td>" . Html::encode($v[$v2]) . '</td>';
		}
		echo "</tr>\n";
        $i++;
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
    <h4 class="page-header">商品报关单</h4>
  </div>
</div>
<div class="well">
<div class="media">
  <div class="media-body search-box">
      <?= Html::beginForm('/site/customs-commodity-order', 'post', array(
          'enctype' => 'multipart/form-data', 'id' => 'form-search', 'class' => 'form-inline')) ?>
          <div class="form-group">
            Excel: <input accept="application/vnd.ms-excel,application/excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="excel-file-uploader" type="file" name="excelfile">
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary search-btn">开始生成</button>
            <a class="btn btn-default" href="/static/download/<?=$reqParam['downFilename']?>" role="button" target="_blank">下载Excel文件</a>
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
  // print_r($checkRes);die();
  _tpl_display_same($checkRes, '商品报关单', $colNameMap['car']);
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
