<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '设置密码';
$urlAjaxSetKey = Url::to('ajax-set-key');
$urlCardList = Url::to(['card-list', 'addBackBtn' => 1]);
$urlResImage = $this->theme->baseUrl . "/images/";
?>
<article class="active pwd_article" data-scroll="true">
    <div class="pad10">
        <div id="firstKeyView">
            <div>
                为了保护账户安全请设置密码
            </div>
            <div class="pwd-box mt10">
                <input type="tel" maxlength="6" class="pwd-input" id="firstKey-input">
                <div class="fake-box grid">
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                </div>
            </div>
            <div class="mt40">
                <a id="firstKey" href="javascript:;" class="btn btn-full btn-yellow2">下一步</a>
            </div>
        </div>
        <div id="confirmKeyView" class="hide">
            <div>
                确认密码
            </div>
            <div class="pwd-box mt10">
                <input type="tel" maxlength="6" class="pwd-input" id="confirmKey-input">
                <div class="fake-box grid">
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                    <div class="col-1">
                        <input type="password" readonly="">
                    </div>
                </div>
            </div>
            <div class="mt40">
                <a id="submitBtn" href="javascript:;" class="btn btn-full btn-yellow2">确认密码</a>
            </div>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        var firstKey = '';
        var confirmKey = '';
        var firstInput = $("#firstKeyView .fake-box input");
        $("#firstKey-input").on("input", function () {
            firstKey = $(this).val().trim();
            for (var i = 0; i < firstKey.length; i++) {
                firstInput.eq("" + i + "").val(firstKey[i]);
            }
            var len = firstKey.length;
            firstInput.each(function () {
                var index = $(this).parents('.col-1').index();
                if (index >= len) {
                    $(this).val("");
                }
            });
        });
        var confirmInput = $("#confirmKeyView .fake-box input");
        $("#confirmKey-input").on("input", function () {
            confirmKey = $(this).val().trim();
            for (var i = 0; i < confirmKey.length; i++) {
                confirmInput.eq("" + i + "").val(confirmKey[i]);
            }
            var len = confirmKey.length;
            confirmInput.each(function () {
                var index = $(this).parents('.col-1').index();
                if (index >= len) {
                    $(this).val("");
                }
            });
        });

        $('#firstKey').click(function () {
            if (firstKey.length != 6) {
                J.showToast('请输入六位密码', '', '1500');
                return;
            }
            $('#firstKeyView').addClass('hide');
            $('#confirmKeyView').removeClass('hide');
        });
        $('#submitBtn').click(function () {
            var btnSubmit = $('#submitBtn');
            if (confirmKey.length != 6) {
                J.showToast('请输入6位密码', '', '1500');
                return;
            }
            if (firstKey != confirmKey) {
                J.closePopup();
                J.customConfirm('',
                        '<div class="mt10 mb10">两次密码不同</div>',
                        '<a id="closeLogout" class="w50">取消</a>',
                        '<a id="emptyKey" class="color-green w50">重新输入</a>',
                        function () {
                        },
                        function () {
                        });
                $('#closeLogout').click(function () {
                    J.closePopup();
                });
                $('#emptyKey').click(function () {
                    $("#confirmKey-input").val('');
                    confirmInput.each(function () {
                        $(this).val('');
                    });
                    confirmKey = '';
                    J.closePopup();
                });
                return;
            }
            disabled(btnSubmit);
            var data = '{"bank":{"userkey":' + confirmKey + '}}';
            var encryptContext = do_encrypt(data, pubkey);
            var param = {param: encryptContext};
            $.ajax({
                type: 'post',
                url: '<?= $urlAjaxSetKey; ?>',
                data: param,
                success: function (data) {
                    if (data.status = 'ok') {
                        location.href = '<?= $urlCardList; ?>';
                    } else {
                        enable(btnSubmit);
                    }
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    enable(btnSubmit);
                    console.log(XmlHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });
    });
</script>