<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\util\StatCode;
/**
 * @var $this View
 * @var $teamDoctor
 */

$this->title = '我的订单';
$status = \Yii::$app->request->get('status', 0);
$BK_STATUS_SERVICE_PAID = StatCode::BK_STATUS_SERVICE_PAID;
$currentUrl = \Yii::$app->request->getUrl();
$urlDoctorTerms = Yii::$app->urlManager->createAbsoluteUrl('doctor/doctor-terms');
$urlPatientBookingList = Url::to(['patient-booking/list', 'addBackBtn' => 1, 'status' => '']);
$urlDoctorTerms.='?returnUrl=' . $currentUrl;
$urlDoctorView = Url::to('doctor/view');
$urlPatientBookingAjaxList = Url::to(['patient-booking/ajax-list', 'status' => $status]);
$urlPatientBookingAjaxCancel = Url::to(['patient-booking/ajax-cancel', 'id' => '']);
$urlPatientBookingSearchView = Url::to(['patient-booking/search-view', 'addBackBtn' => 1]);
$urlResImage = $this->theme->baseUrl . "/images/";
$checkTeamDoctor = $teamDoctor;
?>
<header id="bookingList_header" class="list_header bg-green">
    <div class="grid w100">
        <div class="col-0 pl5 pr10">
            <a href="<?= $urlDoctorView; ?>" data-target="link">
                <div class="pl5">
                    <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
                </div>
            </a>
        </div>
        <div class="col-1 pt7 pb7">
            <div class="searchInput">您可以输入患者姓名，医生姓名</div>
        </div>
        <div id="switchSummary" class="col-0 pl5 pr5">
            筛选
        </div>
    </div>
</header>
<nav id="bookingList_nav" class="header-secondary bg-white color-black3 font-s16">
    <ul class="control-group w100">
        <?php
        $statusActive = '';
        if ($status == 0) {
            $statusActive = 'active';
        }
        ?>
        <li class="<?= $statusActive; ?>">
            <a href="<?= $urlPatientBookingList; ?>/0" id="zhuanti" data-target="link">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 statusLine">全部</div>
                    <div class="col-1"></div>
                </div>
            </a>
        </li>
        <?php
        $statusActive = '';
        if ($status == 1) {
            $statusActive = 'active';
        }
        ?>
        <li class="<?= $statusActive; ?>">
            <a href="<?= $urlPatientBookingList; ?>/1" id="story" data-target="link">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 statusLine">待支付</div>
                    <div class="col-1"></div>
                </div>
            </a>
        </li>
        <?php
        $statusActive = '';
        if ($status == 2) {
            $statusActive = 'active';
        }
        ?>
        <li class="<?= $statusActive; ?>">
            <a href="<?= $urlPatientBookingList; ?>/2" id="story" data-target="link">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 statusLine">安排中</div>
                    <div class="col-1"></div>
                </div>
            </a>
        </li>
        <?php
        $statusActive = '';
        if ($status == 5) {
            $statusActive = 'active';
        }
        ?>
        <li class="<?= $statusActive; ?>">
            <a href="<?= $urlPatientBookingList; ?>/5" id="story" data-target="link">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 statusLine">待确认</div>
                    <div class="col-1"></div>
                </div>
            </a>
        </li>
        <?php
        $statusActive = '';
        if ($status == 6) {
            $statusActive = 'active';
        }
        ?>
        <li class="<?= $statusActive; ?>">
            <a href="<?= $urlPatientBookingList; ?>/6" id="story" data-target="link">
                <div class="grid">
                    <div class="col-1"></div>
                    <div class="col-0 statusLine">待完成</div>
                    <div class="col-1"></div>
                </div>
            </a>
        </li>
    </ul>
</nav>
<article id="bookingList_article" class="active list_article bg-gray3" data-scroll="true" data-active="order_footer">
    <div>

    </div>
