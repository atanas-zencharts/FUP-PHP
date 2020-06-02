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

class AutoBuyerHelper
{
    public string $message = '';
    public float $totalPrice = 0;
    public int $boughtQuantity = 0;

    /** @var OrderShare $buyerOrder */
    private OrderShare $buyerOrder;

    /** @var OrderShare $saleOrder */
    private OrderShare $saleOrder;
    private User $buyer;
    private User $seller;
    private int $canBuy;
    private int $leftToBuy;


    public function __construct($order)
    {
        $this->buyerOrder = $order;
    }

    public function autoBuy()
    {
        $this->buyer = $this->buyerOrder->user;
        $this->leftToBuy = $this->buyerOrder->quantity;
        $saleOrders = $this->getSaleOrdersQuery();

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
            $this->checkWallet();

            if ($this->canBuy < 1 || $this->leftToBuy == 0) {
                break;
            }
            $this->buy();
        }

        if ($this->buyerOrder->quantity == 0) {
            $this->message = 'Order was placed and successfully executed. The full quantity was purchased';
        } elseif ($this->buyerOrder->quantity > 0) {
            $this->message = 'Order was placed and partially executed. The quantity purchased is ' . $this->boughtQuantity . ' out of ' .$this->buyerOrder->quantity_initial;
        }
        return true;
    }

    private function buy()
    {
        if ($this->saleOrder->quantity > $this->buyerOrder->quantity) {
            $this->sellerExceedBuyer();
        } elseif ($this->saleOrder->quantity < $this->buyerOrder->quantity) {
            $this->buyerExceedSeller();
        } elseif ($this->saleOrder->quantity == $this->buyerOrder->quantity) {
            $this->sellerEqualToBuyer();
        }
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
        $asset = $this->getAsset($this->seller->id, $this->saleOrder->company);

        if ($asset) {
            $asset->amount = $asset->amount - $amountSold;
            $asset->amount_sale = $asset->amount_sale - $amountSold;
            $asset->profit_all_time = $asset->profit_all_time + $totalPrice;

            if (!$asset->save()) {
                Yii::error(VarDumper::dumpAsString([
                     $asset->getErrors()
                 ]));
            }
        }
    }

    private function getAsset($userId, $company)
    {
       return UserAsset::find()
            ->andWhere(['user_id' => $userId])
            ->andWhere(['asset_id' => $company->id])
            ->andWhere(['asset_name' => $company->name])
            ->andWhere(['asset_symbol' => $company->symbol])
            ->one();
    }

    /**
     * @param Company $company
     * @return UserAsset
     */
    private function getNewAsset($company)
    {
        $asset = new UserAsset();
        $asset->user_id = $this->buyer->id;
        $asset->asset_id = $company->id;
        $asset->asset_name = $company->name;
        $asset->asset_symbol = $company->symbol;
        $asset->asset_type = 1;
        $asset->asset_type_name = "Company Stocks";
        $asset->paid_min = $this->saleOrder->price;
        $asset->paid_max = $this->saleOrder->price;
        $asset->paid_avg = $this->saleOrder->price;
        return $asset;
    }

    private function recordBuyerAsset($totalPrice, $amountBought)
    {
        $asset = $this->getAsset($this->buyer->id ,$this->buyerOrder->company);

        if (!$asset) {
            $asset = $this->getNewAsset($this->buyerOrder->company);
        }

        if ($asset) {
            if ($asset->paid_min > $this->saleOrder->price) {
                $asset->paid_min = $this->saleOrder->price;
            }

            if ($asset->paid_max < $this->saleOrder->price) {
                $asset->paid_max = $this->saleOrder->price;
            }

            if ($asset->paid_avg != $this->saleOrder->price) {
                $asset->paid_avg = (($asset->amount * $asset->paid_avg) + $totalPrice) / ($asset->amount + $amountBought);
            }

            $asset->amount = $asset->amount + $amountBought;
            $asset->profit_all_time = $asset->profit_all_time - $totalPrice;

            if (!$asset->save()) {
                Yii::error(VarDumper::dumpAsString([
                    $asset->getErrors()
                ]));
            }
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

    private function getSaleOrdersQuery()
    {
        return OrderShare::find()
            ->joinWith('user')
            ->andWhere(['order_share.company_id' => $this->buyerOrder->company_id])
            ->andWhere(['<>', 'order_share.status_id', 3])
            ->andWhere(['order_share.type' => 2])
            ->andWhere('order_share.price <= ' . $this->buyerOrder->price)
            ->andWhere(['>', $this->buyer->wallet, 'order_share.price'])
            ->orderBy('order_share.date_opened DESC, order_share.price ASC');
    }
}