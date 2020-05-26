<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "major_index_price_history".
 *
 * @property int $id
 * @property int $major_index_id
 * @property string|null $date
 * @property float|null $price
 */
class MajorIndexPriceHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'major_index_price_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['major_index_id'], 'required'],
            [['major_index_id'], 'integer'],
            [['date'], 'safe'],
            [['price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'major_index_id' => 'Major Index ID',
            'date' => 'Date',
            'price' => 'Price',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\query\MajorIndexPriceHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\MajorIndexPriceHistoryQuery(get_called_class());
    }
}
