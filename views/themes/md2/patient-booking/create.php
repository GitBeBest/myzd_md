<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\patient\PatientBookingForm;

/*
 * @var $this View
 * @var $model PatientBookingForm
 * @var $userDoctorProfile
 * @var $doctorCerts
 */
$this->title = '就诊信息';
$urlSubmit = Url::to('ajax-create');
$urlProfile = Url::toRoute(['doctor/profile', 'addBackBtn' => 1]);
$urlReturn = Url::toRoute('order/order-view');
$urlRealName = Url::toRoute('doctor/profile');
$urlDoctorUploadCert = Url::toRoute('doctor/upload-cert');
$pid = \Yii::$app->request->get('pid', '');
$urlViewContractDoctors = Url::to(['doctor/view-contract-doctors', 'source' => 1, 'pid' => $pid]);
$expectHospital = \Yii::$app->request->get('expectHospital', '');
$expectDept = \Yii::$app->request->get('expectDept', '');
$expectDoctor = \Yii::$app->request->get('expectDoctor', '');
$urlResImage = $this->theme->baseUrl . "/images/";
$real = $userDoctorProfile;
$userDoctorCerts = $doctorCerts;
$this->params['show_footer'] = false;
?>

<?= Html::jsFile('http://static.mingyizhudao.com/jquery.formvalidate.min.1.0.js'); ?>
<?= Html::jsFile('http://static.mingyizhudao.com/md2/patientBooking.min.1.4.js'); ?>

