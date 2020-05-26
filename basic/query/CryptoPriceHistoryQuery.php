<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\CryptoPriceHistory]].
 *
 * @see \app\models\CryptoPriceHistory
 */
class CryptoPriceHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\CryptoPriceHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\CryptoPriceHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
