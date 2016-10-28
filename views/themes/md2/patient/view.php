<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 * @var $data
 * @var $user_id
 */
$this->title = '患者详情';
$patientInfo = $data->results->patientInfo;
/*
 * source
 * 0:正常途径查看患者详情
 * 1:签约专家途径（患者含有疾病信息，仅供查看）
 * 2:签约专家途径（患者无疾病信息，完善疾病信息）
 */
$source = \Yii::$app->request->get('source', 0);
$id = \Yii::$app->request->get('id', '');
$urlSubmit = Url::toRoute('doctor/add-disease');
$doctorId = \Yii::$app->request->get('doctorId', '');
$urlChooseList = Url::to(['choose-list', 'id' => $doctorId, 'patientId' => $id, 'addBackBtn' => 1]);
$urlUpdatePatientMR = Url::to(['update-patient-mr', 'id' => $patientInfo->id, 'addBackBtn' => 1]);
$urlUploadMRFile = Url::to(['upload-mr-file', 'id' => $patientInfo->id, 'type' => 'update', 'addBackBtn' => 1]);
$urlPatientFiles = 'http://file.mingyizhudao.com/api/loadpatientmr?userId=' . $user_id . '&patientId=' . $patientInfo->id . '&reportType=mr';
$urlPatientBookingCreate = Url::to(['patient-booking/create', 'pid' => $patientInfo->id, 'addBackBtn' => 1]);
$this->params['show_footer'] = false;
?>
<style>
    #jingle_popup{top:0px!important;}
</style>
<?php
if ($source != 1) {
    ?>
    <footer class='bg-white'>
        <button id='patientBookingCreate' class="btn btn-block bg-green">
            <?= $source == 0 ? '继续创建' : '补充疾病信息'; ?>
        </button>
    </footer>
    <?php
}
?>
<article id="patientView_article" class="active" data-scroll="true">
    <div class="pt10 pb10">
        <div class=" bg-white  mb20">

            <div class="grid pb10 pl20 pr20 bb-gray">
                <div class="col-1 w30">患者姓名</div>
                <div class="col-1 w70 text-right"><?= $patientInfo->name; ?></div>
            </div>
            <div class="grid pad10 bb-gray">
                <div class="col-1 pl10 w30">联系方式</div>
                <div class="col-1 pr10 w70 text-right"><?= $patientInfo->mobile; ?></div>
            </div>
            <?php
            $yearly = $patientInfo->age;
            $yearlyText = '';
            $monthly = "";
            if ($yearly == 0 && $patientInfo->ageMonth >= 0) {
                $yearlyText = '';
                $monthly = $patientInfo->ageMonth . '个月';
            } else if ($yearly <= 5 && $patientInfo->ageMonth > 0) {
                $yearlyText = $yearly . '岁';
                $monthly = $patientInfo->ageMonth . '个月';
            } else if ($yearly > 5 && $patientInfo->ageMonth > 0) {
                $yearly++;
                $yearlyText = $yearly . '岁';
            } else {
                $yearlyText = $yearly . '岁';
            }
            ?>
            <div class="grid pad10 bb-gray">
                <div class="col-1 pl10 w30">患者年龄</div>
                <div class="col-1 pr10 w70 text-right"><?= $yearlyText . $monthly; ?></div>
            </div>
            <div class="grid pad10 bb1-gray">
                <div class="col-1 pl10 w30">所在城市</div>
                <div class="col-1 w70 pr10 text-right"><?= $patientInfo->cityName; ?></div>
            </div>
            <div class="grid pad10 bb-gray">
                <div class="col-1 w30 pl10">疾病名称</div>
                <!-- <div class="col-1 w70 text-right"><?= $patientInfo->diseaseName; ?></div> -->
                <?php if ($patientInfo->diseaseDetail == '') { ?>
                    <div class="col-1 w70 pr10 text-right font-hs1">未选择</div>
                <?php } else { ?>
                    <div class="col-1 w70 pr10 text-right "><?= $patientInfo->diseaseName; ?></div>
                <?php } ?>
            </div>
            <div class="pad10 bb-gray">
                <?php if ($patientInfo->diseaseDetail == '') { ?>
                    <div class="text-center  font-hs1 ">暂没填写疾病描述</div>
                <?php } else { ?>
                    <div class="mt5 pl10 font-hs1 word-wrap"><?= $patientInfo->diseaseDetail; ?></div>
                <?php } ?>
            </div>
            <div class="pad10">
                <div class=" pl10 pr10 imglist ">

                </div>
            </div>
        </div>
    </div>
</div>
</article>
<script type="text/javascript">
    Zepto(function($) {
        id = "<?= $patientInfo->id; ?>";
        if (id) {
            ajaxPatientFiles();
        }
        $(".confirmPage").tap(function() {
            $(this).hide();
        });
        $('#patientBookingCreate').tap(function() {
            sessionStorage.removeItem('intention');
            sessionStorage.removeItem('detail');
            var diseaseName = '<?= $patientInfo->diseaseName ?>';
            var diseaseDetail = '<?= $patientInfo->diseaseDetail ?>';
            if ('<?= $source == 0; ?>') {
                if (diseaseDetail == '' && diseaseName == '') {
                    location.href = '<?= $urlSubmit . '?id=' . $id; ?>' + '&returnUrl';
                } else if (_imgfiles.length == 0) {
                    location.href = '<?= $urlUploadMRFile; ?>';
                } else {
                    location.href = '<?= $urlPatientBookingCreate; ?>';
                }
            } else if ('<?= $source == 2; ?>') {
                location.href = '<?= $urlSubmit . '?source=1&id=' . $id; ?>' + '&returnUrl=<?= $urlChooseList; ?>';
            }
        });
    });
    function ajaxPatientFiles() {
        urlPatientFiles = "<?= $urlPatientFiles; ?>";

        $.ajax({
            url: urlPatientFiles,
            success: function(data) {

                setImgHtml(data.results.files);
            }
        });
    }
    var _imgfiles = '';
    function setImgHtml(imgfiles) {
        _imgfiles = imgfiles;
        var innerHtml = '';
        var uiBlock = '';
        if (imgfiles && imgfiles.length > 0) {
            for (i = 0; i < imgfiles.length; i++) {
                if (i % 2 == 0) {
                    innerHtml += '<div class="grid mt10">';
                }
                innerHtml +=
                        '<div class="col-0 pl10 pr10 w50 text-center"><a class="btn_alert"><img class="" data-src="' + imgfiles[i].absFileUrl + '" src="' +
                        imgfiles[i].thumbnailUrl + '" /></div>';
                if (i % 2 == 1) {
                    innerHtml += '</div>';
                }
            }
        } else {
            var url = $('.imgUrl').attr('href');
            innerHtml += '<div class="grid">' +
                    '<div class=" col-0 bgzhaox "style="width:72px;height:72px;">' +
                    '</div>' +
                    '<div class="col-1 font-hs1 vertical-center">' +
                    '<div > ' +
                    '<div >暂无影像资料</div>' +
                    '<div>您可以在提交后在订单详情里补充</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

        }
        $(".imglist").html(innerHtml);
        $('.btn_alert').tap(function() {
            var imgUrl = $(this).find("img").attr("data-src");
            J.popup({
                html: '<div class="imgpopup"><img src="' + imgUrl + '"></div>',
                pos: 'top-second',
                showCloseBtn: true
            });
            $('.imgpopup').tap(function() {
                J.closePopup();
            });
        });
    }
</script>
