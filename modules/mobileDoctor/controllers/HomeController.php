<?php
namespace app\modules\mobileDoctor\controllers;

use yii\web\ViewAction;
use yii\widgets\ActiveForm;

class HomeController extends MobiledoctorController {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'yii\web\ViewAction',
            ),
        );
    }

    /**
     * 默认
     */
    public function actionIndex() {
        $this->redirect(\Yii::$app->user->loginUrl);
    }

    public function actionSetBrowser($browser) {
        if ($browser == 'pc') {
            $this->setBrowserInSession($browser);
            $this->redirect(Yii::app()->params['baseUrl'] . '/site/index?browser=pc');
        } else {
            $this->setBrowserInSession($browser);
            $this->redirect($this->getHomeUrl());
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        //$this->redirect(array('index'));
        if ($error = \Yii::$app->errorHandler->errorAction) {
            if (\Yii::$app->request->isAjax)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax'])) {
            echo ActiveForm::validate($model);
            \Yii::$app->end();
        }
    }

}
