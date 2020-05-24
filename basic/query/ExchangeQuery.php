<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\Exchange]].
 *
 * @see \app\models\Exchange
 */
class ExchangeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Exchange[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Exchange|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
