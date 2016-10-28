<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
/**
 * @var $this \yii\web\View
 */
$this->title = '名医主刀';
$urlBigEvent = Url::to(['home/page', 'view' => 'big-event', 'app' => 0]);
$urlNewList = Url::to(['home/page', 'view' => 'new-list', 'app' => 0]);
$urlRobot = Url::to(['home/page', 'view' => 'robot', 'app' => 0]);
$urlMyzd = Url::to(['home/page', 'view' => 'myzd', 'app' => 0]);
$urlViewContractDoctors = Url::to(['doctor/view-contract-doctors']);
$urlViewCommonweal = Url::to(['doctor/view-common-weal', 'app' => 0]);
$ajaxIndexAnnouncement = Url::to('/apimd/index-announcement');
$urlResImage = $this->theme->baseUrl . "/images/";
?>

<?= Html::cssFile('http://myzd.oss-cn-hangzhou.aliyuncs.com/static/mobile/js/jquery.bxslider/jquery.bxslider.css') ?>
<?= Html::jsFile('http://myzd.oss-cn-hangzhou.aliyuncs.com/static/mobile/js/jquery.bxslider/jquery.bxslider.min.js') ?>
<?= Html::jsFile('http://myzd.oss-cn-hangzhou.aliyuncs.com/static/mobile/js/jquery-1.9.1.min.js') ?>

<article id="home_article" class="active" data-scroll="true" data-active="home_footer">
    <div>
        <div id="banner" class="pb10">
            <div id="team-bxslider">
                <ul class="bxslider">

                </ul>
            </div>
        </div>
        <div class="grid pt20 pb20 bg-white">
            <div class="col-0 pl10 pr10 pb2 br-gray">
                <img src="http://static.mingyizhudao.com/146898693233368" class="h16p">
            </div>
            <div class="col-0 pl10 pr10">
                <img src="http://static.mingyizhudao.com/146898697093799" class="h20p">
            </div>
            <div id="information" class="col-1 pr10">
                <div id="team-bxslider">
                    <ul class="bxslider">
                    </ul>
                </div>
            </div>
        </div>
        <div class="pl5 pr5 pt10 pb10 grid">
            <div class="col-1 w50 bg-white mr5">
                <a href="<?= $urlViewCommonweal; ?>">
                    <div class="text-center pt20 pb20">
                        <img src="http://static.mingyizhudao.com/146880907449190" class="w80p">
                        <div class="pt10">
                            加入名医公益
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-1 w50 bg-white ml5">
                <a href="<?= $urlMyzd; ?>">
                    <div class="text-center pt20 pb20">
                        <img src="http://static.mingyizhudao.com/146880906689188" class="w80p">
                        <div class="pt10">
                            了解名医主刀
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="pb10">
            <a href="<?= $urlViewContractDoctors; ?>">
                <div class="pad20 grid bg-white">
                    <div class="col-1">
                        <img src="http://static.mingyizhudao.com/146880949657339" class="w70p">
                    </div>
                    <div class="col-0 pl5 pr5">
                        查看名医主刀签约专家
                    </div>
                    <div class="col-1">
                        <img src="http://static.mingyizhudao.com/146880950633542" class="w70p">
                    </div>
                </div>
            </a>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        //轮播图
        var html = '<li class="slide">' +
                '<a href="<?= $urlBigEvent; ?>">' +
                '<img class="w100" src="http://static.mingyizhudao.com/146881024079320">' +
                '</a>' +
                '</li>' +
                '<li class="slide">' +
                '<a href="<?= $urlNewList; ?>">' +
                '<img class="w100" src="http://static.mingyizhudao.com/146881024084983">' +
                '</a>' +
                '</li>' +
                '<li class="slide">' +
                '<a href="<?= $urlRobot; ?>">' +
                '<img class="w100" src="http://static.mingyizhudao.com/146881024062046">' +
                '</a>' +
                '</li>';
        $('#banner .bxslider').html(html);
        $('#banner .bxslider').bxSlider({
            mode: 'fade',
            slideMargin: 0,
            controls: false,
            auto: true
        });

        //咨询
        $.ajax({
            url: '<?php echo $ajaxIndexAnnouncement; ?>',
            success: function (data) {
                readyInf(data);
            }
        });
        function readyInf(data) {
            var inf = '';
            var doctors = data.results.doctors;
            if (doctors.length > 0) {
                for (var i = 0; i < doctors.length; i++) {
                    inf += '<li class="slide pt2 textOmitted">' + doctors[i] + '</li>';
                }
            }
            $('#information .bxslider').html(inf);
            $('#information .bxslider').bxSlider({
                mode: 'fade',
                slideMargin: 0,
                controls: false,
                auto: true,
                pager: false
            });
        }

    });
</script>