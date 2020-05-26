<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "forex_history".
 *
 * @property int $id
 * @property string|null $date
 * @property float|null $bid
 * @property float|null $ask
 * @property float|null $open
 * @property float|null $low
 * @property float|null $high
 */
class ForexHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'forex_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['bid', 'ask', 'open', 'low', 'high'], 'number'],
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
            'bid' => 'Bid',
            'ask' => 'Ask',
            'open' => 'Open',
            'low' => 'Low',
            'high' => 'High',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\query\ForexHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\ForexHistoryQuery(get_called_class());
    }
}
