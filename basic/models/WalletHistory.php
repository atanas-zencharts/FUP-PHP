<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wallet_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $history_type_id
 * @property float $amount
 * @property string|null $date
 *
 * @property WalletHistoryTypes $historyType
 * @property User $user
 */
class WalletHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'history_type_id', 'amount'], 'required'],
            [['user_id', 'history_type_id'], 'integer'],
            [['amount'], 'number'],
            [['date'], 'safe'],
            [['history_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => WalletHistoryTypes::className(), 'targetAttribute' => ['history_type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'history_type_id' => 'History Type ID',
            'amount' => 'Amount',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[HistoryType]].
     *
     * @return \yii\db\ActiveQuery|\app\query\WalletHistoryTypesQuery
     */
    public function getHistoryType()
    {
        return $this->hasOne(WalletHistoryTypes::className(), ['id' => 'history_type_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\WalletHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\WalletHistoryQuery(get_called_class());
    }
}
