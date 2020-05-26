<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\ForexHistory]].
 *
 * @see \app\models\ForexHistory
 */
class ForexHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\ForexHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\ForexHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
