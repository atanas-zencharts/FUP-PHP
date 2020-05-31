<?php


namespace app\controllers;


use app\models\Forex;
use yii\rest\Controller;

class ForexController extends Controller
{
    public function actionGetAll()
    {
        $forex = Forex::find()->asArray()->all();
        return $this->asJson($forex);
    }
}