<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property float $price
 * @property float $beta
 * @property int $volAvg
 * @property int $mktCap
 * @property float|null $lastDiv
 * @property string|null $range
 * @property float|null $changes
 * @property float|null $changePercentage
 * @property int|null $exchage_id
 * @property int|null $industry_id
 * @property int|null $sector_id
 * @property string|null $ceo
 * @property string|null $description
 * @property string|null $website
 * @property string|null $image
 * @property float|null $dayLow
 * @property float|null $dayHigh
 * @property float|null $open
 * @property float|null $previousDay
 *
 * @property Exchange $exchage
 * @property Industry $industry
 * @property Sector $sector
 * @property OrderShare[] $orderShares
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'symbol', 'price', 'beta', 'volAvg', 'mktCap'], 'required'],
            [['price', 'beta', 'lastDiv', 'changes', 'changePercentage', 'dayLow', 'dayHigh', 'open', 'previousDay'], 'number'],
            [['volAvg', 'mktCap', 'exchage_id', 'industry_id', 'sector_id'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 500],
            [['symbol', 'range'], 'string', 'max' => 255],
            [['ceo'], 'string', 'max' => 505],
            [['website', 'image'], 'string', 'max' => 2000],
            [['exchage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exchange::className(), 'targetAttribute' => ['exchage_id' => 'id']],
            [['industry_id'], 'exist', 'skipOnError' => true, 'targetClass' => Industry::className(), 'targetAttribute' => ['industry_id' => 'id']],
            [['sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sector::className(), 'targetAttribute' => ['sector_id' => 'id']],
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
            'beta' => 'Beta',
            'volAvg' => 'Vol Avg',
            'mktCap' => 'Mkt Cap',
            'lastDiv' => 'Last Div',
            'range' => 'Range',
            'changes' => 'Changes',
            'changePercentage' => 'Change Percentage',
            'exchage_id' => 'Exchage ID',
            'industry_id' => 'Industry ID',
            'sector_id' => 'Sector ID',
            'ceo' => 'Ceo',
            'description' => 'Description',
            'website' => 'Website',
            'image' => 'Image',
            'dayLow' => 'Day Low',
            'dayHigh' => 'Day High',
            'open' => 'Open',
            'previousDay' => 'Previous Day',
        ];
    }

    /**
     * Gets query for [[Exchage]].
     *
     * @return \yii\db\ActiveQuery|\app\query\ExchangeQuery
     */
    public function getExchage()
    {
        return $this->hasOne(Exchange::className(), ['id' => 'exchage_id']);
    }

    /**
     * Gets query for [[Industry]].
     *
     * @return \yii\db\ActiveQuery|\app\query\IndustryQuery
     */
    public function getIndustry()
    {
        return $this->hasOne(Industry::className(), ['id' => 'industry_id']);
    }

    /**
     * Gets query for [[Sector]].
     *
     * @return \yii\db\ActiveQuery|\app\query\SectorQuery
     */
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    /**
     * Gets query for [[OrderShares]].
     *
     * @return \yii\db\ActiveQuery|\app\query\OrderShareQuery
     */
    public function getOrderShares()
    {
        return $this->hasMany(OrderShare::className(), ['company_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\CompanyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\CompanyQuery(get_called_class());
    }
}
