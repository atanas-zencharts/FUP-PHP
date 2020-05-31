<?php


namespace app\controllers;


use app\models\Company;
use yii\rest\Controller;

class CompanyController extends Controller
{
    public function actionGetAll()
    {
        $company = Company::find()->asArray()->all();
        return $this->asJson($company);
    }
}