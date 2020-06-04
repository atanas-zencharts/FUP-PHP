<?php


namespace app\controllers;


use app\models\User;
use app\models\UserAsset;
use yii\rest\Controller;

class AssetController extends Controller
{
    public function actionGetSummaryInfo($id)
    {
        $user = User::findOne($id);
        $assets = UserAsset::find()->andWhere(['user_id' => $id])->andWhere(['<>', 'amount', 0])->all();
        $summaryInfo = ['Cash' => $user->wallet, 'Forex' => 0, 'Shares' => 0, 'Crypto' => 0];
        foreach ($assets AS $asset) {
            switch ($asset->asset_type) {
                CASE 1:
                    $summaryInfo['Shares'] = $summaryInfo['Shares'] + ($asset->amount * $asset->paid_avg);
                    break;
                CASE 2:
                    $summaryInfo['Forex'] = $summaryInfo['Forex'] + ($asset->amount * $asset->paid_avg);
                    break;
                CASE 3:
                    $summaryInfo['Crypto'] = $summaryInfo['Crypto'] + ($asset->amount * $asset->paid_avg);
                    break;
            }
        }
        $summaryInfo['Total'] = $summaryInfo['Cash'] + $summaryInfo['Shares'] + $summaryInfo['Forex'] + $summaryInfo['Crypto'];
        return $this->asJson($summaryInfo);
    }

    public function actionGetDetailedInformation($id)
    {
        $assets = UserAsset::find()->andWhere(['user_id' => $id])->andWhere(['<>', 'amount', 0])->asArray()->all();
        return $this->asJson($assets);
    }
}