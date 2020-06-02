<?php


namespace app\controllers;

use Yii;
use yii\helpers\VarDumper;
use app\models\Company;
use app\models\OrderShare;
use yii\rest\Controller;

class CompanyController extends Controller
{
    public function actionGetAll()
    {
        $company = Company::find()->asArray()->all();
        return $this->asJson($company);
    }

    public function actionPlaceBuyOrder($id, $amount, $price, $userId, $orderId = null)
    {
        $order = OrderShare::findOne($orderId);
        if (!$order) {
            $order = new OrderShare();
            $order->company_id = $id;
            $order->user_id = $userId;
            $order->type = 2;
            $order->status_id = 1;
        }

        if ($order->status_id < 2) {
            $order->quantity_initial = $amount;
        }
        $order->quantity = $amount;
        $order->price = $price;

        if (!$order->save()) {
            Yii::error(VarDumper::dumpAsString([
                   $order->getErrors()
             ]));
            return false;
        } else {
            return true;
        }
    }

    public function actionGetSellOrders($id)
    {
        $sellOrders = OrderShare::find()->andWhere(['type' => 2])->andWhere(['company_id' => $id])->asArray()->all();
        return $this->asJson($sellOrders);
    }
}