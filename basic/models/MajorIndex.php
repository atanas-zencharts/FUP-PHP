<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "major_index".
 *
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property float $price
 * @property float $change
 * @property float|null $dayLow
 * @property float|null $dayHigh
 * @property float|null $open
 * @property float|null $previousDay
 * @property float|null $changePercent
 */
class MajorIndex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'major_index';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'symbol', 'price', 'change'], 'required'],
            [['price', 'change', 'dayLow', 'dayHigh', 'open', 'previousDay', 'changePercent'], 'number'],
            [['name', 'symbol'], 'string', 'max' => 255],
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
            'symbol' => 'Symbol',
            'price' => 'Price',
            'change' => 'Change',
            'dayLow' => 'Day Low',
            'dayHigh' => 'Day High',
            'open' => 'Open',
            'previousDay' => 'Previous Day',
            'changePercent' => 'Change Percent',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\query\MajorIndexQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\MajorIndexQuery(get_called_class());
    }
}
