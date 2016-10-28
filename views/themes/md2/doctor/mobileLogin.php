<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/11
 * Time: 10:28
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

$this->title = '医生登录';
$this->params['urlRegister'] = Url::to('register', ['addBackBtn' => 1]);
$this->params['urlForgetPassWord'] = Url::to('forget-password', ['addBackBtn' => 1]);
$this->params['urlPage'] = \Yii::$app->request->get('page', 0);
$this->params['urlGetSmsVerifyCode'] = Url::toRoute('/auth/send-sms-verify-code');
$this->params['urlDoctorValidCaptcha'] = Url::to('valid-captcha');
$this->params['urlAjaxLogin'] = Url::to('ajax-login');
$this->params['authActionType'] = \app\models\auth\AuthSmsVerify::ACTION_USER_LOGIN;
$this->params['urlResImage'] = $this->theme->baseUrl. "/images/";
$this->params['show_footer'] = false;
?>

<?= Html::jsFile('http://static.mingyizhudao.com/jquery.formvalidate.min.1.0.js') ?>
<?= Html::jsFile('http://static.mingyizhudao.com/loginValidator.min.1.0.js') ?>

<header id="login_header" class="bg-green">
    <?php
    if ($loginType == 'sms') {
        $smsActive = 'active';
        $smsHide = '';
        $pawActive = '';
        $pawHide = 'hide';
    } else {
        $smsActive = '';
        $smsHide = 'hide';
        $pawActive = 'active';
        $pawHide = '';
    }
    ?>
    <ul class="control-group">
        <li data-page="smsLogin" class="pageSwitch <?= Html::encode($smsActive) ?>">
            快速登录
        </li>
        <li data-apge="pawLogin" class="pageSwitch <?= Html::encode($pawActive) ?>">
            密码登录
        </li>
    </ul>
