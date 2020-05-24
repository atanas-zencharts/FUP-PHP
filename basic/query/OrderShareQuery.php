<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\OrderShare]].
 *
 * @see \app\models\OrderShare
 */
class OrderShareQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\OrderShare[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\OrderShare|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
