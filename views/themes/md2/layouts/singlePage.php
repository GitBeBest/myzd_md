<!DOCTYPE html>
<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\web\View;
use yii\helpers\Html;
?>
<html lang="zh" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta charset="utf-8" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />
    <title><?php echo $this->title; ?></title>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <!--<meta http-equiv="cache-control" max-age="600" />-->
    <link rel="shortcut icon" type="image/ico" href="http://myzd.oss-cn-hangzhou.aliyuncs.com/static/mobile/css/icons/favicon.ico" />
    <?= Html::cssFile('http://static.mingyizhudao.com/common.min.1.0.css') ?>
    <?= Html::cssFile('http://static.mingyizhudao.com/md2/custom.min.2.3.css') ?>

    <?= Html::jsFile('http://static.mingyizhudao.com/zepto.min.1.0.js') ?>
    <?= Html::jsFile('http://static.mingyizhudao.com/common.min.1.0.js') ?>
    <?= Html::jsFile('http://static.mingyizhudao.com/main.min.1.0.js') ?>
</head>
<body>
<div id="section_container">
    <section id="main_section" class="active" data-init="true">
        <?php
        if (!isset($this->params['show_footer'])||(isset($this->params['show_footer']) && $this->params['show_footer'] == true)) {
            $this->beginContent('@app/views/themes/md2/layouts/footer.php');
            $this->endContent();
        }
        ?>
        <?php
            echo $content;
        ?>
    </section>
</div>
</body>
</html>