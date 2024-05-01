<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = 'Customs平台';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no, email=no">
    <title><?= Html::encode($this->title) ?></title>
    <link href="/static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="/static/css/sb-admin-2.css?v=1" rel="stylesheet">
    <link href="/static/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="/static/vendor/jquery/jquery.min.js"></script>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Customs平台</a>
            </div>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="/"><i class="fa fa-dashboard fa-fw"></i>&nbsp;首页</a>
                        </li>
                        <li>
                            <a href="/site/customs-excel-check"><i class="fa fa-check-circle-o fa-fw"></i>&nbsp;Excel清单对账</a>
                        </li>
                        <li>
                            <a href="/site/customs-pdf-check"><i class="fa fa-check-circle-o fa-fw"></i>&nbsp;PDF清单对账</a>
                        </li>
                        <li>
                            <a href="/site/customs-commodity-order"><i class="fa fa-check-circle-o fa-fw"></i>&nbsp;商品报关单</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <!-- Page Content -->
        <div id="page-wrapper">
        <?php echo $content;?>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->    
    <script src="/static/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="/static/vendor/metisMenu/metisMenu.min.js"></script>
    <script src="/static/js/sb-admin-2.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>