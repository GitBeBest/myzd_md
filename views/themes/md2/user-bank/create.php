<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\doctor\DoctorBankCardForm;
/**
 * @var $this View
 * @var $model DoctorBankCardForm
 */
$this->title = '添加银行卡信息';
$urlResImage = $this->theme->baseUrl . "/images/";
$urlLoadCity = Url::to(['/region/load-cities', 'state' => '']);
$urlAjaxCreate = Url::to('ajax-create');
$urlCardList = Url::to(['card-list','addBackBtn' => 1]);
$this->params['show_footer'] = false;
?>

<?php
$this->registerJsFile('http://static.mingyizhudao.com/jquery.formvalidate.min.1.0.js', ['position' => View::POS_END]);
$this->registerJsFile('http://static.mingyizhudao.com/card.min.1.0.js', ['position' => View::POS_END]);
?>

<article id="userbankCreate_article" class="active userbank_article" data-scroll="true">
    <div class="pt10">
        <?php
            $form = ActiveForm::begin([
                'id' => 'card-form',
                'enableClientValidation' => false,
                'options' => [
                    'data-return-url' => $urlCardList,
                    'data-action-url' => $urlAjaxCreate,
                    'validateOnSubmit' => true,
                    'validateOnType' => true,
                    'validateOnDelay' => 500,
                    'errorCssClass' => 'error',
                ],
                'enableAjaxValidation' => false,
            ]);
        ?>
        <?= $form->field($model, 'is_default')->hiddenInput(['name' => 'card[is_default]', 'value' => 0])->label(false);?>
        <div class="pl10 pr10 bg-white">
            <div class="inputRow">
                <div class="grid bb-gray">
                    <div class="col-0 w90p vertical-center">
                        持卡人
                    </div>
                    <div class="col-1">
                        <?= $form->field($model, 'name')->textInput(['name' => 'card[name]', 'placeholder' => '请输入姓名', 'class' => 'noPaddingInput'])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="inputRow">
                <div class="grid bb-gray">
                    <div class="col-0 w90p vertical-center">
                        卡号
                    </div>
                    <div class="col-1">
                        <?= $form->field($model, 'card_no')->input('number', ['name' => 'card[card_no]', 'placeholder' => '请输入银行卡号', 'class' => 'noPaddingInput'])->label(false);?>
                    </div>
                </div>
            </div>
            <div class="inputRow">
                <div class="grid bb-gray">
                    <div class="col-0 w90p vertical-center">
                        省份
                    </div>
                    <div class="col-1">
                        <?php
                        $model->state_id = null;
                        ?>
                        <?= $form->field($model, 'state_id')->dropDownList($model->loadOptionsState(), ['name' => 'card[state_id]', 'prompt' => '选择省份', 'class' => '','id' => 'card_state_id'])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="inputRow">
                <div class="grid bb-gray">
                    <div class="col-0 w90p vertical-center">
                        城市
                    </div>
                    <div class="col-1">
                        <?= $form->field($model, 'city_id')->dropDownList($model->loadOptionsCity(), ['name' => 'card[city_id]', 'prompt' => '选择城市', 'class' => '','id' => 'card_city_id'])->label(false);?>
                    </div>
                </div>
            </div>
            <div class="inputRow">
                <div class="grid bb-gray">
                    <div class="col-0 w90p vertical-center">
                        开户银行
                    </div>
                    <div class="col-1">
                        <?= $form->field($model, 'bank')->textInput(['name' => 'card[bank]', 'placeholder' => '选择开户银行', 'class' => 'noPaddingInput'])->label(false);?>
                    </div>
                </div>
            </div>
            <div class="inputRow">
                <div class="grid bb-gray">
                    <div class="col-0 w90p vertical-center">
                        支行名称
                    </div>
                    <div class="col-1">
                        <?= $form->field($model, 'subbranch')->textInput(['name' => 'card[subbranch]', 'placeholder' => '选择银行支行', 'class' => 'noPaddingInput'])->label(false);?>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid pt10 pl10 pr10">
            <div class="col-1"></div>
            <div id="setDefault" class="col-0" data-select="0">
                <img src="http://static.mingyizhudao.com/146664844004130" class="w17p mr6">设为默认
            </div>
        </div>
        <?php
        $form->end();
        ?>
        <div class="pad10">
            <button id="submitBtn" class="btn btn-full btn-yellow3">保存</button>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        $('#setDefault').click(function () {
            if ($(this).attr('data-select') == 0) {
                $(this).attr('data-select', 1);
                $('input[name="card[is_default]"]').val(1);
                $(this).html('<img src="http://static.mingyizhudao.com/146665213709967" class="w17p mr6">设为默认');
            } else {
                $(this).attr('data-select', 0);
                $('input[name="card[is_default]"]').val(0);
                $(this).html('<img src="http://static.mingyizhudao.com/146664844004130" class="w17p mr6">设为默认');
            }
        });
        $("select#card_state_id").change(function () {
            $("select#card_city_id").attr("disabled", true);
            var stateId = $(this).val();
            var actionUrl = "<?php echo $urlLoadCity; ?>";// + stateId + "&prompt=选择城市";
            $.ajax({
                type: 'get',
                url: actionUrl,
                data: {'state': this.value, 'prompt': '选择城市'},
                cache: false,
                // dataType: "html",
                'success': function (data) {
                    $("select#card_city_id").html(data);
                    // jquery mobile fix.
                    captionText = $("select#card_city_id>option:first-child").text();
                    $("#card_city_id-button>span:first-child").text(captionText);
                },
                'error': function (data) {
                },
                complete: function () {
                    $("select#card_city_id").attr("disabled", false);
                    $("select#card_city_id").removeAttr("disabled");
                }
            });
            return false;
        });
    });
</script>