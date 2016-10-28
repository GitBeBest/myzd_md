<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '发现';
$urlResImage = $this->theme->baseUrl . "/images/";
$source = \Yii::$app->request->get('app', 1);
if ($source == 1) {
    $this->params['show_footer'] = false;
    $urlKaTeEr = Url::to(['home/page', 'view' => 'kataer']);
    $urlLuJinSong = Url::to(['home/page', 'view' => 'lujinsong']);
    $urlRenShanCheng = Url::to(['home/page', 'view' => 'renshancheng']);
    $urlMillionWelfare = Url::to(['home/page', 'view' => 'millionWelfare']);
    $urlForbes = Url::to(['home/page', 'view' => 'forbes']);
    $urlDiDoctor = Url::to(['home/page', 'view' => 'diDoctor']);
} else {
    $urlKaTeEr = Url::to(['home/page', 'view' => 'kataer', 'app' => 0]);
    $urlLuJinSong = Url::to(['home/page', 'view' => 'lujinsong', 'app' => 0]);
    $urlRenShanCheng = Url::to(['home/page', 'view' => 'renshancheng', 'app' => 0]);
    $urlMillionWelfare = Url::to(['home/page', 'view' => 'millionWelfare', 'app' => 0]);
    $urlForbes = Url::to(['home/page', 'view' => 'forbes', 'app' => 0]);
    $urlDiDoctor = Url::to(['home/page', 'view' => 'diDoctor', 'app' => 0]);
}
?>
<article id="findView_article" class="active" data-scroll="true" data-active="find_footer">
    <div>
        <div>
            <a href="<?= $urlKaTeEr; ?>">
                <img src="http://static.mingyizhudao.com/146345307724930" class="w100">
            </a>
        </div>
        <div class="pad10 text-center bg-white">
            卡塔尔王子中国寻医记
        </div>
        <div class="mt10">
            <a href="<?= $urlLuJinSong; ?>">
                <img src="http://static.mingyizhudao.com/146345357860329" class="w100">
            </a>
        </div>
        <div class="pad10 text-center bg-white">
            医生访谈陆劲松
        </div>
        <div class="mt10">
            <a href="<?= $urlRenShanCheng; ?>">
                <img src="http://static.mingyizhudao.com/146345375303173" class="w100">
            </a>
        </div>
        <div class="pad10 text-center bg-white">
            医生访谈任善成
        </div>
        <div class="mt10">
            <a href="<?= $urlMillionWelfare; ?>">
                <img src="http://static.mingyizhudao.com/146345400344854" class="w100">
            </a>
        </div>
        <div class="pad10 text-center bg-white">
            百万公益冬日暖阳
        </div>
        <div class="mt10">
            <a href="<?= $urlForbes; ?>">
                <img src="http://static.mingyizhudao.com/146345567301652" class="w100">
            </a>
        </div>
        <div class="pad10 text-center bg-white">
            名医主刀CEO入选亚洲年轻领袖榜单
        </div>
        <div class="mt10">
            <a href="<?= $urlDiDoctor; ?>">
                <img src="http://static.mingyizhudao.com/146349221777345" class="w100">
            </a>
        </div>
        <div class="pad10 text-center bg-white">
            一键呼叫专家医生，随车上门问诊
        </div>
    </div>
</article>