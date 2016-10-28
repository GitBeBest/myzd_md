<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\user\User;

/**
 * @var $this View
 * @var $user
 */
$this->title = '上传患者病历';
$urlLogin = Url::to('doctor/login');
$patientId = $output['id'];
$urlSubmitMR = Url::to('patient/ajax-create-patient-mr');
$urlUploadFile = 'http://file.mingyizhudao.com/api/uploadparientmr';
$urlReturn = Url::to(['patient/view', 'id' => $patientId]);

$patientBookingId = \Yii::$app->request->get('patientBookingId', '');
$patientAjaxTask = Url::to(['patient/ajax-task', 'id' => '']);

$reportType = \Yii::$app->request->get('report_type', 'mr');
$bookingId = \Yii::$app->request->get('bookingid', '');

$type = \Yii::$app->request->get('type', 'create');
if ($type == 'update') {
    $urlReturn = Url::to(['patient/view', 'id' => $patientId, 'addBackBtn' => 1]);
} else if ($type == 'create') {
    if ($output['returnUrl'] == '') {
        $urlReturn = Url::to(['patient-booking/create', 'pid' => $patientId, 'addBackBtn' => 1]);
    } else if ($reportType == 'da') {
        $urlReturn = Url::to(['order/orderView', 'booking_id' => $bookingId, 'addBackBtn' => 1]);
    } else {
        $urlReturn = $output['returnUrl'];
    }
}
if (isset($output['id'])) {
    $urlPatientMRFiles = 'http://file.mingyizhudao.com/api/loadpatientmr?userId=' . $user->id . '&patientId=' . $patientId . '&reportType=' . $reportType;
    $urlDeletePatientMRFile = 'http://file.mingyizhudao.com/api/deletepatientmr?userId=' . $user->id . '&id=';
} else {
    $urlPatientMRFiles = "";
    $urlDeletePatientMRFile = "";
}
$urlResImage = $this->theme->baseUrl . "/images/";
$this->params['show_footer'] = false;
?>

<?=
Html::cssFile('http://myzd.oss-cn-hangzhou.aliyuncs.com/static/mobile/js/webuploader/css/webuploader.css');
Html::cssFile('http://static.mingyizhudao.com/webuploader.custom.1.0.css');
Html::jsFile('http://myzd.oss-cn-hangzhou.aliyuncs.com/static/mobile/js/webuploader/js/webuploader.min.js');
Html::jsFile('http://static.mingyizhudao.com/md2/uploadMRFile.min.1.2.js');
?>
<article id="updateMRFileIos_article" class="active" data-scroll="true">
    <div class="form-wrapper">
        <form id="patient-form" data-url-uploadfile="<?= $urlUploadFile; ?>" data-url-return="<?= $urlReturn; ?>" data-patientBookingId="<?= $patientBookingId; ?>" data-patientAjaxTask="<?= $patientAjaxTask; ?>">
            <input id="patientId" type="hidden" name="patient[id]" value="<?= $output['id']; ?>" />
            <input id="reportType" type="hidden" name="patient[report_type]" value="mr" />
        </form>
        <div class="pad10">
            <div class="text-center mt20">
                <div class="font-w800">
                    请您上传患者的相关病历资料
                </div>
                <div class="font-s12 color-gray">
                    (图片需清晰可见，最多9张)
                </div>
            </div>
            <div class="mt20">
                <!--图片上传区域 -->
                <div id="uploader" class="wu-example">
                    <!--<div class="imglist"><ul class="filelist"></ul></div>-->
                    <div class="queueList">
                        <div id="dndArea" class="placeholder">
                            <div class="exampleSection mb20">
                                <div class="text-center font-s14 color-black">
                                    示例(如CT、磁共振、病理报告等)
                                </div>
                                <div class="grid mt20">
                                    <div class="col-1 w40">
                                        <img src="http://static.mingyizhudao.com/147433914906338"/>
                                    </div>
                                    <div class="col-1 w20">

                                    </div>
                                    <div class="col-1 w40">
                                        <img src="http://static.mingyizhudao.com/146968742128587"/>
                                    </div>
                                </div>
                            </div>
                            <div id="filePicker"></div>
                            <div class="mt10">
                                <?php
                                if ($type == 'create') {
                                    echo '<a href="javascript:;" class="btn btn-full skipBtn">稍后补充</a>' .
                                    '<div class="text-center color-gray font-s12">(提交订单后可在订单详情里补充)</div>';
                                }
                                ?>
                            </div>
                            <!-- <p>或将照片拖到这里，单次最多可选10张</p>-->
                        </div>
                    </div>
                    <div class="statusBar" style="display:none; padding-bottom: 40px;">
                        <div class="progress">
                            <span class="text">0%</span>
                            <span class="percentage"></span>
                        </div>
                        <div class="info hide"></div>
                        <div class="">
                            <!-- btn 继续添加 -->
                            <!--<div id="filePicker2" class="pull-right"></div>-->
                            <ul class="filelist">
                                <li id="filePicker3" class="btn-add-img">+</li>
                            </ul>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pt20 pb20">
                            <a id="btnSubmit" class="statusBar uploadBtn state-pedding btn btn-yes btn-full">确认</a>
                        </div>
                    </div>
                    <!--一开始就显示提交按钮就注释上面的提交 取消下面的注释 -->
                    <!--<div class="statusBar uploadBtn">提交</div>-->
                </div>
            </div>
        </div>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function () {
        $('.skipBtn').click(function () {
            sessionStorage.removeItem('intention');
            sessionStorage.removeItem('detail');
            location.href = '<?= $urlReturn; ?>';
        });
        var urlPatientMRFiles = "<?= $urlPatientMRFiles; ?>";
//        $.ajax({
//            url: urlPatientMRFiles,
//            success: function(data) {
//                setImgHtml(data.results);
//            }
//        });
    });
    function setImgHtml(files) {
        var innerHtml = '';
        var imgfiles = files.files;
        if (imgfiles && imgfiles.length > 0) {
            for (i = 0; i < imgfiles.length; i++) {
                var imgfile = imgfiles[i];
                innerHtml +=
                        '<li id="' +
                        imgfile.id + '"><p class="imgWrap"><img src="' +
                        imgfile.thumbnailUrl + '" data-src="' +
                        imgfile.absFileUrl + '"></p><div class="file-panel delete">删除</div></li>';
            }
        } else {
            innerHtml += '';
        }
        $(".imglist .filelist").html(innerHtml);
        initDelete();
    }
    function initDelete() {
        $('.imglist .delete').tap(function () {
            domLi = $(this).parents("li");
            id = domLi.attr("id");
            J.confirm('提示', '确定删除这张图片?', function () {
                deleteImg(id, domLi);
            }, function () {
                J.showToast('取消', '', 1000);
            });
        });
    }
    function deleteImg(id, domLi) {
        J.showMask();
        var urlDeletePatientMRFile = '<?= $urlDeletePatientMRFile ?>' + id;
        $.ajax({
            url: urlDeletePatientMRFile,
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status == 'ok') {
                    domLi.remove();
                    J.showToast('删除成功!', '', 1000);
                } else {
                    J.showToast(data.error, '', 3000);
                }
            },
            complete: function () {
                J.hideMask();
            }
        });
    }
</script>

