<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_share".
 *
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property float $price
 * @property int $quantity
 * @property string $date_buy
 * @property int $status_id
 * @property int|null $type 1 - Buy, 2 - Hold, 3 - Sell
 * @property string|null $date_sell
 * @property float|null $paid
 * @property float|null $profit
 *
 * @property Company $company
 * @property Status $status
 * @property User $user
 */
class OrderShare extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_share';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'company_id', 'price', 'quantity', 'status_id'], 'required'],
            [['user_id', 'company_id', 'quantity', 'status_id', 'type'], 'integer'],
            [['price', 'paid', 'profit'], 'number'],
            [['date_buy', 'date_sell'], 'safe'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'company_id' => 'Company ID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'date_buy' => 'Date Buy',
            'status_id' => 'Status ID',
            'type' => 'Type',
            'date_sell' => 'Date Sell',
            'paid' => 'Paid',
            'profit' => 'Profit',
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
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery|\app\query\StatusQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\OrderShareQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\OrderShareQuery(get_called_class());
    }
}
