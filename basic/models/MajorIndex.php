<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "major_index".
 *
 * @property int $id
 * @property string $name
 * @property string $ticker
 * @property float $price
 * @property float $change
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
            [['name', 'ticker', 'price', 'change'], 'required'],
            [['price', 'change'], 'number'],
            [['name', 'ticker'], 'string', 'max' => 255],
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
            'ticker' => 'Ticker',
            'price' => 'Price',
            'change' => 'Change',
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
