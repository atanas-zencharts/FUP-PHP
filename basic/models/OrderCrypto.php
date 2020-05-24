<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_crypto".
 *
 * @property int $id
 * @property int $user_id
 * @property int $crypto_id
 * @property float $price
 * @property int $quantity
 * @property string $date_buy
 * @property int $status_id
 * @property int $type 1 - Buy, 2 - Hold, 3 - Sell
 * @property string|null $date_sell
 * @property float|null $paid
 * @property float|null $profit
 *
 * @property Cryptocurency $crypto
 * @property Status $status
 * @property User $user
 */
class OrderCrypto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_crypto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'crypto_id', 'price', 'quantity', 'status_id', 'type'], 'required'],
            [['user_id', 'crypto_id', 'quantity', 'status_id', 'type'], 'integer'],
            [['price', 'paid', 'profit'], 'number'],
            [['date_buy', 'date_sell'], 'safe'],
            [['crypto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cryptocurency::className(), 'targetAttribute' => ['crypto_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
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
            'crypto_id' => 'Crypto ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'date_buy' => 'Date Buy',
            'status_id' => 'Status ID',
            'type' => 'Type',
            'date_sell' => 'Date Sell',
            'paid' => 'Paid',
            'profit' => 'Profit',
        ];
    }

    /**
     * Gets query for [[Crypto]].
     *
     * @return \yii\db\ActiveQuery|\app\query\CryptocurencyQuery
     */
    public function getCrypto()
    {
        return $this->hasOne(Cryptocurency::className(), ['id' => 'crypto_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery|\app\query\StatusQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
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
     * @return \app\query\OrderCryptoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\OrderCryptoQuery(get_called_class());
    }
}
