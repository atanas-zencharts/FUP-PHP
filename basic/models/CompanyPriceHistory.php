<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_price_history".
 *
 * @property int $id
 * @property string|null $date
 * @property float|null $price
 * @property float|null $dayLow
 * @property float|null $dayHigh
 * @property float|null $open
 * @property float|null $previousDay
 * @property int|null $company_id
 *
 * @property Company $company
 */
class CompanyPriceHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_price_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['price', 'dayLow', 'dayHigh', 'open', 'previousDay'], 'number'],
            [['company_id'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
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
            'dayLow' => 'Day Low',
            'dayHigh' => 'Day High',
            'open' => 'Open',
            'previousDay' => 'Previous Day',
            'company_id' => 'Company ID',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery|\app\query\CompanyQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\CompanyPriceHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\CompanyPriceHistoryQuery(get_called_class());
    }
}
