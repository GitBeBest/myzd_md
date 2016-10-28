<?php
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $user
 */

$this->title = '个人中心';
$urlPatientCreate = Url::to(['patient/create', 'addBackBtn' => 1]);
$urlPatientList = Url::to(['patient/list', 'addBackBtn' => 1]);
$urlPatientBookingList = Url::to(['patient-booking/list', 'status' => 0, 'addBackBtn' => 1]);
$urlDoctorPatientBookingList = Url::to(['patient-booking/doctor-patient-booking-list', 'addBackBtn' => 1]);
$urlDoctorAccount = Url::to(['doctor/account', 'addBackBtn' => 1]);
$urlDoctorInfo = Url::to(['doctor/doctor-info', 'addBackBtn' => 1]);
$urlDoctorContract = Url::to(['doctor/contract', 'addBackBtn' => 1]);
$urlDrView = Url::to(['doctor/dr-view', 'addBackBtn' => 1]);
$urlDoctorProfile = Url::to(['doctor/profile', 'addBackBtn' => 1]);
$urlDoctorTerms = Url::to(['doctor/doctor-terms', 'addBackBtn' => 1]);
$urlDoctorUploadCert = Url::to(['doctor/upload-cert','addBackBtn' => 1]);
$urlUserBankViewInputKey = Url::to(['user-bank/view-input-key', 'addBackBtn' => 1]);
$urlResImage = $this->theme->baseUrl . "/images/";
$verified = $user->results->userInfo->verified;
$teamDoctor = $user->results->userInfo->teamDoctor;
$isContractDoctor = $user->results->userInfo->isContractDoctor;
$this->params['show_footer'] = true;
?>
<article id="doctorView_article" class="active" data-scroll="true" data-active="center_footer">
    <div class="">
        <div class="bg-green" id="userCenter">
            <div class="pad20 grid">
                <div class="col-0 w77p">
                    <img src="http://static.mingyizhudao.com/146968490433527">
                </div>
                <div class="col-1 color-white pl20 grid" >
                    <div class="col-1">
                        <div class="pt10">
                            您好!<?= $user->results->userInfo->name; ?>
                        </div>
                        <div class="pt10 font-s12">
                            <?php
                            if ($user->results->userInfo->isProfile == '') {
                                echo '<span class="noIcon">未实名认证</span>';
                            } else if ($user->results->userInfo->doctorCerts == '') {
                                echo '<span class="noIcon">未实名认证</span>';
                            } else if ($verified == '') {
                                echo '<span class="waitingIcon">认证中</span>';
                            } else {
                                echo '<span class="realNameIcon">实名认证</span>';
                            }
                            ?>
                        </div>
                    </div>
                    <div  class="col-0 w15p grid middle">
                        <img src="http://static.mingyizhudao.com/146967375501082" class="w8p">
                    </div>
                </div>
            </div>
        </div>
        <ul class="list mt10">
            <li class="nextImg">
                <a id="patientListCheck" class="color-000">
                    <div class="grid font-type">
                        <div class="col-0 w20 myPatients"></div>
                        <div class="col-0 w80">
                            未处理患者
                        </div>
                    </div>
                </a>
            </li>
            <li class="nextImg">
                <a id="getBooking" class="color-000">
                    <div class="grid font-type">
                        <div class="col-0 w20 receivedBooking"></div>
                        <div class="col-0 w80">
                            收到预约
                        </div>
                    </div>
                </a>
            </li>
        </ul>
        <ul class="list mt10">
            <li class="nextImg">
                <div id="userbankViewInputKey" class="grid font-type">
                    <div class="col-0 w20 userbankIcon">
                    </div>
                    <div class="col-0 w80">
                        我的账户
                    </div>
                </div>
            </li>
            <li class="nextImg">
                <div id="checkInf" class="grid font-type">
                    <div class="col-0 w20 consultingAgreement"></div>
                    <div class="col-0 w80">
                        名医主刀顾问协议
                        <?php
                        if ($teamDoctor) {
                            echo '<span class="teamIcon">已签约</span>';
                        }
                        ?>
                    </div>
                </div>
            </li>
            <li class="nextImg">
                <a id="term" class="color-000">
                    <div class="grid font-type">
                        <div class="col-0 w20 signDoctor"></div>
                        <div class="col-0 w80">
                            成为签约专家
                            <?php
                            if ($isContractDoctor) {
                                echo '<span class="signIcon">已同意</span>';
                            }
                            ?>
                        </div>
                    </div>
                </a>
            </li>
        </ul>
        <ul class="list mt10">
            <li id="phoneService" class="nextImg">
                <div class="grid font-type">
                    <div class="col-0 w20 customerService"></div>
                    <div class="col-0 w80">
                        联系客服
                    </div>
                </div>
            </li>
            <li id="phoneService" class="nextImg hide">
                <div class="grid font-type">
                    <div class="col-0 w20 inviteExperts"></div>
                    <div class="col-0 w80">
                        邀请专家
                    </div>
                </div>
            </li>
        </ul>
    </div>
