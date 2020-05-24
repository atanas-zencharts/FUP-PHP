<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wallet_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $action_type
 * @property float $amount
 *
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
            [['user_id', 'action_type', 'amount'], 'required'],
            [['user_id', 'action_type'], 'integer'],
            [['amount'], 'number'],
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
            'action_type' => 'Action Type',
            'amount' => 'Amount',
        ];
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