</header>
<article id="login_article" class="active bg-gary" data-scroll="true">
    <div>
        <div id="smsLogin" class="mt30 ml10 mr10 <?= Html::encode($smsHide) ?>">
            <?php
            $form = ActiveForm::begin([
                'id' => 'smsLogin-form',
                'options' => [
                    'data-url-action' => $this->params['urlAjaxLogin'],
                    'data-url-return' => Url::to('view'),
                    'data-url-checkCode' => $this->params['urlDoctorValidCaptcha'],
                ],
                'enableClientValidation' => false,
                'enableAjaxValidation' => false
            ]);
            ?>
            <?= Html::hiddenInput("smsverify[actionUrl]", $this->params['urlGetSmsVerifyCode']) ?>
            <?= Html::hiddenInput("smsverify[actionType]", $this->params['authActionType']) ?>

            <div class="input">
                <div class="">
                    <div class="inputBorder mb10">
                        <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入您的手机号', 'class' => 'noPaddingInput'])->label(false); ?>
                    </div>
                    <?= $form->errorSummary($model, ['username']) ?>
                </div>
            </div>
            <div class="input mt30">
                <div id="captchaCode" class="grid inputBorder mb10">
                    <div class="col-1">
                        <input type="text" id="UserDoctorMobileLoginForm_captcha_code" class="noPaddingInput" name="UserDoctorMobileLoginForm[captcha_code]" placeholder="请输入图形验证码">
                    </div>
                    <div class="col-0 w2p mt5 mb5 br-gray">
                    </div>
                    <div class="col-0 w95p text-center">
                        <div class="input-group-addon">
                            <a href="javascript:void(0);"><img src="<?= \Yii::$app->request->baseUrl. '/mobileDoctor/doctor/get-captcha'; ?>" class="h40" onclick="this.src = '<?php echo \Yii::$app->request->baseUrl?> /mobileDoctor/doctor/get-captcha/';  + Math.random()"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="input mt30">
                <div class="grid inputBorder mb10">
                    <div class="col-1">
                        <?= $form->field($model, 'verify_code')->textInput(['placeholder' => '请输入验证码', 'class' => 'noPaddingInput'])->label(false) ?>
                    </div>
                    <div class="col-0 w2p mt5 mb5 br-gray">
                    </div>
                    <div class="col-0 w95p text-center">
                        <button id="btn-sendSmsCode" class="btn btn-sendSmsCode ui-corner-all ui-shadow">获取验证码</button>
                    </div>
                </div>
                <?= $form->errorSummary($model, ['verify_code']) ?>
            </div>
            <div class="mt40">
                <a id="btnSubmitSms" class="btn btn-yes btn-full">登录</a>
            </div>
            <?php ActiveForm::end() ?>
        </div>
        <div id="pawLogin" class="mt30 ml10 mr10 <?= Html::encode($pawHide)?>">
            <?php
            $form = ActiveForm::begin([
                'id' => 'pawLogin-form',
                'options' => [
                    'data-url-action' => $this->params['urlAjaxLogin'],
                    'data-url-return' => Url::toRoute('view', true)
                ],
                'enableClientValidation' => false,
                'enableAjaxValidation' => false,
            ]);
            ?>
            <div class="input">
                <div class="grid inputBorder mb10">
                    <div class="col-0 phoneIcon">
                    </div>
                    <div class="col-0 w2p br-gray mt10 mb10">
                    </div>
                    <div class="col-1">
                        <?= $form->field($pwdModel, 'username')->textInput(['placeholder' => '请输入您的手机号', 'class' => 'noPaddingInput'])->label(false) ?>
                    </div>
                </div>
            </div>
            <div class="input mt30">
                <div class="grid inputBorder mb10">
                    <div class="col-0 lockIcon">
                    </div>
                    <div class="col-0 w2p br-gray mt10 mb10">
                    </div>
                    <div class="col-1">
                        <?= $form->field($pwdModel, 'password')->passwordInput(['placeholder' => '请输入密码', 'class' => 'noPaddingInput'])->label(false) ?>
                    </div>
                </div>
            </div>
            <div class="mt40">
                <a id="btnSubmitPaw" class="btn btn-yes btn-full">登录</a>
            </div>
            <div class="">
                <div class="mt20 text-right">
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
        <div class="grid">
            <div class="w50 text-right pr20">
                <a href="<?= $this->params['urlRegister'] ?>">注册账号</a>
            </div>
            <div class="w50 pl20">
                <a href="<?= $this->params['urlForgetPassWord'] ?>">忘记密码</a>
            </div>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        validCode();
        $('.pageSwitch').click(function () {
            var page = $(this).attr('data-page');
            if (page == 'smsLogin') {
                $('#smsLogin').removeClass('hide');
                $('#pawLogin').addClass('hide');
            } else {
                $('#smsLogin').addClass('hide');
                $('#pawLogin').removeClass('hide');
            }
        });
        $("#btn-sendSmsCode").click(function (e) {
            e.preventDefault();
            checkForm($(this));
        });
    });

    function is_weiXin() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            var registerFlag = '<?php echo $registerFlag; ?>';
            if (registerFlag != 1) {
                J.showMask('获取用户信息中...');
                window.location.href = "<?php echo \Yii::$app->request->hostInfo . "/weixinpub/oauth/autoLogin?returnUrl=" . $returnUrl; ?>";
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function validCode() {
        $("#vailcode").attr("src", "<?php echo Url::to('user/getCaptcha'); ?>/" + Math.random());
    }

    function checkForm(domBtn) {
        var domForm = $("#smsLogin-form");
        var domMobile = domForm.find("#UserDoctorMobileLoginForm_username");
        var captchaCode = $('#UserDoctorMobileLoginForm_captcha_code').val();
        var mobile = domMobile.val();
        if (mobile.length === 0) {
            $("#UserDoctorMobileLoginForm_username-error").remove();
            $("#UserDoctorMobileLoginForm_username").parents('div.input').append("<div id='UserDoctorMobileLoginForm_username-error' class='error'>请输入手机号码</div>");
            //domMobile.parent().addClass("error");
        } else if (!validatorMobile(mobile)) {
            $("#UserDoctorMobileLoginForm_username-error").remove();
            $("#UserDoctorMobileLoginForm_username").parents('div.input').append("<div id='UserDoctorMobileLoginForm_username-error' class='error'>请输入正确的中国手机号码!</div>");
        } else if (captchaCode == '') {
            $('#UserDoctorMobileLoginForm_captcha_code-error').remove();
            $('#captchaCode').after('<div id="UserDoctorMobileLoginForm_captcha_code-error" class="error">请输入图形验证码</div>');
        } else {
            $('#UserDoctorMobileLoginForm_captcha_code-error').remove();
            var formdata = domForm.serializeArray();
            //check图形验证码
            $.ajax({
                type: 'post',
                url: '<?php echo $this->params['urlDoctorValidCaptcha']; ?>?co_code=' + captchaCode,
                data: formdata,
                success: function (data) {
                    //console.log(data);
                    if (data.status == 'ok') {
                        sendSmsVerifyCode(domBtn, domForm, mobile, captchaCode);
                    } else {
                        $('#captchaCode').after('<div id="UserDoctorMobileLoginForm_captcha_code-error" class="error">' + data.error + '</div>');
                    }
                }
            });
        }
    }
    function sendSmsVerifyCode(domBtn, domForm, mobile, captchaCode) {
        $(".error").html(""); //删除错误信息
        var actionUrl = domForm.find("input[name='smsverify[actionUrl]']").val();
        var actionType = domForm.find("input[name='smsverify[actionType]']").val();
        var formData = new FormData();
        formData.append("AuthSmsVerify[mobile]", mobile);
        formData.append("AuthSmsVerify[actionType]", actionType);
        $.ajax({
            type: 'post',
            url: actionUrl + '?captcha_code=' + captchaCode,
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            'success': function (data) {
                if (data.status === true || data.status === 'ok') {
                    //domForm[0].reset();
                    buttonTimerStart(domBtn, 60000);
                }
                else {
                    console.log(data);
                    if (data.errors.captcha_code != undefined) {
                        $('#captchaCode').after('<div id="UserDoctorMobileLoginForm_captcha_code-error" class="error">' + data.errors.captcha_code + '</div>');
                    }
                }
            },
            'error': function (data) {
                console.log(data);
            },
            'complete': function () {
            }
        });
    }
    function buttonTimerStart(domBtn, timer) {
        timer = timer / 1000 //convert to second.
        var interval = 1000;
        var timerTitle = '秒后重发';
        domBtn.attr("disabled", true);
        domBtn.html(timer + timerTitle);
        timerId = setInterval(function () {
            timer--;
            if (timer > 0) {
                domBtn.html(timer + timerTitle);
            } else {
                clearInterval(timerId);
                timerId = null;
                domBtn.html("重新发送");
                domBtn.attr("disabled", false).removeAttr("disabled");
                ;
            }
        }, interval);
    }
    function validatorMobile(mobile) {
        var mobileReg = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
        return mobileReg.test(mobile);
    }
</script>

