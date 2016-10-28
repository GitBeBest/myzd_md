<?php
use yii\helpers\Url;
use yii\web\View;
/**
 * @var $this View
 */
$urlCreatePatient = Url::to(['patient/create', 'addBackBtn' => 1, 'status' => 0]);
$urlDoctorView = Url::toRoute('doctor/view');
$urlHome = Url::to(['home/page', 'view' => 'home']);
$urlFindView = Url::to(['home/page', 'view' => 'find-view', 'app' => 0]);
$urlPatientBookingList = Url::to(['patient-booking/list', 'status' => 0, 'addBackBtn' => 1]);
$urlResImage = $this->theme->baseUrl . "/images/";
?>
<footer id="public_footer" class="bg-white hide">
    <div class="">
        <a href="<?= $urlCreatePatient; ?>" class="createIcon"></a>
    </div>
    <ul class="control-group w100">
        <li class="w25" data-active="home_footer">
            <a href="<?= $urlHome; ?>">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 homeIcon"></div>
                    <div class="col-1"></div>
                </div>
                <div class="font-s12">首页</div>
            </a>
        </li>
        <li class="w25" data-active="find_footer">
            <a href="<?= $urlFindView; ?>">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 findIcon"></div>
                    <div class="col-1"></div>
                </div>
                <div class="font-s12">发现</div>
            </a>
        </li>
        <li class="w25" data-active="create_footer">
            <a href="<?= $urlCreatePatient; ?>">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0">
                    </div>
                    <div class="col-1"></div>
                </div>
                <div class="font-s12 pt24">创建</div>
            </a>
        </li>
        <li class="w25" data-active="order_footer">
            <a href="<?= $urlPatientBookingList; ?>">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 orderIcon"></div>
                    <div class="col-1"></div>
                </div>
                <div class="font-s12">订单</div>
            </a>
        </li>
        <li class="w25" data-active="center_footer">
            <a href="<?= $urlDoctorView; ?>">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 centerIcon"></div>
                    <div class="col-1"></div>
                </div>
                <div class="font-s12">我的</div>
            </a>
        </li>
    </ul>
</footer>
<script>
    /*新页面，自动给footer中添加active*/
    $(document).ready(function () {
        var active = $('article').attr('data-active');

        $('footer li').each(function () {
            if ($(this).attr('data-active') == active) {
                $(this).addClass('active');
            }
        });
        $('footer').removeClass('hide');
    });
</script>