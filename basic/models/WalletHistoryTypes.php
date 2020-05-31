<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wallet_history_types".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property WalletHistory[] $walletHistories
 */
class WalletHistoryTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_history_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 70],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[WalletHistories]].
     *
     * @return \yii\db\ActiveQuery|\app\query\WalletHistoryQuery
     */
    public function getWalletHistories()
    {
        return $this->hasMany(WalletHistory::className(), ['history_type_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\WalletHistoryTypesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\WalletHistoryTypesQuery(get_called_class());
    }
}
