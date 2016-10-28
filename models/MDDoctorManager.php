<?php
namespace app\models;

use app\models\base\ErrorList;
use app\models\doctor\DoctorHuizhenForm;
use app\models\doctor\DoctorZhuanzhenForm;
use app\models\doctor\UserDoctorHuizhen;
use app\models\doctor\UserDoctorProfile;
use app\models\doctor\UserDoctorZhuanzhen;

class MDDoctorManager {

    /**
     * 会诊选择不参与
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function disJoinHuizhen($userId) {
        $model = $this->loadUserDoctorHuizhenByUserId($userId);
        $output = ['status' => 'ok'];
        if (isset($model)) {
            $model->is_join = UserDoctorHuizhen::IS_NOT_JOIN;
            if ($model->update(array('is_join')) === false) {
                $output['status'] = 'no';
                $output['errors'] = $model->getErrors();
            } else {
                $output['status'] = 'ok';
                $output['zzId'] = $model->getId();
            }
        }
        return $output;
    }

    /**
     * 转诊选择不参与
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function disJoinZhuanzhen($userId) {
        $output = array('status' => 'no');
        $model = $this->loadUserDoctorZhuanzhenByUserId($userId);
        if (isset($model)) {
            $model->is_join = UserDoctorZhuanzhen::IS_NOT_JOIN;
            if ($model->update(array('is_join')) == false) {
                $output['status'] = 'no';
                $output['errors'] = $model->getErrors();
            } else {
                $output['status'] = 'ok';
                $output['hzId'] = $model->getId();
            }
        } else {
            $output['errors'] = 'no data...';
        }
        return $output;
    }

    /**
     * 根据医生id查询其填写的会诊信息
     * @param $userId
     * @param null $with
     * @return UserDoctorHuizhen
     */
    public function loadUserDoctorHuizhenByUserId($userId, $with = null) {
        return (new UserDoctorHuizhen())->getByAttributes(array('user_id' => $userId), $with);
    }

    /**
     * 根据id查询会诊信息
     * @param $id
     * @return null|UserDoctorHuizhen
     */
    public function loadUserDoctorHuizhenById($id) {
        return (new UserDoctorHuizhen())->getById($id);
    }

    /**
     * 根据医生id查询其填写的转诊信息
     * @param $userId
     * @param null $with
     * @return UserDoctorZhuanzhen|null
     */
    public function loadUserDoctorZhuanzhenByUserId($userId, $with = null) {
        return (new UserDoctorZhuanzhen())->getByAttributes(array('user_id' => $userId), $with);
    }

    /**
     * 根据id查询转诊信息
     * @param $id
     * @return array|null|UserDoctorZhuanzhen
     */
    public function loadUserDoctorZhuanzhenById($id) {
        return (new UserDoctorZhuanzhen())->getById($id);
    }

    /**
     * 保存或者修改医生会诊信息
     * @param $values
     * @return array
     */
    public function createOrUpdateDoctorHuizhen($values) {
        $output = array('status' => 'no');
        $userId = $values['user_id'];
        $form = new DoctorHuizhenForm();
        $form->setAttributes($values, true);
        if ($form->validate() === false) {
            $output['status'] = 'no';
            $output['errors'] = $form->getErrors();
            return $output;
        }
        $doctorHz = new UserDoctorHuizhen();
        $model = $this->loadUserDoctorHuizhenByUserId($userId);
        if (isset($model)) {
            $doctorHz = $model;
        }
        $attributes = $form->getSafeAttributes();
        $doctorHz->setAttributes($attributes, true);
        if ($doctorHz->save() === false) {
            $output['status'] = 'no';
            $output['errors'] = $doctorHz->getErrors();
        } else {
            $output['status'] = 'ok';
            $output['hzId'] = $doctorHz->getId();
        }
        return $output;
    }

    /**
     * @param $values
     * @return array
     */
    public function createOrUpdateDoctorZhuanzhen($values) {
        $output = array('status' => 'no');
        $userId = $values['user_id'];
        $form = new DoctorZhuanzhenForm();
        $form->setAttributes($values, true);
        if ($form->validate() === false) {
            $output['status'] = 'no';
            $output['errors'] = $form->getErrors();
            return $output;
        }
        $doctorZz = new UserDoctorZhuanzhen();
        $model = $this->loadUserDoctorZhuanzhenByUserId($userId);
        if (isset($model)) {
            $doctorZz = $model;
        }
        $attributes = $form->getSafeAttributes();
        $doctorZz->setAttributes($attributes, true);
        if ($doctorZz->save() === false) {
            $output['status'] = 'no';
            $output['errors'] = $doctorZz->getErrors();
        } else {
            $output['status'] = 'ok';
            $output['zzId'] = $doctorZz->getId();
        }
        return $output;
    }

