<?php
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 */
$this->title = '专家签约';
$urlResImage = $this->theme->baseUrl . "/images/";
$urlCreateDoctorHz = Url::to(["create-doctor-hz", 'addBackBtn' => 1]);
$urlCreateDoctorZz = Url::to(["create-doctor-zz", 'addBackBtn' => 1]);
$urlQuestionnaire = Url::to('questionnaire');
$urlAjaxViewDoctorHz = Url::to("ajax-view-doctor-hz");
$urlAjaxViewDoctorZz = Url::to("ajax-view-doctor-zz");
$urlDoctorHzSubmit = Url::to('ajax-doctor-hz');
$urlDoctorZzSubmit = Url::to('ajax-doctor-zz');
$urlDoctorView = Url::to('view');
$urlDoctorContract = Url::to(['contract', 'addBackBtn' => 1]);
$this->params['show_footer'] = false;
?>

<header id="drView_header" class="bg-green">
    <nav class="left">
        <a href="<?= $urlDoctorView; ?>" data-target="link">
            <div class="pl5">
                <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
            </div>
        </a>
    </nav>
    <h1 class="title">签约成功</h1>
    <nav class="right">
        <a href="<?= $urlDoctorContract; ?>">
            服务协议
        </a>
    </nav>
</header>
<article id="drView_article" class="active bg-gray3" data-scroll="true">
    <div class="color-black6 pt10 pl10 pr10 pb50">
        <div>感谢您成为名医主刀签约专家!</div>
        <div>后期我们会为您推荐符合您要求的患者。</div>
        <div class="bg-white br5 mt20">
            <div class="grid pad10 bb-gray">
                <div class="col-1 doctorIcon">接受转诊病人</div>
                <div class="col-0">
                    <a href="<?= $urlQuestionnaire; ?>" class="modifyBtn">
                        修改
                    </a>
                </div>
            </div>
            <div class="pad10 zhuanzhenInfo">

            </div>
        </div>
        <div class="bg-white br5 mt20">
            <div class="grid pad10 bb-gray">
                <div class="col-1 doctorIcon">去外地会诊</div>
                <div class="col-0">
                    <a href="<?= $urlQuestionnaire; ?>" class="modifyBtn">
                        修改
                    </a>
                </div>
            </div>
            <div class="pad10 huizhenInfo">

            </div>
        </div>
    </div>
