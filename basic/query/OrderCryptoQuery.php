<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\OrderCrypto]].
 *
 * @see \app\models\OrderCrypto
 */
class OrderCryptoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\OrderCrypto[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\OrderCrypto|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
