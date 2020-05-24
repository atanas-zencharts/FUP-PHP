<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\Forex]].
 *
 * @see \app\models\Forex
 */
class ForexQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Forex[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Forex|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
