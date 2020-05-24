<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\ApiCallHistory]].
 *
 * @see \app\models\ApiCallHistory
 */
class ApiCallHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\ApiCallHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\ApiCallHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
