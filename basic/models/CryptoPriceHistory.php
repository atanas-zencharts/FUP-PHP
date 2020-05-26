<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "crypto_price_history".
 *
 * @property int $id
 * @property string|null $date
 * @property float|null $price
 * @property float|null $capitalization
 * @property int $crypto_id
 *
 * @property Cryptocurency $crypto
 */
class CryptoPriceHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'crypto_price_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['price', 'capitalization'], 'number'],
            [['crypto_id'], 'required'],
            [['crypto_id'], 'integer'],
            [['crypto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cryptocurency::className(), 'targetAttribute' => ['crypto_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'price' => 'Price',
            'capitalization' => 'Capitalization',
            'crypto_id' => 'Crypto ID',
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
     * {@inheritdoc}
     * @return \app\query\CryptoPriceHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\CryptoPriceHistoryQuery(get_called_class());
    }
}
