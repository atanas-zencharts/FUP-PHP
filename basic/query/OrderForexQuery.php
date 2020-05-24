<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\OrderForex]].
 *
 * @see \app\models\OrderForex
 */
class OrderForexQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\OrderForex[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\OrderForex|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
