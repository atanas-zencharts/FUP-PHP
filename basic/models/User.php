<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string|null $date_of_birth
 * @property string|null $city
 * @property string|null $post_code
 * @property string|null $address
 * @property int|null $disabled
 * @property float|null $wallet
 * @property string|null $password
 *
 * @property OrderCrypto[] $orderCryptos
 * @property OrderForex[] $orderForexes
 * @property OrderShare[] $orderShares
 * @property WalletHistory[] $walletHistories
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'first_name', 'last_name'], 'required'],
            [['date_of_birth'], 'safe'],
            [['disabled'], 'integer'],
            [['wallet'], 'number'],
            [['username'], 'string', 'max' => 500],
            [['first_name', 'last_name', 'city', 'post_code'], 'string', 'max' => 255],
            [['address', 'password'], 'string', 'max' => 1000],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'date_of_birth' => 'Date Of Birth',
            'city' => 'City',
            'post_code' => 'Post Code',
            'address' => 'Address',
            'disabled' => 'Disabled',
            'wallet' => 'Wallet',
            'password' => 'Password',
        ];
    }

    /**
     * Gets query for [[OrderCryptos]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderCryptoQuery
     */
    public function getOrderCryptos()
    {
        return $this->hasMany(OrderCrypto::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[OrderForexes]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderForexQuery
     */
    public function getOrderForexes()
    {
        return $this->hasMany(OrderForex::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[OrderShares]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderShareQuery
     */
    public function getOrderShares()
    {
        return $this->hasMany(OrderShare::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[WalletHistories]].
     *
     * @return \yii\db\ActiveQuery|\app\query\WalletHistoryQuery
     */
    public function getWalletHistories()
    {
        return $this->hasMany(WalletHistory::className(), ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\UserQuery(get_called_class());
    }
}
