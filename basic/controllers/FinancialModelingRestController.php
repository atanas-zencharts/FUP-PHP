<?php


namespace app\controllers;

use Yii;
use yii\helpers\VarDumper;
use app\service\FinancialModelingPrepApi;
use yii\httpclient\Client;
use yii\rest\Controller;

class FinancialModelingRestController extends Controller
{

    public function actionGetCrypto()
    {
        $fm = new FinancialModelingPrepApi();
        $fm->getCrypto();
    }

    public function actionGetMajorIndex()
    {
        $fm = new FinancialModelingPrepApi();
        $fm->getMajorIndex();
    }

    public function actionGetForex()
    {
        $fm = new FinancialModelingPrepApi();
        $fm->getForex();
    }

    public function actionGetCompany()
    {
        $fm = new FinancialModelingPrepApi();
        $fm->getCompany();
    }
}