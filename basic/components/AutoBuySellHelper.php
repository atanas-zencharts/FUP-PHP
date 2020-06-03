<?php


namespace app\components;

use app\models\Company;
use app\models\UserAsset;
use Yii;
use yii\helpers\VarDumper;
use app\models\OrderShare;
use app\models\User;
use app\models\WalletHistory;
use yii\db\Query;

/** @property OrderShare $buyerOrder */
/** @property OrderShare $saleOrder */
/** @property User $buyer */
/** @property User $seller */
/** @property string $message */

class AutoBuySellHelper
{
    public string $message = '';
    public float $totalPrice = 0;
    public int $boughtQuantity = 0;

    /** @var OrderShare $buyerOrder */
    private OrderShare $buyerOrder;

    /** @var OrderShare $saleOrder */
    private OrderShare $saleOrder;

    private UserAsset $buyerAsset;
    private UserAsset $sellerAsset;
    private User $buyer;
    private User $seller;
    private int $canBuy;
    private int $canSell;
    private int $leftToBuy;
    private int $leftToSell;

    public function autoBuy($order)
    {
        $this->buyerOrder = $order;
        $this->buyer = $this->buyerOrder->user;
        $this->leftToBuy = $this->buyerOrder->quantity;
        $this->buyerAsset = $this->getAsset($this->buyer->id, $this->buyerOrder->company, $this->buyerOrder);
        $saleOrders = $this->getOrdersQuery(2, $this->buyerOrder, $this->buyer->wallet);

        Yii::error(VarDumper::dumpAsString([
            'rawSql' => $saleOrders->createCommand()->rawSql
         ]));

        if (!$saleOrders->exists()) {
            $this->message = 'Order was placed but is not executed because there is no seller at the moment.';
            return false;
        }

        $orders = $saleOrders->all();

        foreach ($orders AS $orderShare) {
            $this->saleOrder = $orderShare;
            $this->seller = $this->saleOrder->user;
            $this->sellerAsset = $this->getAsset($this->seller->id, $this->saleOrder->company, $this->saleOrder);
            $this->checkWallet();

            if ($this->canBuy < 1 || $this->leftToBuy == 0) {
                break;
            }
            $this->doOperation();
        }

        if ($this->buyerOrder->quantity == 0) {
            $this->message = 'Order was placed and successfully executed. The full quantity was purchased';
        } elseif ($this->buyerOrder->quantity > 0) {
            $this->message = 'Order was placed and partially executed. The quantity purchased is ' . $this->boughtQuantity . ' out of ' .$this->buyerOrder->quantity_initial;
        }
        return true;
    }

    private function doOperation()
    {
        if ($this->saleOrder->quantity > $this->buyerOrder->quantity) {
            $this->sellerExceedBuyer();
        } elseif ($this->saleOrder->quantity < $this->buyerOrder->quantity) {
            $this->buyerExceedSeller();
        } elseif ($this->saleOrder->quantity == $this->buyerOrder->quantity) {
            $this->sellerEqualToBuyer();
        }
    }

    public function autoSell($order)
    {
        $this->saleOrder = $order;
        $this->seller = $this->saleOrder->user;
        $this->leftToSell = $this->saleOrder->quantity;
        $this->sellerAsset = $this->getAsset($this->seller->id, $this->saleOrder->company, $this->saleOrder);
        $this->sellerAsset->amount_sale = $this->sellerAsset->amount_sale + $this->saleOrder->quantity;
        $this->sellerAsset->save();
        $orders = $this->getOrdersQuery(1, $this->saleOrder, $this->seller->wallet);

        Yii::error(VarDumper::dumpAsString([
            'rawSql' => $orders->createCommand()->rawSql
        ]));

        if (!$orders->exists()) {
            $this->message = 'Order was placed but is not executed because there is no buyer at the moment.';
            return false;
        }

        foreach ($orders->all() AS $order) {
            $this->buyerOrder = $order;
            $this->buyer = $this->buyerOrder->user;
            $this->buyerAsset = $this->getAsset($this->buyer->id, $this->buyerOrder->company, $this->buyerOrder);
            $this->checkWallet();

            if ($this->canBuy < 1 || $this->leftToBuy == 0) {
                break;
            }
            $this->doOperation();
        }

        if ($this->buyerOrder->quantity == 0) {
            $this->message = 'Order was placed and successfully executed. The full quantity was purchased';
        } elseif ($this->buyerOrder->quantity > 0) {
            $this->message = 'Order was placed and partially executed. The quantity purchased is ' . $this->boughtQuantity . ' out of ' .$this->buyerOrder->quantity_initial;
        }
        return true;
    }


