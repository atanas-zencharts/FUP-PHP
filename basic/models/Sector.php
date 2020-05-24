<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sector".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property Company[] $companies
 */
class Sector extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sector';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 1000],
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
        return $this->hasMany(Company::className(), ['sector_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\query\SectorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\SectorQuery(get_called_class());
    }
}
