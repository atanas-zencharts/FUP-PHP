<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "forex".
 *
 * @property int $id
 * @property string $ticker
 * @property float $bid
 * @property float $ask
 * @property float $open
 * @property float $low
 * @property float $high
 * @property float $changes
 * @property string $date
 *
 * @property OrderForex[] $orderForexes
 */
class Forex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'forex';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticker', 'bid', 'ask', 'open', 'low', 'high', 'changes'], 'required'],
            [['bid', 'ask', 'open', 'low', 'high', 'changes'], 'number'],
            [['date'], 'safe'],
            [['ticker'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticker' => 'Ticker',
            'bid' => 'Bid',
            'ask' => 'Ask',
            'open' => 'Open',
            'low' => 'Low',
            'high' => 'High',
            'changes' => 'Changes',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[OrderForexes]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderForexQuery
     */
    public function getOrderForexes()
    {
        return $this->hasMany(OrderForex::className(), ['forex_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\ForexQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\ForexQuery(get_called_class());
    }
}
