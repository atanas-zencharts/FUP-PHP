<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_call_history".
 *
 * @property int $id
 * @property string $date
 * @property string $action
 */
class ApiCallHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_call_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['action'], 'required'],
            [['action'], 'string', 'max' => 255],
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
            'action' => 'Action',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\query\ApiCallHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\query\ApiCallHistoryQuery(get_called_class());
    }
}
