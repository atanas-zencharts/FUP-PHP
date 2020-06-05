<?php

namespace app\controllers;

use app\models\Company;
use app\models\CompanyPriceHistory;
use app\models\Cryptocurency;
use app\models\Forex;
use app\models\OrderCrypto;
use app\models\OrderForex;
use app\models\OrderShare;
use app\models\User;
use app\models\UserAsset;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        $client = new Client();
//        file_get_contents($url)
        return $this->render('about');
    }

    public function actionShare()
    {

        $companies = Company::find()->all();
        $users = User::find()->andWhere(['>', 'id', 2])->all();
        foreach ($users AS $user) {
            foreach ($companies AS $company) {
                $asset = new UserAsset();
                $asset->user_id = $user->id;
                $asset->asset_id = $company->id;
                $asset->asset_name = $company->name;
                $asset->asset_symbol = $company->symbol;
                $asset->asset_type = 1;
                $asset->asset_type_name = "Stocks";

                if (!$asset->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $asset->getErrors()
                    ]));
                } else {
                    $forSaleS = 0;
                    $rand = rand(6, 10);
                    Yii::error(VarDumper::dumpAsString([
                         'RAND' => $rand
                     ]));
                    for ($i = 0; $i <= $rand; $i++) {

                        Yii::error(VarDumper::dumpAsString([
                                    "ORDER SHARE"
                         ]));
                        $orderShare = new OrderShare();
                        $orderShare->company_id = $company->id;
                        $orderShare->price;
                        $orderShare->user_id = $user->id;
                        $orderShare->type = rand(1, 2);
                        $orderShare->quantity = rand(100, 200);
                        $orderShare->quantity_initial = $orderShare->quantity;
                        $orderShare->status_id = 1;
                        $orderShare->date_opened = (new \DateTime())->format(DATE_W3C);
                        $orderShare->price = (rand(1, 2) == 1 ? $company->price + (($company->price / 100) * rand(1, 10)) : $company->price - (($company->price / 100) * rand(1, 10)));

                        if (!$orderShare->save()) {
                            Yii::error(VarDumper::dumpAsString([
                                $orderShare->getErrors()
                            ]));
                        }
                        if ($orderShare->type == 2) {
                            $forSaleS += $orderShare->quantity;
                        }
                    }


                    if ($forSaleS) {
                        $asset->amount = $forSaleS * 10;
                        $asset->amount_sale = $forSaleS;
                        if (!$asset->save()) {
                            Yii::error(VarDumper::dumpAsString([
                                $asset->getErrors()
                            ]));
                        }
                    }

                }
            }
        }
    }

    public function actionForex()
    {
        $users = User::find()->andWhere(['>', 'id', 2])->all();
        $forex = Forex::find()->all();
        foreach ($users AS $user) {
            foreach ($forex AS $f) {

                $assetF = new UserAsset();
                $assetF->user_id = $user->id;
                $assetF->asset_id = $f->id;
                $assetF->asset_name = $f->ticker;
                $assetF->asset_symbol = $f->ticker;
                $assetF->asset_type = 2;
                $assetF->asset_type_name = "Forex";

                if (!$assetF->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $assetF->getErrors()
                    ]));
                } else {
                    $forSaleF = 0;
                    for ($i = 0; $i <= rand(6, 10); $i++) {
                        $orderF = new OrderForex();
                        $orderF->forex_id = $f->id;
                        $orderF->user_id = $user->id;
                        $orderF->type = rand(1, 2);
                        $orderF->quantity = rand(100, 200);
                        $orderF->quantity_initial = $orderF->quantity;
                        $orderF->status_id = 1;
                        $orderF->date_opened = (new \DateTime())->format(DATE_W3C);
                        $orderF->price = (rand(1, 2) == 1 ? $f->price + (($f->price / 100) * rand(1, 10)) : $f->price - (($f->price / 100) * rand(1, 10)));

                        if (!$orderF->save()) {
                            Yii::error(VarDumper::dumpAsString([
                                $orderF->getErrors()
                            ]));
                        } else {
                            if ($orderF->type == 2) {
                                $forSaleF += $orderF->quantity;
                            }
                        }

                    }

                    $assetF->amount = $forSaleF * 10;
                    $assetF->amount_sale = $forSaleF;

                    if (!$assetF->save()) {
                        Yii::error(VarDumper::dumpAsString([
                            $assetF->getErrors()
                        ]));
                    }
                }
            }
        }
    }

    public function actionCrypto()
    {
        $crypto = Cryptocurency::find()->all();
        $users = User::find()->andWhere(['>', 'id', 2])->all();
        foreach ($users AS $user) {
            foreach ($crypto AS $cryptocurency) {
                $assetC = new UserAsset();
                $assetC->user_id = $user->id;
                $assetC->asset_id = $cryptocurency->id;
                $assetC->asset_name = $cryptocurency->name;
                $assetC->asset_symbol = $cryptocurency->symbol;
                $assetC->asset_type = 3;
                $assetC->asset_type_name = "Crypto";

                if (!$assetC->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $assetC->getErrors()
                    ]));
                } else {
                    $forSaleC = 0;
                    for ($i = 0; $i <= rand(6, 10); $i++) {
                        $orderC = new OrderCrypto();
                        $orderC->crypto_id = $cryptocurency->id;
                        $orderC->user_id = $user->id;
                        $orderC->type = rand(1, 2);
                        $orderC->quantity = rand(100, 200);
                        $orderC->quantity_initial = $orderC->quantity;
                        $orderC->status_id = 1;
                        $orderC->date_opened = (new \DateTime())->format(DATE_W3C);
                        $orderC->price = (rand(1, 2) == 1 ? $cryptocurency->price + (($cryptocurency->price / 100) * rand(1, 10)) : $cryptocurency->price - (($cryptocurency->price / 100) * rand(1, 10)));

                        if (!$orderC->save()) {
                            Yii::error(VarDumper::dumpAsString([
                                $orderC->getErrors()
                            ]));
                        }
                        if ($orderC->type == 2) {
                            $forSaleC += $orderC->quantity;
                        }
                    }

                    $assetC->amount = $forSaleC * 10;
                    $assetC->amount_sale = $forSaleC;

                    if (!$assetC->save()) {
                        Yii::error(VarDumper::dumpAsString([
                            $assetC->getErrors()
                        ]));
                    }
                }
            }
        }
    }

    public function actionTest()
    {
        $company = Company::find()->one();
        $forex = Forex::find()->all();
        $cryptocurency = Cryptocurency::find()->one();
        $user = User::find()->andWhere(['=', 'id', 3])->one();

        $orderC = new OrderCrypto();
        $orderC->crypto_id = $cryptocurency->id;
        $orderC->user_id = $user->id;
        $orderC->type = rand(1, 2);
        $orderC->quantity = rand(100, 200);
        $orderC->quantity_initial = $orderC->quantity;
        $orderC->status_id = 1;
        $orderC->date_opened = (new \DateTime())->format(DATE_W3C);
        $orderC->price = (rand(1, 2) == 1 ? $cryptocurency->price + (($cryptocurency->price / 100) * rand(1, 10)) : $cryptocurency->price - (($cryptocurency->price / 100) * rand(1, 10)));

        if (!$orderC->save()) {
            Yii::error(VarDumper::dumpAsString([
                $orderC->getErrors()
            ]));
        }
    }

    public function actionTestR()
    {
        $companies = Company::find()->all();
        foreach ($companies AS $company) {
            $userAssets = UserAsset::find()
                ->andWhere(['asset_id' => $company->id])
                ->andWhere(['asset_name' => $company->name])
                ->andWhere(['asset_symbol' => $company->symbol])
                ->all();

            foreach ($userAssets AS $asset) {
                $asset->paid_avg = round($company->price - (($company->price / 100) * rand(10, 60)), 2);
                if (!$asset->save()) {
                    Yii::error(VarDumper::dumpAsString([
                         $asset->getErrors()
                     ]));
                }
            }
        }

        $forex = Forex::find()->all();
        foreach ($forex AS $f) {
            $userAssetsF = UserAsset::find()
                ->andWhere(['asset_id' => $f->id])
                ->andWhere(['asset_name' => $f->ticker])
                ->andWhere(['asset_symbol' => $f->ticker])
                ->all();

            foreach ($userAssetsF AS $asset) {
                $asset->paid_avg = round($f->ask - (($f->ask / 100) * rand(10, 60)), 2);
                if (!$asset->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $asset->getErrors()
                    ]));
                }
            }
        }

        $crypto = Cryptocurency::find()->all();
        foreach ($crypto AS $c) {
            $userAssetsC = UserAsset::find()
                ->andWhere(['asset_id' => $c->id])
                ->andWhere(['asset_name' => $c->name])
                ->andWhere(['asset_symbol' => $c->symbol])
                ->all();

            foreach ($userAssetsC AS $asset) {
                $asset->paid_avg = round($c->price - (($c->price / 100) * rand(10, 60)), 2);
                if (!$asset->save()) {
                    Yii::error(VarDumper::dumpAsString([
                        $asset->getErrors()
                    ]));
                }
            }
        }
    }

    public function actionPriceHistory() {
        $companies = Company::find()->all();

        foreach ($companies As $c)
            $previous = CompanyPriceHistory::find()->andWhere(['company_id' => $c->id])->orderBy('id DESC')->one();

            $cph = new CompanyPriceHistory();
            $cph->price = round((rand(1, 2) == 1 ? $previous->price + (($previous->price / 100) * rand(1, 10)) : $previous->price - (($previous->price / 100) * rand(1, 10))), 2);
            $cph->dayHigh = round($cph->price + (($cph->price / 100) * rand(1, 5)), 2);
            $cph->dayLow = round($cph->price - (($cph->price / 100) * rand(1, 5)), 2);
            $cph->previousDay = round((rand(1, 2) == 1 ? $previous->price + (($previous->price / 100) * rand(1, 3)) : $previous->price - (($previous->price / 100) * rand(1, 3))), 2);
            $cph->open = round((rand(1, 2) == 1 ? $previous->price + (($previous->price / 100) * rand(1, 3)) : $previous->price - (($previous->price / 100) * rand(1, 3))), 2);
            $cph->date = (new \DateTime())->format(DATE_W3C);
            $cph->company_id = $c->id;

            if (!$cph->save()) {
                Yii::error(VarDumper::dumpAsString([
                     $cph->getErrors()
                 ]));
            } else {
                $c->previousDay = $cph->previousDay;
                $c->price = $cph->price;
                $c->changePercentage = (1 - $cph->previousDay / $cph->price) * 100;
                $c->range = $cph->dayLow . ' - '. $cph->dayHigh;

                if (!$c->save()) {
                    Yii::error(VarDumper::dumpAsString([
                         $c->getErrors()
                     ]));
                }
            }
        }
}
