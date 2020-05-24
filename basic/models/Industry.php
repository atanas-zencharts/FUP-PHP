<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "industry".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property Company[] $companies
 */
class Industry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'industry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery|\app\query\CompanyQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['industry_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\IndustryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\IndustryQuery(get_called_class());
    }
}