    private function checkWallet()
    {
        $this->canBuy = $this->saleOrder->quantity;

        if ($this->buyer->wallet < ($this->saleOrder->quantity * $this->saleOrder->price)) {
            $this->canBuy = round(($this->buyer->wallet / $this->saleOrder->price) ,2,PHP_ROUND_HALF_DOWN);
        }

        if ($this->canBuy > $this->buyerOrder->quantity) {
            $this->canBuy = $this->buyerOrder->quantity;
        }
        $this->boughtQuantity += $this->canBuy;
    }

    private function recordSellerWallet($totalPrice)
    {
        $amountBefore = $this->seller->wallet;
        $amount = $this->seller->wallet + $totalPrice;
        $this->seller->updateAttributes(['wallet' => $amount]);
        $this->recordWalletHistory($this->seller->id, 4, $totalPrice, $amountBefore, $amount);
    }

    private function recordWalletHistory($userId, $typeId, $totalPrice, $amountBefore, $amount)
    {
        $history = new WalletHistory();
        $history->user_id = $userId;
        $history->history_type_id = $typeId;
        $history->amount = $totalPrice;
        $history->amount_before = $amountBefore;
        $history->amount_after = $amount;
        $history->date = (new \DateTime())->format(DATE_W3C);

        if (!$history->save()) {
            Yii::error(VarDumper::dumpAsString([
                $history->getErrors()
            ]));
        }
    }

    private function recordBuyerWallet($totalPrice)
    {
        $this->totalPrice += $totalPrice;
        $amountBefore = $this->buyer->wallet;
        $amount = $this->buyer->wallet - $totalPrice;
        $this->buyer->updateAttributes(['wallet' => $amount]);
        $this->recordWalletHistory($this->buyer->id, 3, $totalPrice, $amountBefore, $amount);

    }

    private function recordSellerAsset($totalPrice, $amountSold)
    {
        $this->sellerAsset->amount = $this->sellerAsset->amount - $amountSold;
        $this->sellerAsset->amount_sale = $this->sellerAsset->amount_sale - $amountSold;
        $this->sellerAsset->profit_all_time = $this->sellerAsset->profit_all_time + $totalPrice;

        if (!$this->sellerAsset->save()) {
            Yii::error(VarDumper::dumpAsString([
                $this->sellerAsset->getErrors()
            ]));
        }
    }

    private function getAsset($userId, $company, $order)
    {
       $asset = UserAsset::find()
            ->andWhere(['user_id' => $userId])
            ->andWhere(['asset_id' => $company->id])
            ->andWhere(['asset_name' => $company->name])
            ->andWhere(['asset_symbol' => $company->symbol])
            ->one();

       if (!$asset) {
           $asset = $this->getNewAsset($company, $order);
       }
       return $asset;
    }

    /**
     * @param Company $company
     * @return UserAsset
     */
    private function getNewAsset($company, $order)
    {
        $asset = new UserAsset();
        $asset->user_id = $this->buyer->id;
        $asset->asset_id = $company->id;
        $asset->asset_name = $company->name;
        $asset->asset_symbol = $company->symbol;
        $asset->asset_type = 1;
        $asset->asset_type_name = "Company Stocks";
        $asset->paid_min = $order->price;
        $asset->paid_max = $order->price;
        $asset->paid_avg = $order->price;

        if (!$asset->save()) {
            Yii::error(VarDumper::dumpAsString([
                 $asset->getErrors()
             ]));
        }
        return $asset;
    }

    private function recordBuyerAsset($totalPrice, $amountBought)
    {
        if ($this->buyerAsset->paid_min > $this->saleOrder->price) {
            $this->buyerAsset->paid_min = $this->saleOrder->price;
        }

        if ($this->buyerAsset->paid_max < $this->saleOrder->price) {
            $this->buyerAsset->paid_max = $this->saleOrder->price;
        }

        if ($this->buyerAsset->paid_avg != $this->saleOrder->price) {
            $this->buyerAsset->paid_avg = (($this->buyerAsset->amount * $this->buyerAsset->paid_avg) + $totalPrice) / ($this->buyerAsset->amount + $amountBought);
        }

        $this->buyerAsset->amount = $this->buyerAsset->amount + $amountBought;
        $this->buyerAsset->profit_all_time = $this->buyerAsset->profit_all_time - $totalPrice;

        if (!$this->buyerAsset->save()) {
            Yii::error(VarDumper::dumpAsString([
                $this->buyerAsset->getErrors()
            ]));
        }
    }