</article>
<script>
    $(document).ready(function () {
        J.showMask();
        $.ajax({
            url: '<?= $urlPatientBookingAjaxList; ?>',
            success: function (data) {
                var structureData = structure_data(data);
                var returnData = do_decrypt(structureData, privkey);
                returnData = analysis_data(returnData);
                readyPage(returnData, 2);
                $('#switchSummary').on('tap', function () {
                    var dataSummary = $(this).attr('data-summary');
                    var topPopup = '<header id="bookingList_header" class="list_header bg-green">' +
                            '<div class="grid w100">' +
                            '<div class="col-0 pl5 pr10">' +
                            '<a href="<?= $urlDoctorView; ?>" data-target="link">' +
                            '<div class="pl5">' +
                            '<img src="http://static.mingyizhudao.com/146968435878253" class="w11p">' +
                            '</div>' +
                            '</a>' +
                            '</div>' +
                            '<div class="col-1 pt7 pb7">' +
                            '<div class="searchInput">您可以输入患者姓名，医生姓名</div>' +
                            '</div>' +
                            '<div class="col-0 pl5 pr5" data-target="closePopup">' +
                            '筛选' +
                            '</div>' +
                            '</div>' +
                            '</header>' +
                            '<article class="active list_article" data-scroll="true" style="height:111px;">' +
                            '<ul class="list">';
                    if (dataSummary == 1) {
                        topPopup += '<li class="summary activeIcon" data-summary="1">已传小结</li>';
                    } else {
                        topPopup += '<li class="summary" data-summary="1">已传小结</li>';
                    }
                    if (dataSummary == 0) {
                        topPopup += '<li class="summary activeIcon" data-summary="0">未传小结</li>';
                    } else {
                        topPopup += '<li class="summary" data-summary="0">未传小结</li>';
                    }
                    topPopup += '</ul>' +
                            '</article>';
                    J.popup({
                        html: topPopup,
                        pos: 'top',
                        showCloseBtn: false
                    });
                    $('.summary').click(function () {
                        var summary = $(this).attr('data-summary');
                        readyPage(returnData, summary);
                        $('#switchSummary').attr('data-summary', summary);
                        J.closePopup();
                    });
                });
            }
        });

        $('.searchInput').click(function () {
            location.href = '<?= $urlPatientBookingSearchView; ?>';
        });

        function readyPage(returnData, switchSummary) {
            var innerHtml = '<div class="mb10">';
            var bookingList = returnData.results.bookingList;
            if (bookingList.length > 0) {
                //用于控制筛选无订单
                var switchJudge = false;
                for (var i = 0; i < bookingList.length; i++) {
                    var booking = bookingList[i];
                    if (((switchSummary == 0) && (booking.hasFile == 0)) || ((switchSummary == 1) && (booking.hasFile == 1)) || (switchSummary == 2)) {
                        switchJudge = true;
                        innerHtml += '<div class="mt10 ml5 mr5 bg-white br5">' +
                                '<a href="<?= $orderView = Url::to('order/orderView'); ?>/bookingid/' + booking.id + '/status/' + booking.status + '/addBackBtn/1" data-target="link">' +
                                '<div class="pad10 bb-gray">';
                        if (booking.status == 9) {
                        } else if (booking.hasFile == 0) {
                            innerHtml += '<span class="color-blue6 mr5">未传小结</span>';
                        } else {
                            innerHtml += '<span class="color-blue6 mr5">已传小结</span>';
                        }
                        if ((booking.status == 1) || (booking.status == 2) || (booking.status == 5) || (booking.status == 6)) {
                            innerHtml += '<span class="color-yellow3">' + booking.statusText + '</span>';
                        } else {
                            innerHtml += '<span class="color-green3">' + booking.statusText + '</span>';
                        }
                        innerHtml += '</div>' +
                                '<div class="pad10 bb-gray">' +
                                '<div class="grid">' +
                                '<div class="col-0">就诊医生:</div>' +
                                '<div class="col-1 pl5">' + booking.doctorName + '</div>' +
                                '</div>' +
                                '<div class="grid mt10">' +
                                '<div class="col-0">就诊医院:</div>' +
                                '<div class="col-1 pl5">' + booking.hospital + '</div>' +
                                '</div>' +
                                '<div class="grid mt10">' +
                                '<div class="col-0">患者姓名:</div>' +
                                '<div class="col-1 pl5">' + booking.patientName + '</div>' +
                                '</div>' +
                                '</div>' +
                                '</a>';
                        if ((booking.status == 1) || (booking.status == 2)) {
                            innerHtml += '<div class="grid pl10 pr10 font-s12">' +
                                    '<div class="col-1 pt8">' +
                                    '预约单号:' + booking.refNo +
                                    '</div>' +
                                    '<div class="col-0 pt5 pb5">' +
                                    '<div class="cancelIcon" data-id="' + booking.id + '">' +
                                    '取消订单' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>';
                        } else {
                            innerHtml += '<div class="pad10 font-s12">' +
                                    '预约单号:' + booking.refNo +
                                    '</div>' +
                                    '</div>';
                        }
                    }
                }
                if (!switchJudge) {
                    innerHtml += '<div class="mt50 text-center">' +
                            '<img src="http://static.mingyizhudao.com/146295490734874" class="w170p">' +
                            '</div>' +
                            '<div class="text-center font-s24 color-gray9">' +
                            '暂无预约信息' +
                            '</div>';
                }
            } else {
                innerHtml += '<div class="mt50 text-center">' +
                        '<img src="http://static.mingyizhudao.com/146295490734874" class="w170p">' +
                        '</div>' +
                        '<div class="text-center font-s24 color-gray9">' +
                        '暂无预约信息' +
                        '</div>';
            }
            innerHtml += '</div>';
            $('#bookingList_article').html(innerHtml);
            J.hideMask();
            $('.cancelIcon').click(function () {
                var id = $(this).attr('data-id');
                J.customConfirm('',
                        '<div class="mt10 mb10">取消订单</div>',
                        '<a id="closePopup" class="w50">取消</a>',
                        '<a id="cancelOrder" class="color-green w50">确认</a>',
                        function () {
                        },
                        function () {
                        });
                $('#closePopup').click(function () {
                    J.hideMask();
                });
                $('#cancelOrder').click(function () {
                    J.hideMask();
                    J.showMask();
                    $.ajax({
                        url: '<?= $urlPatientBookingAjaxCancel; ?>/' + id,
                        success: function (data) {
                            J.hideMask();
                            if (data.status == 'ok') {
                                J.showToast('操作成功', '', '1500');
                                location.reload();
                            } else {
                                J.showToast(data.errors, '', '1500');
                            }
                        },
                        error: function (XmlHttpRequest, textStatus, errorThrown) {
                            J.hideMask();
                            J.showToast('网络错误，请稍后再试', '', '1500');
                            console.log(XmlHttpRequest);
                            console.log(textStatus);
                            console.log(errorThrown);
                        },
                    });
                });
            });
        }

        $('#bookingList_article').scroll(function () {
            if ($(this).scrollTop() > 0) {
                $('#bookingList_nav').addClass('bb-gray');
            } else {
                $('#bookingList_nav').removeClass('bb-gray');
            }
        });
        if ('<?= $checkTeamDoctor; ?>' == 1) {
            J.customConfirm('您已实名认证',
                    '<div class="mt10 mb10">尚未签署《医生顾问协议》</div>',
                    '<a data="cancel" class="w50">暂不</a>',
                    '<a data="ok" class="color-green w50">签署协议</a>',
                    function () {
                        location.href = "<?= $urlDoctorTerms; ?>";
                    },
                    function () {
                        location.href = "<?= $urlDoctorView; ?>";
                    });
        }
    });
</script>