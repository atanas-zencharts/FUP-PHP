<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cryptocurency".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property float $change
 * @property int $capitalization
 *
 * @property OrderCrypto[] $orderCryptos
 */
class Cryptocurency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cryptocurency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'change', 'capitalization'], 'required'],
            [['price', 'change'], 'number'],
            [['capitalization'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'price' => 'Price',
            'change' => 'Change',
            'capitalization' => 'Capitalization',
        ];
    }

    /**
     * Gets query for [[OrderCryptos]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderCryptoQuery
     */
    public function getOrderCryptos()
    {
        return $this->hasMany(OrderCrypto::className(), ['crypto_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\CryptocurencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\CryptocurencyQuery(get_called_class());
    }
}
