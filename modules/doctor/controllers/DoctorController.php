<?php
namespace app\modules\doctor\controllers;

use yii\web\Controller;

class DoctorController extends WebsiteController {

    //基本信息页面
    public function actionBasicView($id = null) {
        $form = new BasicInfoForm();
        if (strIsEmpty($id) == false) {
            $doctor = NewDoctor::model()->getById($id);
            $form->initModel($doctor);
        }
        $this->render('basicView', array('model' => $form));
    }

    //获取医生头像七牛上传权限
    public function actionAjaxDrToken() {
        $url = 'http://121.40.127.64:8089/api/tokendrcert';
        $data = $this->send_get($url);
        $output = array('uptoken' => $data['results']['uploadToken']);
        $this->renderJsonOutput($output);
    }

    public function actionAjaxBasic() {
        $output = array('status' => 'no');
        if (isset($_POST['BasicInfoForm'])) {
            $values = $_POST['BasicInfoForm'];
            $manager = new Manager();
            $output = $manager->saveBasic($values);
        } else {
            $output['errors'] = 'miss data...';
        }
        $this->renderJsonOutput($output);
    }

    //医生信息页面
    public function actionDoctorView($id) {
        $form = new IndustrialInfoForm();
        $doctor = NewDoctor::model()->getById($id);
        $form->initModel($doctor);
        $this->render('doctorView', array('model' => $form));
    }

    //第二个页面的信息保存
    public function actionAjaxDoctor() {
        $output = array('status' => 'no');
        if (isset($_POST['DoctorForm'])) {
            $values = $_POST['DoctorForm'];
            $form = new IndustrialInfoForm();
            $form->setAttributes($values, true);
            if ($form->validate()) {
                $model = NewDoctor::model()->getById($form->id);
                $attributes = $form->getSafeAttributes();
                $model->setAttributes($attributes, true);
                if ($model->update(array('hospital_id', 'hospital_name', 'category_id', 'cat_name', 'clinic_title', 'academic_title'))) {
                    $output['status'] = 'ok';
                    $output['id'] = $model->getId();
                } else {
                    $output['errors'] = $model->getErrors();
                }
            } else {
                $output['errors'] = $form->getErrors();
            }
        } else {
            $output['errors'] = 'miss data...';
        }
        $this->renderJsonOutput($output);
    }

    //查询医院
    public function actionAjaxSearchHospital() {
        $input = $_GET;
        $apiview = new ApiViewHospitalSearch($input);
        $output = $apiview->loadApiViewData();
        $this->renderJsonOutput($output);
    }

    //进入专业页面
    public function actionMajorView($id) {
        $form = new MajorForm();
        $form->id = $id;
        $this->render('majoyView', array('model' => $form));
    }

    //异步加载亚专业
    public function actionAjaxSubCat($id) {
        $doctor = NewDoctor::model()->getById($id);
        $api = new ApiViewSubCat($doctor->category_id);
        $output = $api->loadApiViewData();
        $this->renderJsonOutput($output);
    }

    //异步查询亚专业
    public function actionAjaxSearchSub($id, $name) {
        $api = new ApiViewSubSearch($id, $name);
        $output = $api->loadApiViewData();
        $this->renderJsonOutput($output);
    }

    //异步加载手术
    public function actionAjaxSurgery($id) {
        $doctor = NewDoctor::model()->getById($id);
        $api = new ApiViewSurgery($doctor->category_id);
        $output = $api->loadApiViewData();
        $this->renderJsonOutput($output);
    }

    //异步查询手术
    public function actionAjaxSearchSurgery($id, $name) {
        $api = new ApiViewSurgerySearch($id, $name);
        $output = $api->loadApiViewData();
        $this->renderJsonOutput($output);
    }

    //保存专业数据
    public function actionAjaxMajor($id) {
        $output = array('status' => 'no');
        if (isset($_POST['MajorForm'])) {
            $values = $_POST['MajorForm'];
            $manager = new Manager();
            $output = $manager->saveMajor($values);
        } else {
            $output['errors'] = 'miss data...';
        }
        $this->renderJsonOutput($output);
    }
    
    public function actionSuccess() {
        $this->render('success');
    }

}
