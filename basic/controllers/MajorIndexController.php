<?php


namespace app\controllers;


use yii\rest\Controller;
use app\models\MajorIndex;

class MajorIndexController extends Controller
{
    public function actionGetAll()
    {
        $majorIndex = MajorIndex::find()->asArray()->all();
        return $this->asJson($majorIndex);
    }
}