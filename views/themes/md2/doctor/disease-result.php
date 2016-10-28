<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '搜索疾病';
$searchDisease = Url::to(['search-disease', 'is_like' => 1, 'name' => '']);
$addDisease = Url::to('add-disease');
$id = \Yii::$app->request->get('id', '');
/*
 * source
 * 0:正常途径完善疾病信息
 * 1:从签约专家路径完善疾病信息，点击下一步，回到签约专家中的选择患者页面
 */
$source = \Yii::$app->request->get('source', '0');
$sourceReturn = \Yii::$app->request->get('returnUrl', '');
?>
<header class="searchHeader bg-green">
    <div class="grid w100">
        <div class="col-1 pl20">
            <i class="icon_search"></i>
            <input class="icon_input" type="text" placeholder="您可以输入疾病名称">
            <a class="icon_clear hide"></a>
        </div>
        <a href="javascript" class="col-0 pl5 pr5 color-white" data-target="back">
            取消
        </a>
    </div>
</header>
<article class="active" data-scroll="true">
    <div id="searchDiseaseView">

    </div>
</article>
<script>
    $(document).ready(function() {
        $("header").on("input", function() {
            var searchValue = $('input').val();
            if (Trim(searchValue) == '') {
                $('.icon_clear').addClass('hide');
                $('#searchDiseaseView').html('');
                return false;
            } else if (searchValue.match(/[a-zA-Z]/g) != null) {
                $('.icon_clear').removeClass('hide');
                var innerHtml = '<ul class="list"><li>没有找到该疾病</li><li class="selectDisease color-green" data-name="' + searchValue + '">使用"' + searchValue + '"为您想要的疾病名称.</li></ul>';
                $('#searchDiseaseView').html(innerHtml);
                //选择疾病
                selectDisease()
                return false;
            } else if (searchValue != '') {
                $('.icon_clear').removeClass('hide');
                ajaxSearch(searchValue);
            }
        });

        //清空input
        $('.icon_clear').click(function() {
            $('.icon_input').val('');
            $(this).addClass('hide');
            $('#searchDiseaseView').html('');
        });

        function ajaxSearch(searchValue) {
            $.ajax({
                type: 'get',
                url: '<?= $searchDisease; ?>' + searchValue,
                success: function(data) {
                    if (data.status == 'ok') {
                        readyPage(data, searchValue);
                    }
                }
            });
        }

        function readyPage(data, searchValue) {
            var innerHtml = '';
            var results = data.results;
            if (results.length > 0) {
                innerHtml += '<ul class="list">';
                for (var i = 0; i < results.length; i++) {
                    innerHtml += '<li class="selectDisease" data-name="' + results[i].name + '">' + results[i].name + '</li>';
                }
                innerHtml += '<ul>';
            } else {
                innerHtml += '<ul class="list"><li>没有找到该疾病</li>' +
                        '<li class="selectDisease color-green" data-name="' + searchValue + '">使用"' + searchValue + '"为您想要的疾病名称.</li></ul>';
            }
            $('#searchDiseaseView').html(innerHtml);
            //选择疾病
            selectDisease();
        }

        function selectDisease() {
            $('.selectDisease').click(function() {
                var diseaseName = $(this).attr('data-name');
                location.href = '<?= $addDisease; ?>?source=' + '<?= $source; ?>&id=' + '<?= $id; ?>&returnUrl=' + '<?= $sourceReturn; ?>&diseaseName=' + diseaseName;
            });
        }

        function Trim(str) {
            return str.replace(/(^\s*)|(\s*$)/g, "");
        }
    });
</script>