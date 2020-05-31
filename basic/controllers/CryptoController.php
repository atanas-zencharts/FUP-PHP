<?php


namespace app\controllers;


use app\models\Cryptocurency;

class CryptoController
{
    public function actionGetAll()
    {
        $crypto = Cryptocurency::find()->asArray()->all();
        return $this->asJson($crypto);
    }
}