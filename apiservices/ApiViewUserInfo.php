<?php
namespace app\apiServices;

use app\models\user\User;
use app\models\UserManager;

class ApiViewUserInfo extends EApiViewService {

    private $user;
    private $userMgr;

    /***
     * ApiViewUserInfo constructor.
     * @param User $user
     */
    public function __construct(User $user) {
        parent::__construct();
        $this->user = $user;
        $this->userMgr = new UserManager();
    }

    protected function createOutput() {
        $this->output = array(
            'status' => self::RESPONSE_OK,
            'errorCode' => 0,
            'errorMsg' => 'success',
            'results' => $this->results,
        );
    }

    protected function loadData() {
        $this->loadUserInfo();
    }

    public function loadUserInfo() {
        $profile = $this->user->getUserDoctorProfile();
        $models = $this->userMgr->loadUserDoctorFilesByUserId($this->user->id);
        $doctorCerts = false;
        if (arrayNotEmpty($models)) {
            $doctorCerts = true;
        }
        $data = new \stdClass();
        $data->hasKey = strIsEmpty($this->user->user_key) ? false : true;
        $data->doctorCerts = $doctorCerts;
        if (isset($profile)) {
            $data->isProfile = true;
            $data->name = $profile->getName();
            //是否是签约医生
            $data->verified = $profile->isVerified();
            $data->teamDoctor = $profile->isTermsDoctor();
            $data->isCommonweal = $profile->isCommonweal();
            $data->isContractDoctor = $profile->isContractDoctor();
        } else {
            $data->isProfile = false;
            $data->name = $this->user->getMobile();
            $data->verified = false;
            $data->teamDoctor = false;
            $data->isCommonweal = false;
            $data->isContractDoctor = false;
        }
        $this->results->userInfo = $data;
    }

}
