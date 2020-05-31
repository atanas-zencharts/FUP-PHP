<?php


namespace app\controllers;


use app\models\Cryptocurency;
use yii\rest\Controller;

class CryptoController extends Controller
{
    public function actionGetAll()
    {
        $crypto = Cryptocurency::find()->asArray()->all();
        return $this->asJson($crypto);
    }
}