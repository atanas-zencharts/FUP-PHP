<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\CompanyPriceHistory]].
 *
 * @see \app\models\CompanyPriceHistory
 */
class CompanyPriceHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\CompanyPriceHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\CompanyPriceHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
