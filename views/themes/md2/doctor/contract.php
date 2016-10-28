<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $isContracted
 */
$this->title = '签约协议';
$urlResImage = $this->theme->baseUrl . "/images/";
$urlDoctorView = Url::to('view');
$urlDoctorDrView = Url::to('questionnaire');
$ajaxDoctorContract = Url::to('ajax-doctor-contract');
$this->params['show_footer'] = false;
$current_path = $this->theme->getBasePath();
?>
<header class="bg-green">
    <nav class="left">
        <a href="javascript:;" data-target="back">
            <div class="pl5">
                <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
            </div>
        </a>
    </nav>
    <h1 class="title">签约协议</h1>
</header>
<article id="terms_article" class="active" data-scroll="true">
    <div id="terms" class="terms">
        <div>
            <div>
                <?php
                    $this->beginContent($current_path."/home/terms-doctor-contract.php");
                    $this->endContent();
                 ?>
            </div>
            <div class="mt20">
                <?php
                if ($isContracted) {
                    echo '<a href="javascript:;" class="hideTerms btn btn-default btn-block" >已同意</a>';
                } else {
                    echo '<a id="signContract" href="javascript:;" class="btn btn-yes btn-block">我同意</a>';
                }
                ?>
            </div>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        $('#signContract').click(function () {
            J.showMask();
            $.ajax({
                url: '<?= $ajaxDoctorContract; ?>',
                success: function (data) {
                    if (data.status == 'ok') {
                        location.href = '<?= $urlDoctorDrView; ?>';
                    }
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    J.hideMask();
                    console.log(XmlHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                },
            });
        });
    });
</script>