<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\user\User;

/**
 * @var $this View
 * @var $user User
 */
$this->title = '上传患者病历';
$urlLogin = Url::to('doctor/login');
$patientId = $output['id'];
$urlSubmitMR = Url::to('patient/ajax-create-patient-mr');
$urlUploadFile = Url::toRoute('qiniu/ajax-patient-mr');
$urlQiniuAjaxToken = Url::toRoute('qiniu/ajax-patient-token');
$urlReturn = Url::to(['patient/view', 'id' => $patientId]);

$patientBookingId = \Yii::$app->request->get('patientBookingId', '');
$patientAjaxTask = Url::to(['patient/ajax-task', 'id' => '']);

$type = \Yii::$app->request->get('type', 'create');
if ($type == 'update') {
    $urlReturn = Url::to(['patient/view', 'id' => $patientId, 'addBackBtn' => 1]);
} else if ($type == 'create') {
    if ($output['returnUrl'] == '') {
        $urlReturn = Url::to(['patient-booking/create', 'pid' => $patientId, 'addBackBtn' => 1]);
    } else {
        $urlReturn = $output['returnUrl'];
    }
}

if (isset($output['id'])) {
    $urlPatientMRFiles = Url::to(['http://file.mingyizhudao.com/api/loadpatientmr','userId' =>$user->id, 'patientId' => $patientId, 'reportType' => 'mr']);
    $urlDeletePatientMRFile = 'http://file.mingyizhudao.com/api/deletepatientmr?userId=' . $user->id . '&id=';
} else {
    $urlPatientMRFiles = "";
    $urlDeletePatientMRFile = "";
}
$urlResImage = $this->theme->baseUrl . "/images/";
$this->params['show_footer'] = false;
?>

<?= Html::cssFile('http://static.mingyizhudao.com/common.min.1.1.css'); ?>
<?= Html::jsFile('http://static.mingyizhudao.com/custom.min.1.0.js'); ?>
<?= Html::jsFile('http://static.mingyizhudao.com/md2/patientUpload.min.1.4.js'); ?>


<article id="updateMRFileAndroid_article" class="active android_article" data-scroll="true">
    <div class="pad10">
        <div class="text-center mt20">
            <div class="font-w800">
                请您上传患者的相关病历资料
            </div>
            <div class="font-s12 color-gray">
                (图片需清晰可见，最多9张)
            </div>
        </div>
        <div class="exampleSection">
            <div class="text-center font-s14">
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
        <div class="imglist mt10">
            <ul class="filelist"></ul>
        </div>
        <div class="clearfix"></div>
        <div class="container">
            <div class="text-left wrapper">
                <form id="booking-form" data-url-uploadfile="<?= $urlUploadFile; ?>" data-url-return="<?= $urlReturn; ?>" data-patientBookingId="<?= $patientBookingId; ?>" data-patientAjaxTask="<?= $patientAjaxTask; ?>">
                    <input id="patientId" type="hidden" name="Booking[patient_id]" value="<?= $patientId; ?>" />
                    <input id="reportType" type="hidden" name="Booking[report_type]" value="mr" />
                    <input type="hidden" id="domain" value="http://mr.file.mingyizhudao.com">
                    <input type="hidden" id="uptoken_url" value="<?= $urlQiniuAjaxToken; ?>">
                </form>
            </div>
            <div class="body mt10">
                <div class="col-md-12 mt10">
                    <table class="table table-striped table-hover text-left" style="display:none">
                        <tbody id="fsUploadProgress">
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <div id="container">
                        <a id="pickfiles" class="" href="#" >
                            <span>点击添加图片</span>
                        </a>
                    </div>
                </div>
                <div id="skipSection" class="mt10">
                    <?php
                    if ($type == 'create') {
                        echo '<a href="javascript:;" class="btn btn-full skipBtn" data-ajax="false">稍后补充</a>' .
                        '<div class="text-center color-gray font-s12">(提交订单后可在订单详情里补充)</div>';
                    }
                    ?>
                </div>
            </div>
            <div id="submitBtn" class="hide pt20">
                <button class="btn btn-full bg-green color-white">确认</button>
            </div>
        </div>
        <div id="deleteConfirm" class="confirm" style="top: 50%; left: 5%; right: 5%; border-radius: 3px; margin-top: -64.5px;">
            <div class="popup-title">提示</div>
            <div class="popup-content text-center">确定删除这张图片?</div>
            <div id="popup_btn_container">
                <a class="cancel">取消</a>
                <a class="delete">确定</a>
            </div>
        </div>
        <div id="jingle_toast" class="toast"><a href="#" class="font-s18">取消!</a></div>
        <div id="loading_popup" style="" class="loading">
            <i class="icon spinner"></i>
            <p>上传中...</p>
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
        $("#deleteConfirm .cancel").click(function () {
            $("#deleteConfirm").hide();
            $("#jingle_toast").show();
            setTimeout(function () {
                $("#jingle_toast").hide();
            }, 1000);
        });
        $("#deleteConfirm .delete").click(function () {
            $("#deleteConfirm").hide();
            id = $(this).attr("data-id");
            domId = "#" + id;
            domLi = $(domId);
            deleteImg(id, domLi);
            setTimeout(function () {
                $("#jingle_toast").hide();
            }, 2000);
        });
        //加载病人病历图片
        var urlPatientMRFiles = "<?= $urlPatientMRFiles; ?>";
//        $.ajax({
//            url: urlPatientMRFiles,
//            success: function(data) {
//                setImgHtml(data.results.files);
//            }
//        });
    });
    function setImgHtml(imgFiles) {
        var innerHtml = '';
        if (imgFiles && imgFiles.length > 0) {
            for (i = 0; i < imgFiles.length; i++) {
                var imgFile = imgFiles[i];
                innerHtml +=
                        '<li id="' +
                        imgFile.id + '"><p class="imgWrap"><img src="' +
                        imgFile.thumbnailUrl + '" data-src="' +
                        imgFile.absFileUrl + '"></p><div class="file-panel delete"><span class="">删除</span></div></li>';
            }
        } else {
            innerHtml += '';
        }
        $(".imglist .filelist").html(innerHtml);
        initDelete();
    }
    function initDelete() {
        $('.imglist .delete').click(function () {
            domLi = $(this).parents("li");
            id = domLi.attr("id");
            $("#deleteConfirm .delete").attr("data-id", id);
            $("#deleteConfirm").show();
        });
    }
    function deleteImg(id, domLi) {
        $(".ui-loader").show();
        var urlDeleteDoctorCert = '<?= $urlDeletePatientMRFile ?>' + id;
        $.ajax({
            url: urlDeleteDoctorCert,
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status == 'ok') {
                    domLi.remove();
                    $("#jingle_toast a").text('删除成功!');
                    $("#jingle_toast").show();
                }
            },
            complete: function () {
                $(".ui-loader").hide();
            }
        });
    }
</script>
