<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 * @var $data
 */
$this->title = '收到的预约';
$urlDoctorView = Url::toRoute('doctor/view');
$urlResImage = $this->theme->baseUrl . "/images/";
$this->params['show_footer'] = false;
?>
<header class="bg-green">
    <nav class="left">
        <a href="" data-target="back">
            <div class="pl5">
                <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
            </div>
        </a>
    </nav>
    <ul class="control-group">
        <li data-booking="processing" class="bookingMenu active">待处理</li>
        <li data-booking="done" class="bookingMenu">已完成</li>
    </ul>
    <nav class="right">
        <a class="header-user" data-target="link" data-icon="user" href="<?= $urlDoctorView ?>">
            <i class="icon user"></i>
        </a>
    </nav>
</header>
<article id="doctorPatientBookingList_article" class="active" data-scroll="true">
    <div class="pb20">
        <div class="processingList">
            <?php
            $processingList = $data->results->processingList;
            if ($processingList) {
                for ($i = 0; $i < count($processingList); $i++) {
                    $processingBooking = $processingList[$i];
                    ?>
                    <a href="<?= Url::to(['doctor-patient-booking', 'id' => $processingBooking->id, 'type' => $processingBooking->bkType, 'addBackBtn' => 1]); ?>" data-target="link">
                        <div class="p10 bt5-gray">
                            <div class="text-right font-s12 color-green">发送时间：
                                <?= $processingBooking->dateUpdated; ?>
                            </div>
                            <div class="grid">
                                <div class="col-0">患者姓名:</div>
                                <div class="col-1 pl5"><?= $processingBooking->name; ?></div>
                            </div>
                            <div class="grid mt10">
                                <div class="col-0">疾病名称:</div>
                                <div class="col-1 pl5"><?= $processingBooking->diseaseName; ?></div>
                            </div>
                            <div class="grid mt10 mb10">
                                <div class="col-0">就诊意向:</div>
                                <div class="col-1 pl5"><?= $processingBooking->travelType; ?></div>
                            </div>
                        </div>
                    </a>
                    <?php
                }
            } else {
                ?>
                <div class="mb10">
                    <div class="mt50 text-center">
                        <img src="http://static.mingyizhudao.com/146295490734874" class="w170p">
                    </div>
                    <div class="text-center font-s24 color-gray9">暂无预约信息</div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="doneList hide">
            <?php
            $doneList = $data->results->doneList;
            if ($doneList) {
                for ($i = 0; $i < count($doneList); $i++) {
                    $doneBooking = $doneList[$i];
                    ?>
                    <a href="<?= Url::to(['doctor-patient-booking', 'id' => $doneBooking->id, 'type' => $doneBooking->bkType, 'addBackBtn' => 1]); ?>" data-target="link">
                        <div class="p10 bt5-gray grid">
                            <div class="col-0 grid middle mr10">
                                <?php
                                if ($doneBooking->doctorAccept == 1) {
                                    echo '<img src="http://static.mingyizhudao.com/146968750757337" class="w35p">';
                                } else {
                                    echo '<img src="http://static.mingyizhudao.com/146968754577748" class="w35p">';
                                }
                                ?>
                            </div>
                            <div class="col-1">
                                <div class="text-right font-s12 color-green">发送时间：
                                    <?php
                                    if (isset($doneBooking->dateUpdated)) {
                                        echo $doneBooking->dateUpdated;
                                    } else {
                                        echo $doneBooking->dateCreated;
                                    }
                                    ?>
                                </div>
                                <div class="grid">
                                    <div class="col-0">患者姓名:</div>
                                    <div class="col-1 pl5"><?= $doneBooking->name; ?></div>
                                </div>
                                <div class="grid mt10">
                                    <div class="col-0">疾病名称:</div>
                                    <div class="col-1 pl5"><?= $doneBooking->diseaseName; ?></div>
                                </div>
                                <div class="grid mt10 mb10">
                                    <div class="col-0">就诊意向:</div>
                                    <div class="col-1 pl5"><?= $doneBooking->travelType; ?></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php
                }
            } else {
                ?>
                <div class="mb10">
                    <div class="mt50 text-center">
                        <img src="http://static.mingyizhudao.com/146295490734874" class="w170p">
                    </div>
                    <div class="text-center font-s24 color-gray9">暂无预约信息</div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        $(".bookingMenu").tap(function () {
            var dataBooking = $(this).attr('data-booking');
            if (dataBooking == 'processing') {
                $('.doneList').addClass('hide');
                $('.processingList').removeClass('hide');
                $('#doctorPatientBookingList_article').scrollTop(0);
            } else {
                $('.processingList').addClass('hide');
                $('.doneList').removeClass('hide');
                $('#doctorPatientBookingList_article').scrollTop(0);
            }
        });
    });
</script>