    /**
     * 专家签约
     * @param UserDoctorProfile $model
     * @return bool|int
     * @throws \Exception
     */
    public function doctorContract(UserDoctorProfile $model) {
        $result = false;
        if (isset($model)) {
            $result = 2;
            if (is_null($model->date_contracted)) {
                $model->date_contracted = date('Y-m-d H:i:s');
                $result = $model->update('date_contracted');
            }
        }
        
        return $result;
    }

    /*     * ***************************************app所用方法******************************************************************** */

    /**
     * 会诊选择不参与
     * @param $userId
     * @return array
     */
    public function apiDisJoinHuizhen($userId) {
        $output = array('status' => 'no', 'errorCode' => ErrorList::NOT_FOUND);
        $model = $this->loadUserDoctorHuizhenByUserId($userId);
        if (isset($model)) {
            $model->is_join = UserDoctorHuizhen::IS_NOT_JOIN;
            if ($model->update(array('is_join'))) {
                $output['status'] = 'ok';
                $output['errorCode'] = ErrorList::ERROR_NONE;
                $output['errorMsg'] = 'success';
            } else {
                $output['errorMsg'] = $model->getFirstErrors();
            }
        } else {
            $output['errorMsg'] = '暂未填写会诊信息!';
        }
        return $output;
    }

    /***
     * 转诊选择不参与
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function apiDisJoinZhuanzhen($userId) {
        $output = array('status' => 'no', 'errorCode' => ErrorList::NOT_FOUND);
        $model = $this->loadUserDoctorZhuanzhenByUserId($userId);
        if (isset($model)) {
            $model->is_join = UserDoctorZhuanzhen::IS_NOT_JOIN;
            if ($model->update(array('is_join'))) {
                $output['status'] = 'ok';
                $output['errorCode'] = ErrorList::ERROR_NONE;
                $output['errorMsg'] = 'success';
            } else {
                $output['errorMsg'] = $model->getFirstErrors();
            }
        } else {
            $output['errorMsg'] = '暂未填写转诊信息!';
        }
        return $output;
    }

    //保存或者修改医生会诊信息
    public function apiCreateOrUpdateDoctorHuizhen($values) {
        $output = array('status' => 'no', 'errorCode' => ErrorList::NOT_FOUND);
        $userId = $values['user_id'];
        $form = new DoctorHuizhenForm();
        $form->setAttributes($values, true);
        if ($form->validate() === false) {
            $output['errorMsg'] = $form->getFirstErrors();
            return $output;
        }
        $doctorHz = new UserDoctorHuizhen();
        $model = $this->loadUserDoctorHuizhenByUserId($userId);
        if (isset($model)) {
            $doctorHz = $model;
        }
        $attributes = $form->getSafeAttributes();
        $doctorHz->setAttributes($attributes, true);
        if ($doctorHz->save()) {
            $output['status'] = 'ok';
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = 'success';
        } else {
            $output['errorMsg'] = $doctorHz->getFirstErrors();
        }
        return $output;
    }

    public function apiCreateOrUpdateDoctorZhuanzhen($values) {
        $output = array('status' => 'no', 'errorCode' => ErrorList::NOT_FOUND);
        $userId = $values['user_id'];
        $form = new DoctorZhuanzhenForm();
        $form->setAttributes($values, true);
        if ($form->validate() === false) {
            $output['errorMsg'] = $form->getFirstErrors();
            return $output;
        }
        $doctorZz = new UserDoctorZhuanzhen();
        $model = $this->loadUserDoctorZhuanzhenByUserId($userId);
        if (isset($model)) {
            $doctorZz = $model;
        }
        $attributes = $form->getSafeAttributes();
        $doctorZz->setAttributes($attributes, true);
        if ($doctorZz->save()) {
            $output['status'] = 'ok';
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = 'success';
        } else {
            $output['errorMsg'] = $doctorZz->getFirstErrors();
        }
        return $output;
    }

}
