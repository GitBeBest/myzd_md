<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this View
 */
$this->title = '订单搜索';
$ajaxSearch = Url::to(['ajax-search', 'name' => '']);
$urlPatientBookingAjaxCancel = Url::to(['ajax-cancel', 'id' => '']);
$searchValue = \Yii::$app->request->get('searchValue', '');
$urlSearchView = Url::to('search-view');
$urlResImage = $this->theme->baseUrl . "/images/";
$this->params['show_footer'] = false;
?>
<header id="searchListView_header" class="bg-green search">
    <div class="grid w100">
        <div class="col-0 pl5 pr10">
            <a href="" data-target="back">
                <div class="pl5">
                    <img src="http://static.mingyizhudao.com/146968435878253" class="w11p">
                </div>
            </a>
        </div>
        <div class="col-1">
            <i class="icon_search"></i>
            <input class="icon_input" type="text" placeholder="您可以输入患者姓名，医生姓名">
            <a class="icon_clear hide"></a>
        </div>
        <div id="searchBtn" class="col-0 pl5 pr5">
            搜索
        </div>
    </div>
</header>
<article id="searchListView_article" class="active list_article bg-gray3" data-scroll="true">
    <div>

    </div>
</article>
<script>
    $(document).ready(function () {
        $("#searchListView_header").on("input", function () {
            var searchValue = $(this).val();
            if (searchValue != '') {
                $('.icon_clear').removeClass('hide');
            } else {
                $('.icon_clear').addClass('hide');
            }
        });

        //清空input
        $('.icon_clear').click(function () {
            $('.icon_input').val('');
            $(this).addClass('hide');
        });

        $('#searchBtn').click(function () {
            var searchValue = $('input[type="text"]').val();
            if (searchValue == '') {
                J.showToast('请先输入条件', '', '1500');
            } else {
                J.showMask();
                ajaxSearch(searchValue);
            }
        });

        function ajaxSearch(searchValue) {
            $.ajax({
                url: '<?= $ajaxSearch; ?>' + searchValue,
                success: function (data) {
                    var structureData = structure_data(data);
                    var returnData = do_decrypt(structureData, privkey);
                    returnData = analysis_data(returnData);
                    readyPage(returnData, searchValue);
                },
                error: function (XmlHttpRequest, textStatus, errorThrown) {
                    J.hideMask();
                    console.log(XmlHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                },
            });
        }

        function readyPage(returnData, searchValue) {
            var innerHtml = '<div class="mb10">';
            var bookingList = returnData.results.bookingList;
            if (bookingList.length > 0) {
                for (var i = 0; i < bookingList.length; i++) {
                    var booking = bookingList[i];
                    innerHtml += '<div class="mt10 ml5 mr5 bg-white br5">' +
                            '<a href="<?= $orderView = Url::toRoute('order/order-view'); ?>?booking_id=' + booking.id + '&status=' + booking.status + '&addBackBtn=1" data-target="link">' +
                            '<div class="pad10 bb-gray">';
                    if (booking.status == 9) {
                    } else if (booking.hasFile == 0) {
                        innerHtml += '<span class="color-blue6 mr5">未传小结</span>';
                    } else {
                        innerHtml += '<span class="color-blue6 mr5">已传小结</span>';
                    }
                    if ((booking.status == 1) || (booking.status == 2) || (booking.status == 5) || (booking.status == 11)) {
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
                        innerHtml +=
                                '<div class="grid pl10 pr10 font-s12">' +
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
            } else {
                innerHtml += '<div class="mt50 text-center">' +
                        '<img src="http://static.mingyizhudao.com/146295490734874" class="w170p">' +
                        '</div>' +
                        '<div class="text-center font-s24 color-gray9">' +
                        '暂无搜索结果' +
                        '</div>';
            }
            innerHtml += '</div>';
            $('article').html(innerHtml);
            J.hideMask();
            $('.cancelIcon').click(function () {
                var id = $(this).attr('data-id');
                J.customConfirm('',
                        '<div class="mt10 mb10">取消订单</div>',
                        '<a id="closePopup" class="w50">取消</a>',
                        '<a id="cancelOrder" class="color-green w50">确认</a>', function () {
                        }, function () {
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
    });
</script>