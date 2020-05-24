<?php

namespace app\query;

/**
 * This is the ActiveQuery class for [[\app\models\Sector]].
 *
 * @see \app\models\Sector
 */
class SectorQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Sector[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Sector|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
