<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this yii\web\View
 */
$this->title = '签约专家';
$urlResImage = $this->theme->baseUrl . "/images/";
$state = \Yii::$app->request->get('state', '');
$source = \Yii::$app->request->get('source', 0);
$pid = \Yii::$app->request->get('pid', '');
$patient_booking_create = Url::to('patientBooking/create');
$disease_sub_category = \Yii::$app->request->get('disease_sub_category', '');
$page = \Yii::$app->request->get('page', '');
$urlAjaxContractDoctor = Url::to('ajax-contract-doctor');
$urlViewContractDoctors = Url::to('view-contract-doctors');
$urlState = Url::to('ajax-state-list');
$urlDept = Url::to('ajax-dept-list');
$urlDoctorView = Url::to(['view-doctor', 'id' => '']);
$urlSearchContractDoctors = Url::to(['doctor-list', 'pid' => $pid]);
$this->params['show_footer'] = false;
?>
<?//= Html::jsFile('http://static.mingyizhudao.com/md2/viewContractDoctors.min.1.9.js') ?>

<style>
    #jingle_popup{
        text-align: inherit;
    }
    .activeIcon{background: url('http://static.mingyizhudao.com/146729114401744') no-repeat;background-size: 3px 21px;background-position-y:15px; }
</style>
<?php
$navTop = 'top0p';
$articleTop = 'top50p';
if ($source == 1) {
    $navTop = '';
    $articleTop = 'top84p';
    ?>
    <header id="viewContractDoctors_article" class="bg-green">
        <div class="grid w100">
            <div class="col-0 pl5 pr10">
                <a href="javascript:;" data-target="back">
                    <div class="pl5">
                        <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
                    </div>
                </a>
            </div>
            <div class="col-1 pt7 pb7 pr20">
                <a href="<?= $urlSearchContractDoctors; ?>" class="searchInput">搜索您想要的医生</a>
            </div>
        </div>
    </header>
    <?php
}
?>
<nav id="contractDoctors_nav" class="header-secondary bg-white <?= $navTop; ?>">
    <div class="grid w100 font-s16 color-black6">
        <div id="deptSelect" class="col-1 w33 br-gray bb-gray grid middle grayImg">
            <span id="deptTitle" data-dept="">专业</span><img src="http://static.mingyizhudao.com/147323378222999">
        </div>
        <div id="stateSelect" class="col-1 w33 br-gray bb-gray grid middle grayImg">
            <span id="stateTitle" data-state="">地区</span><img src="http://static.mingyizhudao.com/147323378222999">
        </div>
        <div id="hospitalSelect" class="col-1 w33 bb-gray grid middle grayImg">
            <span id="hospitalTitle" data-hospital="">医院</span><img src="http://static.mingyizhudao.com/147323378222999">
        </div>
    </div>
</nav>
<article id="contractDoctors_article" class="active <?= $articleTop; ?>" data-scroll="true" data-source="<?= $source; ?>">
    <div id="docPage">

    </div>
