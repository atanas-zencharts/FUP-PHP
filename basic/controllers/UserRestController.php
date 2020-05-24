<?php
/**
 * Created by PhpStorm.
 * User: Vvm
 * Date: 2/11/2020
 * Time: 11:40 AM
 */

namespace app\controllers;

use app\models\User;
use Yii;
use yii\rest\Controller;

class UserRestController extends Controller
{

    public function actionRegister()
    {
        $getRequest = Yii::$app->request->get();
        if ($getRequest && User::findByUsername($getRequest['username']) == null) {
            $user = new User();

            if ($user->load($getRequest, '') && $user->save()) {
                return $this->asJson(['success' => true]);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'This username is already taken. Or there is an registration error.']);
    }

    public function actionLogin()
    {
        $getRequest = Yii::$app->request->get();
        if ($getRequest) {
            $user = User::findUser($getRequest['username'], true);
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
}