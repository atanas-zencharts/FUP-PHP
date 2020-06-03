<?php


namespace app\controllers;

use app\components\AutoBuySellHelper;
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
            $order->type = 1;
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
            return $this->asJson(['message' => 'The order could not be saved. Please contact support', 'error' => true]);
        } else {
            $autoBuy = new AutoBuySellHelper();
            $result = $autoBuy->autoBuy($order);

            Yii::error(VarDumper::dumpAsString([
                 'result' => $result
             ]));

            return $this->asJson(['boughtQuantity' => $autoBuy->boughtQuantity, 'totalPrice' => $autoBuy->totalPrice, 'message' => $autoBuy->message, 'error' => false]);
        }
    }

    public function actionPlaceSellOrder($id, $amount, $price, $userId, $orderId = null)
    {

        Yii::error(VarDumper::dumpAsString([
            $id,
            $amount,
            $price,
            $userId,
            $orderId
         ]));
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
            return $this->asJson(['message' => 'The order could not be saved. Please contact support', 'error' => true]);
        } else {
            $autoBuy = new AutoBuySellHelper();
            $result = $autoBuy->autoSell($order);

            Yii::error(VarDumper::dumpAsString([
                'result' => $result
            ]));

            return $this->asJson(['boughtQuantity' => $autoBuy->boughtQuantity, 'totalPrice' => $autoBuy->totalPrice, 'message' => $autoBuy->message, 'error' => false]);
        }
    }

    public function actionGetSellOrders($id, $userId)
    {
        $sellOrders = OrderShare::find()->andWhere(['type' => 2])->andWhere(['company_id' => $id])
            ->andWhere(['<>', 'user_id', $userId])->asArray()->all();
        return $this->asJson($sellOrders);
    }

    public function actionGetBuyOrders($id, $userId)
    {
        $sellOrders = OrderShare::find()->andWhere(['type' => 1])->andWhere(['company_id' => $id])
            ->andWhere(['<>', 'user_id', $userId])->asArray()->all();
        return $this->asJson($sellOrders);
    }
}