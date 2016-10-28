<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '我的银行卡';
$urlResImage = $this->theme->baseUrl . "/images/";
$urlAjaxCardList = Url::to('ajax-card-list');
$urlCreate = Url::to(['create', 'addBackBtn' => 1]);
$urlViewInputKey = Url::to(['view-input-key', 'addBackBtn' => 1, 'type' => 1, 'id' => '']);
$urlDoctorView = Url::to('doctor/view');
$this->params['show_footer'] = false;
?>
<?php
if (isset($data) && !(is_null($data)) && (count($data->results->cards) > 0)) {
    ?>
    <footer class="bg-white">
        <a href="<?= $urlCreate; ?>" class="btn btn-block bg-yellow color-white" data-target="link">新增银行卡</a>
    </footer>
    <?php
}
?>
<article id="cardList_article" class="active" data-scroll="true">
    <div class="pad10">
        <div id="cardList">
            <?php
            if (isset($data) && !(is_null($data))) {
                $cards = $data->results->cards;
                for ($i = 0; $i < count($cards); $i++) {
                    $card = $cards[$i];
                    ?>
                    <div class="cardBg">
                        <div class="grid">
                            <div class="col-1">
                                <?= $card->bank; ?>
                            </div>
                            <div class="col-0">
                                <a href="<?= $urlViewInputKey; ?>/<?= $card->id; ?>" class="color-white">修改</a>
                            </div>
                        </div>
                        <div class="pt20 pb20">
                            <?= $card->cardNo; ?>
                        </div>
                        <div class="grid">
                            <div class="col-1">
                                持卡人：<?= $card->name; ?>
                            </div>
                            <div class="col-0">
                                <?php
                                if ($card->isDefault == 1) {
                                    echo '默认';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
        if (isset($data) && !(is_null($data)) && (count($data->results->cards) == 0)) {
            ?>
            <div class="text-center pt20">
                <img src="http://static.mingyizhudao.com/146659004210229" class="w170p">
            </div>
            <div class="text-center pt10">
                <div>
                    请您提供正确的银行卡信息，
                </div>
                <div>
                    名医主刀将用作为和您结账的工具。
                </div>
            </div>
            <div class="pt30">
                <a href="<?= $urlCreate; ?>" class="btn btn-full bg-yellow color-white" data-target="link">添加银行卡</a>
            </div>
            <?php
        }
        ?>
    </div>
</article>