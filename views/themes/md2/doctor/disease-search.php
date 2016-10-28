<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '选择疾病';
$diseaseResult = Url::to('disease-result');
$addDisease = Url::to('add-disease');
$diseaseCategoryToSub = Url::to('disease-category-to-sub');
$diseaseByCategoryId = Url::to(['disease-by-category-id', 'categoryid' => '']);
$id = \Yii::$app->request->get('id', '');
/*
 * source
 * 0:正常途径完善疾病信息
 * 1:从签约专家路径完善疾病信息，点击下一步，回到签约专家中的选择患者页面
 */
$source = \Yii::$app->request->get('source', '0');
$sourceReturn = \Yii::$app->request->get('returnUrl', '');
$this->params['show_footer'] = false;
?>
<header id="diseaseSearch_header" class="bg-green">
    <div class="grid w100">
        <div class="col-0 pl5 pr10">
            <a href="javascript:;" data-target="back">
                <div class="pl5">
                    <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
                </div>
            </a>
        </div>
        <div class="col-1 pt7 pb7 pr20">
            <div class="searchInput">您可以输入疾病名称</div>
        </div>
    </div>
</header>
<article id="diseaseSearch_article" class="active">
    <div class="grid">
        <div class="scrollModular col-0 w130p br-gray text-center">
            <ul id="deptList" class="list">

            </ul>
        </div>
        <div id="diseaseView" class="scrollModular col-1">

        </div>
    </div>
</article>
<script>
    $(document).ready(function() {
        J.showMask();
        var articleHeight = $('article').height();
        $('.scrollModular').css('height', articleHeight);
        $('.scrollModular').css('overflow', 'auto');
        //搜索疾病
        $('.searchInput').click(function() {
            location.href = '<?= $diseaseResult; ?>?source=' + '<?= $source; ?>&id=' + '<?= $id; ?>&returnUrl=' + '<?= $sourceReturn; ?>';
        });
        //科室列表
        $.ajax({
            url: '<?= $diseaseCategoryToSub; ?>',
            success: function(data) {
                if (data.status == 'ok') {
                    readyDept(data);
                }
            },
            error: function(XmlHttpRequest, textStatus, errorThrown) {
                J.hideMask();
                console.log(XmlHttpRequest);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });

        function readyDept(data) {
            var innerDept = '';
            var innerDisease = '';
            var results = data.results;
            if (results.length > 0) {
                for (var i = 0; i < results.length; i++) {
                    if (i == 0) {
                        innerDept += '<li class="deptChange bg-gray3" data-id="' + results[i].id + '">' + results[i].subCatName + '</li>';
                        innerDisease += '<div class="diseaseList" data-dept="' + results[i].id + '">';
                    } else {
                        innerDept += '<li class="deptChange" data-id="' + results[i].id + '">' + results[i].subCatName + '</li>';
                        innerDisease += '<div class="diseaseList hide" data-dept="' + results[i].id + '">';
                    }
                    var diseaseName = results[i].diseaseName;
                    if (diseaseName.length > 0) {
                        //常见疾病
                        var commonDisease = '<div class="bg-gray3 pad10">常见疾病</div><ul class="list">';
                        var allDisease = '<div class="bg-gray3 pad10">全部</div><ul class="list">';
                        //是否含有常见疾病
                        var bool = false;
                        for (var j = 0; j < diseaseName.length; j++) {
                            if (diseaseName[j].isCommon == 1) {
                                bool = true;
                                commonDisease += '<li class="selectDisease">' + diseaseName[j].name + '</li>';
                            }
                            allDisease += '<li class="selectDisease">' + diseaseName[j].name + '</li>';
                        }
                        commonDisease += '</ul>';
                        allDisease += '</ul>';
                        if (bool) {
                            innerDisease += commonDisease;
                        }
                        innerDisease += allDisease;
                    } else {
                        innerDisease += '<ul class="class"><li>暂无疾病</li></ul>';
                    }
                    innerDisease += '</div>';
                }
            }
            $('#deptList').html(innerDept);
            $('#diseaseView').html(innerDisease);
            J.hideMask();
            //科室切换
            $('.deptChange').click(function() {
                if (!$(this).hasClass('bg-gray3')) {
                    //科室添加背景色
                    $('.deptChange').each(function() {
                        $(this).removeClass('bg-gray3');
                    });
                    $(this).addClass('bg-gray3');
                    var id = $(this).attr('data-id');
                    //疾病显示、隐藏
                    $('.diseaseList').each(function() {
                        var deptId = $(this).attr('data-dept');
                        if (deptId == id) {
                            $(this).removeClass('hide');
                        } else {
                            $(this).addClass('hide');
                        }
                    });
                    $('#diseaseView').scrollTop(0);
                }
            });
            //选择疾病
            $('.selectDisease').click(function() {
                var name = $(this).text();
                location.href = '<?= $addDisease; ?>?source=' + '<?= $source; ?>&id=' + '<?= $id; ?>&returnUrl=' + '<?= $sourceReturn; ?>&diseaseName=' + name;
            });
        }
    });
</script>