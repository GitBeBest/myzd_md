<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 * @var
 */
$this->title = '疾病信息';
$diseaseSearch = Url::to('disease-search');
$diseaseName = \Yii::$app->request->get('diseaseName', '');
$diseaseId = \Yii::$app->request->get('diseaseId', '');
$savePatientDisease = Url::to('save-patient-disease');
$id = \Yii::$app->request->get('id', '');
/*
 * source
 * 0:正常途径完善疾病信息
 * 1:从签约专家路径完善疾病信息，点击下一步，回到签约专家中的选择患者页面
 */
$source = \Yii::$app->request->get('source', '0');
$sourceReturn = \Yii::$app->request->get('returnUrl', '');
$urlReturn = Url::to(['patient/upload-mr-file', 'type' => 'create']);
$addDisease = Url::to('add-disease');
$this->params['show_footer'] = false;
?>
<?//= Html::jsFile('http://static.mingyizhudao.com/md2/addDisease.min.1.1.js') ?>

<article id="addDisease_article" class="active" data-scroll="true">
    <div class="pad10">
        <form id="patient-form" data-url-action="<?= $savePatientDisease; ?>" data-url-return="<?= $urlReturn; ?>" data-source-return="<?= $sourceReturn; ?>" source="<?= $source; ?>">
            <input type="hidden" name="patient[id]" value="<?= $id; ?>">
            <input type="hidden" name="patient[disease_name]" value="<?= $diseaseName; ?>">
            <div class="pt20">
                请选择患者疾病名称：
            </div>
            <div>
                <div class="selectDisease grid">
                    <?php
                    if ($diseaseName != '') {
                        echo '<div class="col-1 color-black">' . $diseaseName . '</div>';
                    } else {
                        echo '<div class="col-1">点击选择患者的疾病</div>';
                    }
                    ?>
                    <div class="col-0 icon-clear <?= $diseaseName != '' ? '' : 'hide' ?>"></div>
                </div>
            </div>
            <div class="pt30">
                请简要描述患者的疾病情况：
            </div>
            <div>
                <textarea name="patient[disease_detail]" placeholder="请至少输入10个字"></textarea>
            </div>
        </form>
        <div>
            <button id="btnSubmit" class="btn btn-yes btn-block" disabled="disabled">下一步</button>
        </div>
    </div>
</article>
<script>
    $(document).ready(function() {
        //按钮操作
        document.addEventListener('input', function(e) {
            var bool = true;
            $('input').each(function() {
                if ($(this).val() == '') {
                    bool = false;
                    return false;
                }
            });
            $('textarea').each(function() {
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
        });
        $('.selectDisease').find('.col-1').click(function() {
            location.href = '<?= $diseaseSearch; ?>?source=' + '<?= $source; ?>&id=' + '<?= $id; ?>&returnUrl=' + '<?= $sourceReturn; ?>';
        });
        //清空疾病
        $('.icon-clear').click(function() {
            var newUrl = '<?= $addDisease; ?>?id=' + '<?= $id; ?>&returnUrl=' + '<?= $sourceReturn; ?>';
            history.pushState({}, '', newUrl);
            location.href = '<?= $diseaseSearch; ?>?id=' + '<?= $id; ?>&returnUrl=' + '<?= $sourceReturn; ?>';
        });

        var domForm = $("#patient-form"), requestUrl = domForm.attr("data-url-action"), returnUrl = domForm.attr("data-url-return"), sourceReturn = domForm.attr("data-source-return"), source = domForm.attr("source"), btnSubmit = $("#btnSubmit");
        btnSubmit.click(function () {
            if ($('input[name="patient[disease_name]"]').val() == "") {
                J.showToast("请选择疾病名称", "", "1500");
                return false
            }
            if ($('textarea[name="patient[disease_detail]"]').val() == "") {
                J.showToast("请输入疾病情况", "", "1500");
                return false
            } else {
                if ($('textarea[name="patient[disease_detail]"]').val().length < 10) {
                    J.showToast("疾病情况至少10个字", "", "1500");
                    return false
                }
            }
            formAjaxSubmit()
        });
        function formAjaxSubmit() {
            J.showMask();
            var id = $('input[name="patient[id]"]').val();
            var disease_name = $('input[name="patient[disease_name]"]').val();
            var disease_detail = $('textarea[name="patient[disease_detail]"]').val();
            $.ajax({
                type: "post",
                url: requestUrl,
                data: {"patient[id]": id, "patient[disease_name]": disease_name, "patient[disease_detail]": disease_detail},
                success: function (data) {
                    if (data.status == "ok") {
                        if (source == 1) {
                            location.href = sourceReturn
                        } else {
                            location.href = returnUrl + "&id=" + id + "&returnUrl=" + sourceReturn
                        }
                    } else {
                        J.hideMask();
                        J.showToast("网络错误，请稍后再试", "", "1500")
                    }
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    J.hideMask();
                    console.log(XmlHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown)
                }
            })
        }
    });
</script>