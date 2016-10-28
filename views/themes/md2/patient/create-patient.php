<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\patient\PatientInfoForm;
use app\assets\AppAsset;
/**
 * @var $this View
 * @var $teamDoctor
 * @var $model PatientInfoForm
 */
AppAsset::register($this);
$this->title = '创建患者';
$urlSavePatient = Url::to('save-patient');
$urlLoadCity = Url::to(['/region/load-cities', 'state' => '']);
$urlSubmit = Url::to('ajax-create');
$urlReturn = Url::toRoute('doctor/add-disease');
$currentUrl = \Yii::$app->request->getUrl();
$urlDoctorTerms = Url::toRoute('doctor/doctor-terms');
$urlDoctorTerms.='?returnUrl=' . $currentUrl;
$urlDoctorView = Url::to('doctor/view');
$checkTeamDoctor = $teamDoctor;
$this->params['show_footer'] = false;
?>

<?= Html::jsFile('http://static.mingyizhudao.com/jquery.formvalidate.min.1.0.js');?>
<?= Html::jsFile('http://static.mingyizhudao.com/md2/patient.min.1.2.js'); ?>

<article class="active" data-scroll="true">
    <div class="ml10 mr10 mb20">
        <div calss="form-wrapper">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'patient-form',
                    'enableClientValidation' => false,
                    'options' => [
                        'data-url-action' => $urlSubmit,
                        'data-url-return' => $urlReturn,
                        'data-type' => 'create',
                        'validateOnSubmit' => true,
                        'validateOnType' => true,
                        'validateOnDelay' => 500,
                        'errorCssClass' => 'error',
                    ],
                    'enableAjaxValidation' => false,
                ]);
            ?>
            <?= $form->field($model, 'country_id')->hiddenInput(['name' => 'patient[country_id]'])->label(false); ?>
            <ul class="list">
                <li>
                    <label for="PatientInfoForm_name">患者姓名</label>
                    <?= $form->field($model, 'name')->textInput(['name' => 'patient[name]', 'placeholder' => '请填写真实姓名', 'maxlength' => 45])->label(false); ?>
                    <?= $form->errorSummary($model); ?>
                </li>
                <li>
                    <label for="PatientInfoForm_mobile">联系方式</label>
                    <?= $form->field($model, 'mobile')->textInput(['name' => 'patient[mobile]', 'placeholder' => '请填写手机号码', 'maxlength' => 50])->label(false); ?>
                    <?= $form->errorSummary($model); ?>
                    <div></div>
                </li>
                <li>
                    <label for="PatientInfoForm_birth_year">出生年月</label>
                    <div class="ui-grid-a">
                        <div class="ui-block-a">
                            <select name="patient[birth_year]" id="patient_birth_year">
                                <option value>选择出生年份</option>
                            </select>
                        </div>
                        <div class="ui-block-b pl10">
                            <select name="patient[birth_month]" id="patient_birth_month">
                                <option value>选择出生月份</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?= $form->errorSummary($model, ['birth_year']); ?>
                    <?= $form->errorSummary($model, ['birth_month']); ?>
                </li>
                <li>
                    <?= Html::activeLabel($model, 'gender'); ?>
                    <div class="grid">
                        <div class="col-0 w50">
                            <input type='radio' name='patient[gender]' id='patient_gender_male' value='1'/><label for='patient_gender_male'>&nbsp;男</label>
                            <div></div>
                        </div>
                        <div class="col-0 w50">
                            <input type='radio' name='patient[gender]' id='patient_gender_female' value='2'/><label for='patient_gender_female'>&nbsp;女</label>
                            <div></div>
                        </div>
                    </div>
                    <?= $form->errorSummary($model, ['gender']); ?>
                </li>
                <li>
                    <?= Html::activeLabel($model, 'state_id'); ?>
                    <?php
                        $model->state_id = null;
                    ?>
                    <?= $form->field($model, 'state_id')->dropDownList($model->loadOptionsState(), ['name' => 'patient[state_id]', 'prompt' => '选择省份', 'class' => '','id' => 'patient_state_id'])->label(false);?>
                    <?= $form->errorSummary($model, ['state_id']); ?>
                </li>
                <li>
                    <?= Html::activeLabel($model, 'city_id'); ?>
                    <?= $form->field($model, 'city_id')->dropDownList($model->loadOptionsCity(), ['name' => 'patient[city_id]', 'prompt' => '选择城市', 'class' => '', 'id' => 'patient_city_id'])->label(false);?>
                    <?= $form->errorSummary($model, ['city_id']); ?>
                </li>
            </ul>
            <?php
               $form->end();
            ?>
            <div class="pad20">
                <button id="btnSubmit" class="btn btn-yes btn-block">下一步</button>
            </div>
        </div>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function() {
        //签约专家跳转过来的returnUrl
        $returnUrl = '<?= $returnUrl; ?>';

        if ('<?= $checkTeamDoctor; ?>' == 1) {
            J.customConfirm('您已实名认证',
                    '<div class="mt10 mb10">尚未签署《医生顾问协议》</div>',
                    '<a data="cancel" class="w50">暂不</a>',
                    '<a data="ok" class="color-green w50">签署协议</a>',
                    function() {
                        location.href = "<?= $urlDoctorTerms; ?>";
                    },
                    function() {
                        location.href = "<?= $urlDoctorView; ?>";
                    });
        }

        //初始化年月下拉菜单
        initDateSelect();
        $("select").change(function() {
            $(this).parents(".ui-select").find("span.error").removeClass(".error");
        });
        $("select#patient_state_id").change(function() {
            $("select#patient_city_id").attr("disabled", true);
            var stateId = $(this).val();
            var actionUrl = "<?= $urlLoadCity; ?>";// + stateId + "&prompt=选择城市";
            $.ajax({
                type: 'get',
                url: actionUrl,
                data: {'state': this.value, 'prompt': '选择城市'},
                cache: false,
                // dataType: "html",
                'success': function(data) {
                    // console.log(data);市的名称

                    $("select#patient_city_id").html(data);
                    // jquery mobile fix.
                    captionText = $("select#patient_city_id>option:first-child").text();
                    $("#patient_city_id-button>span:first-child").text(captionText);
                },
                'error': function(data) {
                },
                complete: function() {
                    $("select#patient_city_id").attr("disabled", false);
                    $("select#patient_city_id").removeAttr("disabled");
                }
            });
            return false;
        });
    });
    function initDateSelect() {
        var yearSelect = $("#patient_birth_year"),
                monthSelect = $("#patient_birth_month"),
                nowYear = new Date().getFullYear(),
                yearInnerHtml = '',
                monthInnerHtml = '';
        for (var i = 0; i <= 150; i++) {
            yearInnerHtml += '<option value="' + (nowYear - i) + '">' + (nowYear - i) + '年</option>';
        }
        yearSelect.append(yearInnerHtml);
        for (var i = 1; i < 13; i++) {
            monthInnerHtml += '<option value="' + i + '">' + i + '月</option>';
        }
        monthSelect.append(monthInnerHtml);
    }
</script>