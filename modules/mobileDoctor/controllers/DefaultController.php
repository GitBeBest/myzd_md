<?php

namespace app\modules\mobileDoctor\controllers;

use yii\web\Controller;
use yii\helpers\Url;
/**
 * Default controller for the `mobileDoctor` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(Url::to('home/index'));
    }
}