    private function sellerExceedBuyer()
    {
       $totalPrice = $this->canBuy * $this->saleOrder->price;
       $this->totalPrice += $totalPrice;
       $this->saleOrder->quantity = $this->saleOrder->quantity - $this->canBuy;
       $this->saleOrder->status_id = 2;
       $this->saleOrder->profit = $this->saleOrder->profit + $totalPrice;

       if ($this->saleOrder->save()) {
           $this->recordSellerWallet($totalPrice);
           $this->recordBuyerWallet($totalPrice);
           $this->recordSellerAsset($totalPrice, $this->canBuy);
           $this->recordBuyerAsset($totalPrice, $this->canBuy);
           $this->leftToBuy = 0;
       }

        $this->buyerOrder->quantity = 0;
        $this->buyerOrder->status_id = 3;
        $this->buyerOrder->paid = $totalPrice;

        if (!$this->buyerOrder->save()) {
            Yii::error(VarDumper::dumpAsString([
                $this->buyerOrder->getErrors()
            ]));
        }
    }

    private function buyerExceedSeller()
    {
        $totalPrice = $this->saleOrder->quantity * $this->saleOrder->price;
        $this->totalPrice += $totalPrice;
        $this->saleOrder->quantity = 0;
        $this->saleOrder->status_id = 3;
        $this->saleOrder->profit = $this->saleOrder->profit + $totalPrice;
        if ($this->saleOrder->save()) {
            $this->recordSellerWallet($totalPrice);
            $this->recordBuyerWallet($totalPrice);
            $this->recordSellerAsset($totalPrice, $this->canBuy);
            $this->recordBuyerAsset($totalPrice, $this->canBuy);
            $this->leftToBuy = $this->leftToBuy - $this->saleOrder->quantity;
        }

        $this->buyerOrder->quantity = $this->buyerOrder->quantity - $this->saleOrder->quantity;
        $this->buyerOrder->status_id = 2;
        $this->buyerOrder->paid = $totalPrice;

        if (!$this->buyerOrder->save()) {
            Yii::error(VarDumper::dumpAsString([
                $this->buyerOrder->getErrors()
            ]));
        }
    }

    private function sellerEqualToBuyer()
    {
        $totalPrice = $this->saleOrder->quantity * $this->saleOrder->price;
        $this->saleOrder->quantity = 0;
        $this->saleOrder->status_id = 3;
        $this->saleOrder->profit = $this->saleOrder->profit + $totalPrice;
        if ($this->saleOrder->save()) {
            $this->recordSellerWallet($totalPrice);
            $this->recordBuyerWallet($totalPrice);
            $this->recordSellerAsset($totalPrice, $this->canBuy);
            $this->recordBuyerAsset($totalPrice, $this->canBuy);
            $this->leftToBuy = 0;
        } else {
            Yii::error(VarDumper::dumpAsString([
                $this->saleOrder->getErrors()
            ]));
        }

        $this->buyerOrder->quantity = 0;
        $this->buyerOrder->status_id = 3;
        $this->buyerOrder->paid = $totalPrice;

        if (!$this->buyerOrder->save()) {
            Yii::error(VarDumper::dumpAsString([
                $this->buyerOrder->getErrors()
             ]));
        }
    }

    private function getOrdersQuery($typeId, $order, $wallet)
    {
        return OrderShare::find()
            ->joinWith('user')
            ->andWhere(['order_share.company_id' => $order->company_id])
            ->andWhere(['<>', 'order_share.user_id',  $order->user_id])
            ->andWhere(['<>', 'order_share.status_id', 3])
            ->andWhere(['order_share.type' => $typeId])
            ->andWhere('order_share.price <= ' . $order->price)
            ->andWhere(['>', $wallet, 'order_share.price'])
            ->orderBy('order_share.date_opened DESC, order_share.price ASC');
    }
}