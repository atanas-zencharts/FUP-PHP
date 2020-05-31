<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\WalletHistoryTypes]].
 *
 * @see \app\models\WalletHistoryTypes
 */
class WalletHistoryTypesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\WalletHistoryTypes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\WalletHistoryTypes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
