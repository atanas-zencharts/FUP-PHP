<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_asset".
 *
 * @property int $id
 * @property int $user_id
 * @property int $asset_id
 * @property string $asset_name
 * @property string $asset_symbol
 * @property int $asset_type
 * @property string $asset_type_name
 * @property int|null $amount
 * @property int|null $amount_sale
 * @property float|null $paid_min
 * @property float|null $paid_avg
 * @property float|null $paid_max
 * @property int|null $price_for_current
 * @property int|null $profit_all_time
 */
class UserAsset extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_asset';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'asset_id', 'asset_name', 'asset_symbol', 'asset_type', 'asset_type_name'], 'required'],
            [['user_id', 'asset_id', 'asset_type', 'amount', 'amount_sale', 'price_for_current', 'profit_all_time'], 'integer'],
            [['paid_min', 'paid_avg', 'paid_max'], 'number'],
            [['asset_name'], 'string', 'max' => 255],
            [['asset_symbol'], 'string', 'max' => 100],
            [['asset_type_name'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'asset_id' => 'Asset ID',
            'asset_name' => 'Asset Name',
            'asset_symbol' => 'Asset Symbol',
            'asset_type' => 'Asset Type',
            'asset_type_name' => 'Asset Type Name',
            'amount' => 'Amount',
            'amount_sale' => 'Amount Sale',
            'paid_min' => 'Paid Min',
            'paid_avg' => 'Paid Avg',
            'paid_max' => 'Paid Max',
            'price_for_current' => 'Price For Current',
            'profit_all_time' => 'Profit All Time',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\query\UserAssetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\UserAssetQuery(get_called_class());
    }
}