</article>
<script>
    $(document).ready(function () {
        ajaxViewDoctorHz();
        ajaxViewDoctorZz();
        $(".huizhen").tap(function () {
            J.customConfirm('',
                    '<div class="mt10 mb10">确认暂不参与外地会诊吗?</div>',
                    '<a id="closeLogout" class="w50">取消</a>',
                    '<a data="ok" class="color-green w50">暂不参与</a>',
                    function () {
                        ajaxRemoveDoctorHz();
                    },
                    function () {
                    });
            $('#closeLogout').click(function () {
                J.closePopup();
            });
        });
        $(".zhuanzhen").tap(function () {
            J.customConfirm('',
                    '<div class="mt10 mb10">确认暂不参与病人转诊吗?</div>',
                    '<a id="closeLogout" class="w50">取消</a>',
                    '<a data="ok" class="color-green w50">暂不参与</a>',
                    function () {
                        ajaxRemoveDoctorZz();
                    },
                    function () {
                    });
            $('#closeLogout').click(function () {
                J.closePopup();
            });
        });
    });
    //加载会诊信息
    function ajaxViewDoctorHz() {
        $.ajax({
            url: '<?= $urlAjaxViewDoctorHz; ?>',
            async: false,
            success: function (data) {
                //构造json
                var structureData = structure_data(data);
                //解密
                var returnData = do_decrypt(structureData, privkey);
                //解析数据
                returnData = analysis_data(returnData);
                if (returnData.results.userDoctorHz != null) {
                    setDoctorHzInfo(returnData.results.userDoctorHz);
                }
            }
        });
    }
    //加载转诊信息
    function ajaxViewDoctorZz() {
        $.ajax({
            url: '<?= $urlAjaxViewDoctorZz; ?>',
            async: false,
            success: function (data) {
                //构造json
                var structureData = structure_data(data);
                //解密
                var returnData = do_decrypt(structureData, privkey);
                //解析数据
                returnData = analysis_data(returnData);
                if (returnData.results.userDoctorZz != null) {
                    setDoctorZzInfo(returnData.results.userDoctorZz);
                }
            }
        });
    }
    //设置会诊html
    function setDoctorHzInfo(userDoctorHz) {
        if (userDoctorHz && userDoctorHz.is_join != 0) {
            var infoHtml = '<div class="font-s14">';
            var prep_days = '';
            var travel_duration = travelDurationToString(userDoctorHz.travel_duration);
            var weeks = weeksToString(userDoctorHz.week_days);
            var patients_prefer = userDoctorHz.patients_prefer == '' ? '暂无信息' : userDoctorHz.patients_prefer;
            var fee_min = userDoctorHz.fee_min == null ? '无' : userDoctorHz.fee_min;
            var fee_max = userDoctorHz.fee_max == null ? '无' : userDoctorHz.fee_max;
            var freq_destination = userDoctorHz.freq_destination == '' ? '暂无信息' : userDoctorHz.freq_destination;
            var destination_req = userDoctorHz.destination_req == '' ? '暂无信息' : userDoctorHz.destination_req;
            var min_no_surgery = userDoctorHz.min_no_surgery == null ? '暂无信息' : userDoctorHz.min_no_surgery;
            infoHtml += '<div class="mt5"><span class="color-gray">外出会诊台数：</span>' + min_no_surgery + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">时间成本要求：</span>' + travel_duration + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">方便会诊时间：</span>' + weeks + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">愿意会诊病例：</span>' + patients_prefer + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">每台咨询费区间：</span>' + fee_min + '元 - ' + fee_max + '元</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">常去地区或医院：</span>' + freq_destination + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">对手术地点/要求条件：</span>' + destination_req + '</div>';
            infoHtml += '</div>';
            $(".huizhenInfo").html(infoHtml);
        } else {
            $('.huizhenInfo').html('<div class="text-center color-gray">暂不接受</div>');
        }
    }

    //设置转诊html
    function setDoctorZzInfo(userDoctorZz) {
        if (userDoctorZz && userDoctorZz.is_join != 0) {
            var infoHtml = '<div class="font-s14">';
            var fee = '';
            if (userDoctorZz.fee == null) {
                fee = '暂无信息';
            } else if (userDoctorZz.fee == 0) {
                fee = '不需要';
            } else {
                fee = userDoctorZz.fee + '元';
            }
            var preferredPatient = userDoctorZz.preferredPatient == '' ? '暂无信息' : userDoctorZz.preferredPatient;
            var prep_days = userDoctorZz.prep_days == '' ? '暂无信息' : userDoctorZz.prep_days;
            infoHtml += '<div class="mt5"><span class="color-gray">转诊费：</span>' + fee + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">对转诊病例的要求：</span>' + preferredPatient + '</div>';
            infoHtml += '<div class="mt5"><span class="color-gray">最快安排床位时间：</span>' + prep_days + '</div>';
            infoHtml += '</div>';
            $('.zhuanzhenInfo').html(infoHtml);
        } else {
            $('.zhuanzhenInfo').html('<div class="text-center color-gray">暂不接受</div>');
        }
    }

    //选择不参与异步修改会诊信息
    function ajaxRemoveDoctorHz() {
        var formdata = '{"form":{"disjoin":"0"}}';
        var encryptContext = do_encrypt(formdata, pubkey);
        var param = {param: encryptContext};
        $.ajax({
            type: 'post',
            url: '<?= $urlDoctorHzSubmit ?>',
            data: param,
            'success': function (data) {
                if (data.status == 'ok') {
                    $('.huizhenInfo').remove();
                    //J.showToast('修改成功', '', 500);
                } else {
                    J.showToast('修改失败', '', 500);
                }
            },
            'error': function (data) {
                console.log(data);
            },
            'complete': function () {
            }
        });
    }
    //选择不参与异步修改转诊信息
    function ajaxRemoveDoctorZz() {
        var formdata = '{"form":{"disjoin":"0"}}';
        var encryptContext = do_encrypt(formdata, pubkey);
        var param = {param: encryptContext};
        $.ajax({
            type: 'post',
            url: '<?= $urlDoctorZzSubmit ?>',
            data: param,
            'success': function (data) {
                if (data.status == 'ok') {
                    $('.zhuanzhenInfo').remove();
                    //J.showToast('修改成功', '', 500);
                } else {
                    J.showToast('修改失败', '', 500);
                }
            },
            'error': function (data) {
                console.log(data);
            },
            'complete': function () {
            }
        });
    }

    function travelDurationToString(travel_durations) {
        var travel_duration = '';
        for (var i = 0; i < travel_durations.length; i++) {
            var travel = travel_durations[i];
            if (travel == 'train3h') {
                travel_duration += '高铁3小时内、';
            } else if (travel == 'plane2h') {
                travel_duration += '飞机2小时内、';
            } else if (travel == 'train5h') {
                travel_duration += '高铁5小时内、';
            } else if (travel == 'plane3h') {
                travel_duration += '飞机3小时内、';
            } else if (travel == 'none') {
                travel_duration += '无特殊要求、';
            }
        }
        travel_duration = travel_duration.substring(0, travel_duration.length - 1);
        return travel_duration == '' ? '暂无信息' : travel_duration;
    }

    function weeksToString(week_days) {
        var weeks = '';
        for (var i = 0; i < week_days.length; i++) {
            var week = week_days[i];
            if (week == 1) {
                weeks += '周一、';
            } else if (week == 2) {
                weeks += '周二、';
            } else if (week == 3) {
                weeks += '周三、';
            } else if (week == 4) {
                weeks += '周四、';
            } else if (week == 5) {
                weeks += '周五、';
            } else if (week == 6) {
                weeks += '周六、';
            } else if (week == 7) {
                weeks += '周日、';
            }
        }
        weeks = weeks.substring(0, weeks.length - 1);
        return weeks == '' ? '暂无信息' : weeks;
    }
</script>