</article>
<script>
    $(document).ready(function () {
        //个人中心
        $('#userCenter').tap(function () {
            location.href = '<?= $urlDoctorAccount; ?>';
        });

        //若医生已认证，但未签署协议，则不能创建患者
        $("#createCheck").tap(function (e) {
            e.preventDefault();
            var verified = '<?= $verified; ?>';
            var teamDoctor = '<?= $teamDoctor ?>';
            if (verified) {
                if (!teamDoctor) {
                    J.hideMask();
                    J.customConfirm('您已实名认证',
                        '<div class="mt10 mb10">尚未签署《医生顾问协议》</div>',
                        '<a id="closeLogout" class="w50">暂不</a>',
                        '<a id="signAgreement" class="color-green w50">签署协议</a>',
                        function () {
                        },
                        function () {
                        });
                    $('#closeLogout').tap(function () {
                        J.closePopup();
                    });
                    $('#signAgreement').tap(function () {
                        location.href = '<?= $urlDoctorTerms; ?>';
                    });
                } else {
                    location.href = "<?= $urlPatientCreate; ?>";
                }
            } else {
                location.href = "<?= $urlPatientCreate; ?>";
            }
        });

        $("#patientListCheck").tap(function (e) {
            e.preventDefault();
            var verified = '<?= $verified; ?>';
            var teamDoctor = '<?= $teamDoctor ?>';
            if (verified) {
                if (!teamDoctor) {
                    J.hideMask();
                    J.customConfirm('您已实名认证',
                        '<div class="mt10 mb10">尚未签署《医生顾问协议》</div>',
                        '<a id="closeLogout" class="w50">暂不</a>',
                        '<a id="signAgreement" class="color-green w50">签署协议</a>',
                        function () {
                        },
                        function () {
                        });
                    $('#closeLogout').tap(function () {
                        J.closePopup();
                    });
                    $('#signAgreement').tap(function () {
                        location.href = '<?= $urlDoctorTerms; ?>';
                    });
                } else {
                    location.href = "<?= $urlPatientList; ?>";
                }
            } else {
                location.href = "<?= $urlPatientList; ?>";
            }
        });

        $("#patientBookingListCheck").tap(function (e) {
            e.preventDefault();
            var verified = '<?= $verified; ?>';
            var teamDoctor = '<?= $teamDoctor ?>';
            if (verified) {
                if (!teamDoctor) {
                    J.hideMask();
                    J.customConfirm('您已实名认证',
                        '<div class="mt10 mb10">尚未签署《医生顾问协议》</div>',
                        '<a id="closeLogout" class="w50">暂不</a>',
                        '<a id="signAgreement" class="color-green w50">签署协议</a>',
                        function () {
                        },
                        function () {
                        });
                    $('#closeLogout').tap(function () {
                        J.closePopup();
                    });
                    $('#signAgreement').tap(function () {
                        location.href = '<?= $urlDoctorTerms; ?>';
                    });
                } else {
                    location.href = "<?= $urlPatientBookingList; ?>";
                }
            } else {
                location.href = "<?= $urlPatientBookingList; ?>";
            }
        });

        $('#getBooking').tap(function () {
            location.href = '<?= $urlDoctorPatientBookingList; ?>';
        });

        $('#term').tap(function () {
            location.href = '<?= $urlDrView; ?>';
        });

        $('#userbankViewInputKey').tap(function (e) {
            e.preventDefault();
            if ('<?= $user->results->userInfo->isProfile; ?>' == '') {
                J.hideMask();
                J.customConfirm('',
                    '<div class="mt10 mb10">您尚未完善个人信息</div>',
                    '<a id="closeLogout" class="w50">暂不</a>',
                    '<a id="doctorProfile" class="color-green w50">完善信息</a>',
                    function () {
                    },
                    function () {
                    });
                $('#closeLogout').tap(function () {
                    J.closePopup();
                });
                $('#doctorProfile').tap(function () {
                    location.href = '<?= $urlDoctorProfile; ?>';
                });
            } else {
                location.href = '<?= $urlUserBankViewInputKey; ?>';
            }
        });

        //医生顾问协议
        $('#checkInf').tap(function (e) {
            e.preventDefault();
            if ('<?= $user->results->userInfo->isProfile; ?>' == '') {
                J.hideMask();
                J.customConfirm('',
                    '<div class="mt10 mb10">您尚未完善个人信息</div>',
                    '<a id="closeLogout" class="w50">暂不</a>',
                    '<a id="doctorProfile" class="color-green w50">完善信息</a>',
                    function () {
                    },
                    function () {
                    });
                $('#closeLogout').tap(function () {
                    J.closePopup();
                });
                $('#doctorProfile').tap(function () {
                    location.href = '<?= $urlDoctorProfile; ?>';
                });
            } else if ('<?= $user->results->userInfo->doctorCerts; ?>' == '') {
                J.hideMask();
                J.customConfirm('',
                    '<div class="mt10 mb10">您尚未上传实名认证证件</div>',
                    '<a id="closeLogout" class="w50">暂不</a>',
                    '<a id="uploadFile" class="color-green w50">上传证件</a>',
                    function () {
                    },
                    function () {
                    });
                $('#closeLogout').tap(function () {
                    J.closePopup();
                });
                $('#uploadFile').tap(function () {
                    location.href = '<?= $urlDoctorUploadCert; ?>';
                });
            } else if ('<?= $verified; ?>' == '') {
                J.hideMask();
                J.showToast('请您等待实名认证通过', '', '1500');
            } else {
                location.href = '<?= $urlDoctorTerms; ?>';
            }
        });

        $('#phoneService').tap(function () {
            location.href = 'tel://4006277120';
        });
    });
</script>