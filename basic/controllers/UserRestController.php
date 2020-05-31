<?php
/**
 * Created by PhpStorm.
 * User: Vvm
 * Date: 2/11/2020
 * Time: 11:40 AM
 */

namespace app\controllers;

use app\models\User;
use app\models\WalletHistory;
use app\models\WalletHistoryTypesBL;
use Yii;
use yii\rest\Controller;
use yii\helpers\VarDumper;

class UserRestController extends Controller
{

    public function actionRegister()
    {
        $getRequest = Yii::$app->request->get();

        if ($getRequest && !User::find()->andWhere(['username' => $getRequest['username']])->exists()) {
            $user = new User();

            if ($user->load($getRequest, '') && $user->save()) {
                return $this->asJson(['success' => true]);
            } else {
                Yii::error(VarDumper::dumpAsString([
                    $user->getErrors()
                 ]));
            }
        }

        return $this->asJson(['success' => false, 'message' => 'This username is already taken. Or there is an registration error.']);
    }

    public function actionLogin()
    {
        $getRequest = Yii::$app->request->get();

        if ($getRequest) {
            $user = User::find()->andWhere(['username' => $getRequest['username']])->one();

            if ($user->password === $getRequest['password']) {
                return $this->asJson([
                    'success' => true,
                    'userID' => $user->id
                ]);
            }
        }

        return $this->asJson([
            'message' => 'Unsuccessful Login'
        ]);
    }

    public function actionGetUserWalletHistory($id)
    {

        Yii::error(VarDumper::dumpAsString([
             '$id' => $id
         ]));
        $walletHistory = WalletHistory::find()->andWhere(['user_id' => $id])->orderBy('date DESC')->asArray()->all();
        return $this->asJson($walletHistory);
    }

    public function actionGetUserCurrentAmount($id)
    {
        $user = User::findOne($id);
        return $user->wallet;
    }

    public function actionUserWalletOperation($id, $amount, $operationType)
    {
        $user = User::find()->andWhere(['id' => $id])->one();
        if (!$user) {
            return null;
        }
        $history = new WalletHistory();
        $history->amount_before = $user->wallet;

        $sum = null;
        if ($operationType == WalletHistoryTypesBL::TYPE_ADD) {
            $sum = $user->wallet + $amount;
        } elseif ($operationType == WalletHistoryTypesBL::TYPE_WITHDRAWAL) {
            $sum = $user->wallet - $amount;
        }

        if ($sum && $user->updateAttributes(['wallet' => $sum])) {
            $history->date = (new \DateTime())->format(DATE_W3C);
            $history->amount = $amount;
            $history->amount_after = $sum;
            $history->history_type_id = $operationType;
            $history->user_id = $user->id;

            if ($history->save()) {
                Yii::error(VarDumper::dumpAsString([
                     $history->getErrors()
                 ]));
            }
        }
        return $user->wallet;
    }
}