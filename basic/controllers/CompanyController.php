<?php


namespace app\controllers;

use app\components\AutoBuySellHelper;
use app\models\UserAsset;
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
        $company = Company::findOne($id);
        $asset = UserAsset::find()
            ->andWhere(['user_id' => $userId])
            ->andWhere(['asset_id' => $company->id])
            ->andWhere(['asset_name' => $company->name])
            ->andWhere(['asset_symbol' => $company->symbol])
            ->one();

        if ($asset->amount == 0) {
            return $this->asJson(['message' => "Order could not be placed, because user don't own any shares", 'error' => true]);
        }

        $order = OrderShare::findOne($orderId);
        if (!$order) {
            $order = new OrderShare();
            $order->company_id = $id;
            $order->user_id = $userId;
            $order->type = 2;
            $order->status_id = 1;
        }

        if ($order->status_id < 2) {
            $order->quantity_initial = ($asset->amount <= $amount ? $asset->amount : $amount);
        }
        $order->quantity = ($asset->amount <= $amount ? $asset->amount : $amount);
        $order->price = $price;

        if (!$order->save()) {
            Yii::error(VarDumper::dumpAsString([
                $order->getErrors()
            ]));
            return $this->asJson(['message' => 'The order could not be saved. Please contact support', 'error' => true]);
        } else {
            $autoBuy = new AutoBuySellHelper();
            $result = $autoBuy->autoSell($order);

            return $this->asJson(['boughtQuantity' => $autoBuy->boughtQuantity, 'totalPrice' => $autoBuy->totalPrice, 'message' => $autoBuy->message, 'error' => false]);
        }
    }

    public function actionGetSellOrders($id, $userId)
    {

        $sellOrders = OrderShare::find()->andWhere(['type' => 2])->andWhere(['company_id' => $id])
            ->andWhere(['<>', 'user_id', $userId])->andWhere(['<', 'status_id', 3]);

        return $this->asJson($sellOrders->asArray()->all());
    }

    public function actionGetBuyOrders($id, $userId)
    {
        $buyOrders = OrderShare::find()->andWhere(['type' => 1])->andWhere(['company_id' => $id])
            ->andWhere(['<>', 'user_id', $userId])->andWhere(['<', 'status_id', 3]);

        return $this->asJson($buyOrders->asArray()->all());
    }

    public function actionGetUserOrders($id)
    {
        $orders = OrderShare::find()->andWhere(['<', 'status_id', 3])->andWhere(['user_id' => $id])->asArray()->all();
        return $this->asJson($orders);
    }

    public function actionDeleteOrder($id)
    {
        $order = OrderShare::findOne($id);

        if ($order->type == 2) {
            $asset = UserAsset::find()->andWhere(['user_id' => $order->user_id])->andWhere(['asset_id' => $order->company_id])
                ->andWhere(['asset_name' => $order->company->name])->andWhere(['asset_symbol' => $order->company->symbol])->one();

            $asset->amount_sale = $asset->amount_sale - $order->quantity;
            $asset->save();
        }

        if ($order->delete()) {
            return $this->asJson(['success' => true, 'message' => "Order was deleted successfully"]);
        } else {
            return $this->asJson(['success' => false, 'message' => "Order could not be deleted"]);
        }
    }

    public function actionUpdateOrder($id, $price, $amount)
    {
        $order = OrderShare::findOne($id);
        $order->quantity = $amount;
        $order->price = $price;

        if (!$order->save()) {
            Yii::error(VarDumper::dumpAsString([
                $order->getErrors()
            ]));
            return $this->asJson(['message' => 'The order could not be updated. Please contact support', 'error' => true]);
        } else {
            $autoBuy = new AutoBuySellHelper();
            $result = $autoBuy->autoSell($order);

            Yii::error(VarDumper::dumpAsString([
                'result' => $result
            ]));

            return $this->asJson(['boughtQuantity' => $autoBuy->boughtQuantity, 'totalPrice' => $autoBuy->totalPrice, 'message' => $autoBuy->message, 'error' => false]);
        }
    }
}