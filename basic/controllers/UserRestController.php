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
}