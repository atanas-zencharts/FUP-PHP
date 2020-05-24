<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\Cryptocurency]].
 *
 * @see \app\models\Cryptocurency
 */
class CryptocurencyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Cryptocurency[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Cryptocurency|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
