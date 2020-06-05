<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_crypto".
 *
 * @property int $id
 * @property int $user_id
 * @property int $crypto_id
 * @property int $status_id
 * @property float $price
 * @property int $quantity
 * @property int|null $quantity_initial
 * @property string $date_opened
 * @property string|null $date_closed
 * @property float|null $paid
 * @property float|null $profit
 * @property int $type 1 - Buy, 2 - Sell
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
            [['user_id', 'crypto_id', 'status_id', 'price', 'quantity', 'type'], 'required'],
            [['user_id', 'crypto_id', 'status_id', 'quantity', 'quantity_initial', 'type'], 'integer'],
            [['price', 'paid', 'profit'], 'number'],
            [['date_opened', 'date_closed'], 'safe'],
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
            'status_id' => 'Status ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'quantity_initial' => 'Quantity Initial',
            'date_opened' => 'Date Opened',
            'date_closed' => 'Date Closed',
            'paid' => 'Paid',
            'profit' => 'Profit',
            'type' => 'Type',
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