<article id="patientBookingCreate_article" class="active" data-scroll="true">
    <div class="pad10">
        <div class="form-wrapper">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'booking-form',
                    'action' => $urlSubmit,
                    'enableClientValidation' => false,
                    'options' => array(
                        'data-url-return' => $urlReturn,
                        'validateOnSubmit' => true,
                        'validateOnType' => true,
                        'validateOnDelay' => 500,
                        'errorCssClass' => 'error',
                    ),
                    'enableAjaxValidation' => false,
                ]);
            ?>
            <?= $form->field($model, 'patient_id')->hiddenInput(['name' => 'booking[patient_id]'])->label(false);?>
            <?= $form->field($model, 'user_agent')->hiddenInput(['name' => 'booking[user_agent]'])->label(false);?>
            <?= $form->field($model, 'expected_doctor')->hiddenInput(['name' => 'booking[expected_doctor]', 'value' => $expectDoctor])->label(false);?>
            <?= $form->field($model, 'expected_dept')->hiddenInput(['name' => 'booking[expected_dept]', 'value' => $expectDept])->label(false);?>
            <?= $form->field($model, 'expected_hospital')->hiddenInput(['name' => 'booking[expected_hospital]', 'value' => $expectHospital])->label(false);?>

            <div class="pl10 pr10 pb10 bg-white br5">
                <div id="travel_type" class="triangleGreen">
                    <div class="font-s16 pt10 pb5 bb-gray3 color-green">
                        请您选择就诊方式
                    </div>
                    <div class="grid pt20 pb20">
                        <?php
                        $travelType = $model->travel_type;
                        echo '' . $travelType;
                        $optionsTravelType = $model->loadOptionsTravelType();
                        $i = 1;
                        foreach ($optionsTravelType as $key => $caption) {
                            if ($travelType == $key) {
                                echo '<div data-travel="' . $key . '" class="col-1 w50 intention">' . $caption . '</div>';
                            } else {
                                if ($i == 1) {
                                    echo '<div data-travel="' . $key . '" class="col-1 w50 intention mr10">' . $caption . '</div>';
                                } else {
                                    echo '<div data-travel="' . $key . '" class="col-1 w50 intention">' . $caption . '</div>';
                                }
                            }
                            $i++;
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?= $form->field($model, 'travel_type')->hiddenInput(['name' => 'booking[travel_type]'])->label(false); ?>
            <div class="mt10 pl10 pr10 pb10 bg-white br5">
                <div id="expectedInfo">
                    <div class="font-s16 pt10 pb5 bb-gray3 color-green">
                        请填写您想要预约的主刀医生
                    </div>
                    <div class="grid">
                        <div class="col-0 pt8">所在医院：</div>
                        <div class="col-1">
                            <div class="selectExpect">
                                <?php
                                if ($expectHospital == '') {
                                    echo '请选择医生所在医院';
                                } else {
                                    echo '<span class="color-black">' . $expectHospital . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-0 pt8">所在科室：</div>
                        <div class="col-1">
                            <div class="selectExpect">
                                <?php
                                if ($expectDept == '') {
                                    echo '请选择医生所在科室';
                                } else {
                                    echo '<span class="color-black">' . $expectDept . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-0 pt8">医生姓名：</div>
                        <div class="col-1">
                            <div class="selectExpect">
                                <?php
                                if ($expectDoctor == '') {
                                    echo '请输入医生姓名';
                                } else {
                                    echo '<span class="color-black">' . $expectDoctor . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt10 pl10 pr10 pb10 bg-white br5">
                <div class="font-s16 grid pt10 pb5 bb-gray3 color-green">
                    请填写诊疗目的
                </div>
                <div>
                    <?= $form->field($model, 'detail')->textarea([
                        'name' => 'booking[detail]',
                        'maxlength' => 1000,
                        'rows' => '2',
                        'placeholder' => '如果有其他说明，请在此补充',
                        'class' => 'noPadding'
                    ])->label(false);?>
                </div>
                <?= $form->errorSummary($model); ?>
            </div>
            <?php $form->end(); ?>
            <div class="pt20 pb20">
                <button id="btnSubmit" class="btn btn-yes btn-block" disabled="disabled">提交</button>
            </div>
        </div>
    </div>
</article>
<script>
    Zepto(function ($) {
        document.addEventListener('input', function (e) {
            checkInput();
        });
        $('.selectExpect').click(function () {
            var detail = $('textarea[name="booking[detail]"]').val();
            if (detail != '') {
                sessionStorage.setItem('detail', detail);
            }
            location.href = '<?= $urlViewContractDoctors; ?>';
        });
        document.addEventListener('input', function (e) {
            e.preventDefault();
            $('#expectedError.error').remove();
        });
        //是否实名认证
        $realName = '<?= $real; ?>';
        $urlRealName = '<?php echo $urlRealName; ?>';
        $userDoctorCerts = '<?php echo $userDoctorCerts; ?>'
        $userDoctorUploadCert = '<?php echo $urlDoctorUploadCert; ?>';
        $('.intention').click(function (e) {
            e.preventDefault();
            $('.noTravelType').remove();
            var travelType = $(this).attr('data-travel');
            sessionStorage.setItem('intention', travelType);
            $('input[name = "booking[travel_type]"]').attr('value', travelType);
            $('.intention').each(function () {
                $(this).removeClass('active');
            });
            $(this).addClass('active');
            checkInput();
        });
        //初始化就诊方式
        var intention = sessionStorage.getItem('intention');
        if (intention != null) {
            $('.intention').each(function () {
                if ($(this).attr('data-travel') == intention) {
                    $(this).trigger('click');
                }
            });
        }
        //初始化诊疗目的
        var detail = sessionStorage.getItem('detail');
        if (detail != null) {
            $('textarea[name="booking[detail]"]').val(detail);
            checkInput();
        }
        function checkInput() {
            var bool = true;
            $('input').each(function () {
                if ($(this).val() == '') {
                    bool = false;
                    return false;
                }
            });
            $('textarea').each(function () {
                if ($(this).val() == '') {
                    bool = false;
                    return false;
                }
            });
            if (bool) {
                $('#btnSubmit').removeAttr('disabled');
            } else {
                $('#btnSubmit').attr('disabled', 'disabled');
            }
        }
    });
</script>