</article>
<script>
    $(document).ready(function () {
        J.showMask();
        //请求医生
        $requestDoc = '<?= $urlAjaxContractDoctor; ?>';

        //签约专家访问地址
        $requestViewContractDoctors = '<?= $urlViewContractDoctors; ?>';

        //预约页面
        if ('<?= $source ?>' == 1) {
            $doctorTrigger = '<?= $patient_booking_create; ?>/pid/' + '<?= $pid; ?>';
        } else {
            $doctorTrigger = '<?= $urlDoctorView; ?>';
        }

        $condition = new Array();
        $condition["source"] = '<?= $source; ?>';
        $condition["hospital"] = '';
        $condition["state"] = '<?= $state ?>';
        $condition["disease_sub_category"] = '<?= $disease_sub_category; ?>';
        $condition["page"] = '<?= $page == '' ? 1 : $page; ?>';

        var urlAjaxLoadDoctor = '<?= $urlAjaxContractDoctor; ?>?getcount=1';
        //J.showMask();
        $.ajax({
            url: urlAjaxLoadDoctor,
            success: function (data) {
                readyDoc(data);
                $hospital = data.results.hospital;
            }
        });


        //ajax异步加载地区
        $stateHtml = '';
        var requestState = '<?= $urlState; ?>';
        $.ajax({
            url: requestState,
            success: function (data) {
                $stateHtml = readyState(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
        //ajax 异步加载医院
        // $hospital='';

        $deptId = '';
        deptId = '';
        //ajax异步加载科室
        $deptHtml = '';
        var urlloadDiseaseCategory = '<?= $urlDept; ?>';
        $.ajax({
            url: urlloadDiseaseCategory,
            success: function (data) {
                $deptHtml = readyDept(data);
            }
        });

        function readyDept(data) {
            var results = data.results;
            var innerHtml = '';
            if ('<?php echo $source; ?>' == 1) {
                innerHtml += '<div id="deptScroll" class="color-black" style="margin-top:93px;height:315px;" data-scroll="true">';
            } else {
                innerHtml += '<div id="deptScroll" class="color-black" style="margin-top:49px;height:315px;" data-scroll="true">';
            }
            innerHtml += '<ul class="list">';
            if (results.length > 0) {
                var deptId = $('#deptTitle').attr('data-dept');

                for (var i = 0; i < results.length; i++) {
                    innerHtml += '<li class="cDept" data-dept="' + results[i].id + '">' + results[i].name + '</li>';
                }
            }
            innerHtml += '</ul></div></div>';
            return innerHtml;
        }

        function readyState(data) {
            var stateList = data.results.stateList;
            var innerHtml = '';
            if ('<?= $source; ?>' == 1) {
                innerHtml += '<div id="cityScroll" data-scroll="true" style="height:315px;margin-top:93px;">';
            } else {
                innerHtml += '<div id="cityScroll" data-scroll="true" style="height:315px;margin-top:49px;">';
            }
            innerHtml += '<ul class="list">'
                    + '<li class="state" data-state="">全部</li>';
            for (var s in stateList) {
                var stateId = $('#stateTitle').attr('data-state');
                innerHtml += '<li class="state" data-state="' + s + '">' + stateList[s] + '</li>';
            }
            innerHtml += '</ul></div>';
            return innerHtml;
        }

        function readyHospital(){
            var source=$("article").attr("data-source");
            var innerHtml="";
            if(source==1){
                innerHtml+='<div id="hospitalScroll" data-scroll="true" style="height:315px;margin-top:93px;">'
            }else{
                innerHtml+='<div id="hospitalScroll" data-scroll="true" style="height:315px;margin-top:49px;">'
            }
            innerHtml+='<ul class="list">';
            if($hospital!=null){
                for(var i=0;i<$hospital.length;i++){
                    innerHtml+='<li class="hospital" data-hospital="'+$hospital[i].id+'">'+$hospital[i].name+"</li>"
                }
            }
            innerHtml+="</ul></div>";return innerHtml
        }

        $("#deptSelect").tap(function(){
            var deptName=$("#deptTitle").html();
            var deptId=$("#deptTitle").attr("data-dept");
            var stateName=$("#stateTitle").html();
            var stateId=$("#stateTitle").attr("data-state");
            var hpName=$("#hospitalTitle").html();
            var hpId=$("#hospitalTitle").attr("data-hospital");
            var source=$("article").attr("data-source");
            var innerPage='<div id="findDoc_section">';
            if(source==1){
                innerPage+='<header id="viewContractDoctors_article" class="bg-green">'+'<div class="grid w100">'+'<div class="col-0 pl5 pr10">'+'<a href="javascript:;" data-target="back">'+'<div class="pl5">'+'<img src="http://static.mingyizhudao.com/146968435878253" class="w11p">'+"</div>"+"</a>"+"</div>"+'<div class="col-1 pt7 pb7 pr20">'+'<div class="searchInput">搜索您想要的医生</div>'+"</div>"+"</div>"+"</header>"+'<nav id="contractDoctors_nav" class="header-secondary bg-white">'
            }else{
                innerPage+='<nav id="contractDoctors_nav" class="header-secondary bg-white top0p">'
            }
            innerPage+='<div class="grid w100 color-black font-s16 color-black6">'+'<div id="deptSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="deptTitle" data-dept="'+deptId+'">'+deptName+'</span><img src="http://static.mingyizhudao.com/147323538128982">'+"</div>"+'<div id="stateSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="stateTitle" data-state="'+stateId+'">'+stateName+'</span><img src="http://static.mingyizhudao.com/147323378222999">'+"</div>"+'<div id="hospitalSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="hospitalTitle" data-hospital="'+hpId+'">'+hpName+'</span><img src="http://static.mingyizhudao.com/147323378222999">'+"</div>"+"</div>"+"</nav>"+'<article id="contractDoctors_article" class="active" style="position:static;">'+$deptHtml+"</article>"+"</div>";
            J.popup({
                html:innerPage,pos:"top",showCloseBtn:false
            });
            $(".cDept").each(function(){
                if($(this).attr("data-dept")==deptId){
                    $(this).addClass("activeIcon");
                    var top=0;
                    if(source==1){
                        top=$(this).position().top-93
                    } else{
                        top=$(this).position().top-49
                    }
                    $("#deptScroll").scrollTop(top)
                }
            });
            $(".cDept").click(function(e){
                e.preventDefault();
                $deptId=$(this).attr("data-dept");
                $deptName=$(this).html();
                $condition["hospital"]="";
                $condition["state"]="";
                $condition["disease_sub_category"]=$deptId;
                $condition["page"]=1;
                J.closePopup();
                var requestUrl=$requestDoc+"?"+setUrlCondition()+"&getcount=1";J.showMask();
                $.ajax({
                    url:requestUrl,
                    success:function(data){
                        $hospital=data.results.hospital;
                        readyDoc(data);
                        $deptName=$deptName.length>4?$deptName.substr(0,3)+"...":$deptName;
                        $("#deptTitle").html($deptName);
                        $("#deptTitle").attr("data-dept",$deptId);
                        $("#stateTitle").html("地区");
                        $("#stateTitle").attr("data-state","");
                        $("#hospitalTitle").html("医院");
                        $("#hospitalTitle").attr("data-hospital","");
                        setLocationUrl();
                        $("#contractDoctors_article").scrollTop(0)
                    }
                })
            })
        });

        $("#stateSelect").tap(function(){
            var deptName=$("#deptTitle").html();
            var deptId=$("#deptTitle").attr("data-dept");
            var stateName=$("#stateTitle").html();
            var stateId=$("#stateTitle").attr("data-state");
            var hpName=$("#hospitalTitle").html();
            var hpId=$("#hospitalTitle").attr("data-hospital");
            var source=$("article").attr("data-source");
            var innerPage='<div id="findDoc_section">';
            if(source==1){
                innerPage+='<header id="viewContractDoctors_article" class="bg-green">'+'<div class="grid w100">'+'<div class="col-0 pl5 pr10">'+'<a href="javascript:;" data-target="back">'+'<div class="pl5">'+'<img src="http://static.mingyizhudao.com/146968435878253" class="w11p">'+"</div>"+"</a>"+"</div>"+'<div class="col-1 pt7 pb7 pr20">'+'<div class="searchInput">搜索您想要的医生</div>'+"</div>"+"</div>"+"</header>"+'<nav id="contractDoctors_nav" class="header-secondary bg-white">'
            }else{
                innerPage+='<nav id="contractDoctors_nav" class="header-secondary bg-white top0p">'
            }
            innerPage+='<div class="grid w100 color-black font-s16 color-black6">'+'<div id="deptSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="deptTitle" data-dept="'+deptId+'">'+deptName+'</span><img src="http://static.mingyizhudao.com/147323378222999">'+"</div>"+'<div id="stateSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="stateTitle" data-state="'+stateId+'">'+stateName+'</span><img src="http://static.mingyizhudao.com/147323538128982">'+"</div>"+'<div id="hospitalSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="hospitalTitle" data-hospital="'+hpId+'">'+hpName+'</span><img src="http://static.mingyizhudao.com/147323378222999">'+"</div>"+"</div>"+"</nav>"+'<article id="contractDoctors_article" class="active" style="position:static;">'+$stateHtml+"</article>"+"</div>";
            J.popup({
                html:innerPage,
                pos:"top",
                showCloseBtn:false
            });
            $(".state").each(function(){
                if($(this).attr("data-state")==stateId){
                    $(this).addClass("activeIcon");
                    var top=0;
                    if(source==1){
                        top=$(this).position().top-93
                    }else{
                        top=$(this).position().top-49
                    }
                    $("#cityScroll").scrollTop(top)
                }
            });
            $(".state").click(function(e){
                e.preventDefault();
                $deptId=$("#deptTitle").attr("data-dept");
                $deptName=$("#deptTitle").html();
                $stateId=$(this).attr("data-state");
                $stateName=$(this).html();
                $condition["hospital"]="";
                $condition["disease_sub_category"]=$deptId;
                $condition["state"]=$stateId;
                $condition["page"]=1;
                J.closePopup();
                var requestUrl=$requestDoc+"?"+setUrlCondition()+"&getcount=1";
                J.showMask();
                $.ajax({
                    url:requestUrl,
                    success:function(data){
                        $hospital=data.results.hospital;
                        readyDoc(data);
                        $("#stateTitle").html($stateName);
                        $("#stateTitle").attr("data-state",$stateId);
                        $("#deptTitle").attr("data-dept",$deptId);
                        $("#hospitalTitle").html("医院");
                        $("#hospitalTitle").attr("data-hospital","");
                        setLocationUrl();
                        $("#contractDoctors_article").scrollTop(0)
                    }
                })
            })
        });
        $("#hospitalSelect").tap(function(){
            var deptName=$("#deptTitle").html();
            var deptId=$("#deptTitle").attr("data-dept");
            var stateName=$("#stateTitle").html();
            var stateId=$("#stateTitle").attr("data-state");
            var hpName=$("#hospitalTitle").html();
            var hpId=$("#hospitalTitle").attr("data-hospital");
            var source=$("article").attr("data-source");
            var innerPage='<div id="findDoc_section">';
            if(source==1){
                innerPage+='<header id="viewContractDoctors_article" class="bg-green">'+'<div class="grid w100">'+'<div class="col-0 pl5 pr10">'+'<a href="javascript:;" data-target="back">'+'<div class="pl5">'+'<img src="http://static.mingyizhudao.com/146968435878253" class="w11p">'+"</div>"+"</a>"+"</div>"+'<div class="col-1 pt7 pb7 pr20">'+'<div class="searchInput">搜索您想要的医生</div>'+"</div>"+"</div>"+"</header>"+'<nav id="contractDoctors_nav" class="header-secondary bg-white">'
            }else{
                innerPage+='<nav id="contractDoctors_nav" class="header-secondary bg-white top0p">'
            }
            innerPage+='<div class="grid w100 color-black font-s16 color-black6">'+'<div id="deptSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="deptTitle" data-dept="'+deptId+'">'+deptName+'</span><img src="http://static.mingyizhudao.com/147323378222999">'+"</div>"+'<div id="stateSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="stateTitle" data-state="'+stateId+'">'+stateName+'</span><img src="http://static.mingyizhudao.com/147323378222999">'+"</div>"+'<div id="hospitalSelect" data-target="closePopup" class="col-1 w33 br-gray bb-gray grid middle grayImg">'+'<span id="hospitalTitle" data-hospital="'+hpId+'">'+hpName+'</span><img src="http://static.mingyizhudao.com/147323538128982">'+"</div>"+"</div>"+"</nav>"+'<article id="contractDoctors_article" class="active" style="position:static;">'+readyHospital()+"</article>"+"</div>";
            J.popup({
                html:innerPage,
                pos:"top",
                showCloseBtn:false
            });
            $(".hospital").each(function(){
                if($(this).attr("data-hospital")==hpId){
                    $(this).addClass("activeIcon");
                    var top=0;
                    if(source==1){
                        top=$(this).position().top-93
                    }else{
                        top=$(this).position().top-49
                    }
                    $("#hospitalScroll").scrollTop(top)
                }
            });
            $(".hospital").click(function(e){
                e.preventDefault();
                $hpId=$(this).attr("data-hospital");
                $hpName=$(this).html();
                $deptId=$("#deptTitle").attr("data-dept");
                $stateId=$("#stateTitle").attr("data-state");
                $condition["hospital"]=$hpId;
                $condition["disease_sub_category"]=$deptId;
                $condition["state"]=$stateId;
                $condition["page"]=1;
                J.closePopup();
                var requestUrl=$requestDoc+"?"+setUrlCondition()+"&getcount=1";
                J.showMask();
                $.ajax({
                    url:requestUrl,
                    success:function(data){
                        readyDoc(data);
                        $hpName=$hpName.length>4?$hpName.substr(0,3)+"...":$hpName;
                        $("#hospitalTitle").html($hpName);
                        $("#hospitalTitle").attr("data-hospital",$hpId);
                        setLocationUrl();
                        $("#contractDoctors_article").scrollTop(0)
                    }
                })
            })
        });

        function checkCityList(){
            if($cityData.curRes){
                var cArray=[];
                var cityData=$cityData.results;
                var cityCurData=$cityData.curRes;
                for(var c in cityCurData){
                    for(var j=0;j<cityData.length;j++){
                        if(cityCurData[c]==cityData[j].id){
                            cArray.push(cityData[j])
                        }
                    }
                }
                return readyCity(cArray,0)
            }else{
                return readyCity($cityData.results,0)
            }
        }

        function readyDoc(data){
            var results=data.results.doctors;
            var source=$("article").attr("data-source");
            var innerHtml="";
            if(results){
                if(results.length>0){
                    for(var i=0;i<results.length;i++){
                        var btGray=i==0?"":"bt-gray2";
                        var hp_dept_desc=(results[i].desc==""||results[i].desc==null)?"暂无信息":results[i].desc;
                        hp_dept_desc=hp_dept_desc.length>40?hp_dept_desc.substr(0,40)+"...":hp_dept_desc;
                        innerHtml+='<div><div class="bb10-gray"></div>';
                        var selectDoctor="";
                        if(source==1){
                            selectDoctor=$doctorTrigger+"/expectHospital/"+results[i].hpName+"/expectDept/"+results[i].hpDeptName+"/expectDoctor/"+results[i].name
                        }else{
                            selectDoctor=$doctorTrigger+"/"+results[i].id
                        }
                        innerHtml+='<a href="'+selectDoctor+'" data-target="link">'+'<div class="grid pl15 pr15 pt10 pb10 '+btGray+'">'+'<div class="col-1 w25">'+'<div class="w60p h60p br50 overflow-h"><img class="imgDoc" src="'+results[i].imageUrl+'"></div>';
                        var doctorAtitle="";
                        if(results[i].aTitle!="无"){
                            doctorAtitle=results[i].aTitle
                        }
                        innerHtml+="</div>"+'<div class="ml10 col-1 w75">'+'<div class="color-black2 font-s16">'+results[i].name+'<span class="ml5 color-black6 font-s14">'+doctorAtitle+"</span></div>";
                        if(results[i].hpDeptName==""||results[i].hpDeptName==null){
                            if(results[i].mTitle==""||results[i].mTitle==null){
                                innerHtml+=""
                            }else{
                                innerHtml+='<div class="color-black6">'+results[i].mTitle+"</div>"
                            }
                        }else{
                            if(results[i].mTitle==""||results[i].mTitle==null){
                                innerHtml+='<div class="color-black6">'+results[i].hpDeptName+"</div>"
                            }else{
                                innerHtml+='<div class="color-black6">'+results[i].hpDeptName+'<span class="ml5">'+results[i].mTitle+"</span></div>"
                            }
                        }
                        if(results[i].hpName!=""&&results[i].hpName!=null){
                            innerHtml+='<div class="font-s12 pt5"><span class="hosIcon">'+results[i].hpName+"</span></div>"
                        }
                        innerHtml+="</div>"+"</div>"+"</a>";
                        innerHtml+='<div class="pl15 pr15 pt5 pb10 font-s12 color-black bb-gray2">'+'擅长:<span class="color-gray">'+hp_dept_desc+"</span></div>"+"</div>"
                    }
                }
            }else{
                innerHtml+='<div class="grid pl15 pr15 pt10 pb10 bb-gray2">暂无信息</div>'
            }

            if(data.dataNum!=null){
                var dataPage=Math.ceil(data.dataNum/12);
                if(dataPage>1){
                    innerHtml+='<div class="bb10-gray"></div><div class="grid pl15 pr15 pt10 bb-gray3 bt-gray2"><div class="grid w100">'+'<div class="col-1 w40">'+'<button id="previousPage" type="button" class="button btn-yellow">上一页</button>'+'</div><div class="col-1 w20">'+'<select id="selectPage" onchange="changePage()">';
                    var nowPage=$condition["page"];
                    for(var i=1;i<=dataPage;i++){
                        if(nowPage==i){
                            innerHtml+='<option id="quickPage" value="'+i+'" selected = "selected">'+i+"</option>"
                        }else{
                            innerHtml+='<option id="quickPage" value="'+i+'">'+i+"</option>"
                        }
                    }
                    innerHtml+="</select>"+"</div>"+'<div class="col-1 w40">'+'<button id="nextPage" type="button" class="button btn-yellow">下一页</button>'+"</div>"+"</div></div>"
                }
            }
            $("#docPage").html(innerHtml);initPage(dataPage);J.hideMask()
        }

        function initPage(dataPage){
            $("#previousPage").tap(function(){
                if($condition["page"]>1){
                    $condition["page"]=parseInt($condition["page"])-1;
                    J.showMask();
                    $.ajax({
                        url:$requestDoc+"?"+setUrlCondition()+"&getcount=1",
                        success:function(data){
                            readyDoc(data);
                            setLocationUrl();
                            $("#contractDoctors_article").scrollTop(0)
                        }
                    })
                }else{
                    J.showToast("已是第一页","","1000")
                }
            });
            $("#nextPage").tap(function(){
                if($condition["page"]<dataPage){
                    $condition["page"]=parseInt($condition["page"])+1;
                    J.showMask();
                    $.ajax({
                        url:$requestDoc+"?"+setUrlCondition()+"&getcount=1",
                        success:function(data){
                            readyDoc(data);
                            setLocationUrl();
                            $("#contractDoctors_article").scrollTop(0)
                        }
                    })
                }else{
                    J.showToast("已是最后一页","","1000")
                }
            })
        }

        function changePage(){
            $condition["page"]=$("#selectPage").val();
            J.showMask();
            $.ajax({
                url:$requestDoc+"?"+setUrlCondition()+"&getcount=1",
                success:function(data){
                    readyDoc(data);
                    setLocationUrl();
                    $("#contractDoctors_article").scrollTop(0)
                }
            })
        }

        function setUrlCondition(){
            var urlCondition="";
            for($key in $condition){
                if($condition[$key]&&$condition[$key]!==""){
                    urlCondition+="&"+$key+"="+$condition[$key]
                }
            }

            return urlCondition.substring(1)
        }

        function setLocationUrl(){
            var stateObject={};
            var title="";
            var urlCondition="";
            for($key in $condition){
                if($condition[$key]&&$condition[$key]!==""){
                    urlCondition+="&"+$key+"="+$condition[$key]
                }
            }
            urlCondition=urlCondition.substring(1);
            urlCondition="?"+urlCondition;
            var newUrl=$requestViewContractDoctors+urlCondition;history.pushState(stateObject,title,newUrl)
        }
    });
</script>
