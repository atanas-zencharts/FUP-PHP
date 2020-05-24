<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property int $id
 * @property string $name
 *
 * @property OrderCrypto[] $orderCryptos
 * @property OrderForex[] $orderForexes
 * @property OrderShare[] $orderShares
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
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
     * Gets query for [[OrderCryptos]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderCryptoQuery
     */
    public function getOrderCryptos()
    {
        return $this->hasMany(OrderCrypto::className(), ['status_id' => 'id']);
    }

    /**
     * Gets query for [[OrderForexes]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderForexQuery
     */
    public function getOrderForexes()
    {
        return $this->hasMany(OrderForex::className(), ['status_id' => 'id']);
    }

    /**
     * Gets query for [[OrderShares]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderShareQuery
     */
    public function getOrderShares()
    {
        return $this->hasMany(OrderShare::className(), ['status_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\StatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\StatusQuery(get_called_class());
    }
}
