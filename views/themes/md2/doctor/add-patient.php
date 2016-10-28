<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $doctorInfo
 * @var $patientInfo
 * @var $model
 */
$this->title = '添加患者';
$urlResImage = $this->theme->baseUrl . "/images/";
$urlBookingDoctor = Url::to(['booking/create',  'did' => '']);
$url = Url::to(['home/page', 'view' => 'bookingDoctor', 'addBackBtn' => '1']);
$urlPatientCreate = Url::to(['patient/create', 'addBackBtn' => 1]);
$doctor = $doctorInfo->results->doctor;
$id = \Yii::$app->request->get('id', '');
$returnUrl = Url::to(['doctor/add-patient', 'id' => $id, 'addBackBtn' => 1]);
$urlChoosePatientList = Url::to(['patient/choose-list', 'id' => $doctor->id, 'addBackBtn' => 1]);
$urlSubmit = Url::to('patientBooking/ajax-create');
$urlReturn = Url::to('order/view');
$this->params['show_footer'] = false;
?>

<?= Html::jsFile('http://static.mingyizhudao.com/doctor/jquery.formvalidate.min.1.1.js', ['']) ?>
<?= Html::jsFile('http://static.mingyizhudao.com/md2/addPatient.min.1.0.js') ?>

<footer id="addPatient_footer" class="bg-white">
    <button id="btnSubmit" class="btn btn-block bg-g1">提交</button>
</footer>
<article id="addPatient_article" class="active" data-scroll="true">
    <div>
        <?php $form = ActiveForm::begin([
            'id' => 'patient-form',
            'action' => $urlSubmit,
            'options' => [
                'data-url-return' => $urlReturn,
                'validateOnSubmit' => true,
                'validateOnType' => true,
                'validateOnDelay' => 500,
                'errorCssClass' => 'error',
            ],
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
        ]);
        ?>
        <?= Html::hiddenInput('booking[patient_id]', is_null($patientInfo) ? '' : $patientInfo->results->patientInfo->id, ['id' => 'booking_patient_id']) ?>
        <?= Html::hiddenInput('booking[expected_doctor]', $doctor->name, ['id' => 'booking_expected_doctor']) ?>
        <?= Html::hiddenInput('booking[expected_hospital]', $doctor->hospitalName, ['id' => 'booking_expected_hospital']) ?>
        <?= Html::hiddenInput('booking[expected_dept]', $doctor->hpDeptName, ['id' => 'booking_expected_dept']) ?>
        <?= Html::hiddenInput('booking[travel_type]', '', ['id' => 'booking_travel_type']) ?>

        <div class="grid pt20 pb20 doctorInfo">
            <div class="col-0">
                <div class="imgDiv ml20">
                    <img src="<?= $doctor->imageUrl; ?>" class="imgDoc">
                </div>
            </div>
            <div class="col-1 ml20  color-white">
                <div>
                    <?= '<span class="font-s16">' . $doctor->name . '</span>' . '<span class="ml10">' . $doctor->aTitle . '</span>'; ?>
                </div>
                <div>
                    <?php
                    if ($doctor->hpDeptName == '') {
                        echo $doctor->mTitle;
                    } else {
                        echo $doctor->hpDeptName . '<span class="ml10">' . $doctor->mTitle . '</span>';
                    }
                    ?>
                </div>
                <div class="grid">
                    <div class="col-0">
                        <?= $doctor->hospitalName; ?>
                    </div> 
                    <div class="col-1"></div>
                </div>
            </div>
        </div>
        <div class="bg-white pt10  ">
            <div class="bbh pl10 pb10 ">
                <span class="bgimg1 pl25">选择就诊意向:</span> 
            </div>
            <div class="grid pad20 color-gray">
                <div class="col-1 intention w50 mr10" data-travel="1">邀请专家过来</div>
                <div class="col-1 intention w50 ml10" data-travel="2">希望转诊治疗</div>
            </div>
        </div>
        <div class="mt10 bg-white">
            <div class="pad10 bbh">
                <span class="bgimg2 pl25">请选择您的患者:</span>
            </div>
            <?php
            if (is_null($patientInfo)) {
                echo '<div class="text-center pad20 color-gray"><span class="text-center pr50 pl50 pt10 pb10 b-gray1 br5" id="choosep">+选择患者</span></div>';
            } else {
                ?>
                <div id="choosep" class="pad10 grid color-gray">
                    <div class="col-1">
                        <?= $patientInfo->results->patientInfo->name . '-' . $patientInfo->results->patientInfo->diseaseName; ?>
                    </div>
                    <div class="col-0 icon-clear"></div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="mt10 bg-white">
            <div class="pad10 bbh">
                <span class="bgimg3 pl25">诊疗意见:</span>
            </div> 
            <div class="pad10">
                <textarea name="booking[detail]" id="booking_detail"  placeholder="如您有其他诊疗意见，请填写&#10;如没有请填写“无”" maxlength="1000" cols="10" rows="2"></textarea>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</article>
<script>
    $(document).ready(function() {
        //选择就诊意向
        $('.intention').click(function() {
            var travelType = $(this).attr('data-travel');
            $('input[name = "booking[travel_type]"]').attr('value', travelType);
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
        });
        var session_travelType = sessionStorage.getItem('travelType');
        var session_detail = sessionStorage.getItem('detail');
        if (session_travelType != null) {
            $('.intention').each(function() {
                if ($(this).attr('data-travel') == session_travelType) {
                    $(this).trigger('click');
                }
            });
        }
        $('textarea[name="booking[detail]"]').val(session_detail);
        //选择患者
        $('#choosep').click(function() {
            var travelType = $('input[name="booking[travel_type]"]').val();
            var detail = $('textarea[name="booking[detail]"]').val();
            sessionStorage.setItem('travelType', travelType);
            sessionStorage.setItem('detail', detail);
            location.href = "<?= $urlChoosePatientList; ?>";
        });
    });
</script>