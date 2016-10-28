<?php
use yii\web\View;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '搜索';
$urlAjaxSearch = Url::to('ajax-search');
$urlPatientView = Url::to(['view', 'id' => '']);
$this->params['show_footer'] = false;
?>

<nav id="patientSearchView_nav" class="header-secondary">
    <div class="w100 pl10 pr10 grid">
        <div class="col-1">
            <input type="text" placeholder="请输入患者姓名">
        </div>
        <div id="searchBtn" class="col-0 pt5 pl10 color-black">
            搜索
        </div>
    </div>
</nav>
<article id="searchView_article" class="active" data-scroll="true">
    <div>
    </div>
</article>
<script>
    $(document).ready(function () {
        $('#searchBtn').tap(function () {
            var searchName = $('input').val();
            if (searchName == '') {
                J.showToast('请输入患者姓名', '', '1000');
            } else {
                J.showMask();
                $.ajax({
                    url: '<?= $urlAjaxSearch; ?>?name=' + searchName,
                    success: function (data) {
                        var structureData = structure_data(data);
                        var returnData = do_decrypt(structureData, privkey);
                        returnData = analysis_data(returnData);
                        readyPage(returnData);
                    },
                    error: function () {
                        J.hideMask();
                    }
                });
            }
        });
        function readyPage(data) {
            var patientList = data.results.patientList;
            var html = '<div>';
            if (patientList == null) {
                html += '<div class="text-center mt50">无数据</div>';
            } else {
                for (var i = 0; i < patientList.length; i++) {
                    var patient = patientList[i];
                    var yearly = patient.age;
                    var yearlyText = '';
                    var monthly = "";
                    if (yearly == 0 && patient.age_month >= 0) {
                        yearlyText = '';
                        monthly = patient.age_month + '个月';
                    } else if (yearly <= 5 && patient.age_month > 0) {
                        yearlyText = yearly + '岁';
                        monthly = patient.age_month + '个月';
                    } else if (yearly > 5 && patient.age_month > 0) {
                        yearly++;
                        yearlyText = yearly + '岁';
                    } else {
                        yearlyText = yearly + '岁';
                    }
                    html += '<div class="bb5-gray">' +
                            '<div class="mt10 ml10 mr10 mb10">' +
                            '<a href="<?= $urlPatientView; ?>/' + patient.id + '/addBackBtn/1" class="color-000" data-target="link">' +
                            '<div class="">' +
                            '<div class=" mb10">' + patient.name + '</div>' +
                            '<div class=" mb10">' + patient.gender + ' &nbsp;|&nbsp; ' + yearlyText + monthly + ' &nbsp;|&nbsp; ' + patient.city_name + '</div>' +
                            '<div class=" mb10">' + patient.disease_name + '</div>' +
                            '</div>' +
                            '</a>' +
                            '</div>' +
                            '</div>'
                }
            }
            html += '</div>';
            $('#searchView_article').html(html);
            J.hideMask();
        }
    });
</script>