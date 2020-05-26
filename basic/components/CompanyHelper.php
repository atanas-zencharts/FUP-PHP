<?php

namespace app\components;

use app\models\Industry;
use app\models\Sector;
use Yii;
use yii\helpers\VarDumper;
use app\models\Exchange;

class CompanyHelper
{
    public static function getExchangeIdByName(string $name)
    {
        $trimmedName = trim($name);
        $query = Exchange::find()->andWhere(['trim(lower(name))' => strtolower($trimmedName)]);

        if (!$query->exists()) {
            CompanyHelper::createObject($trimmedName, Exchange::class);
        }
        $record = $query->one();
        return $record->id;
    }

    public static function getIndustryIdByName(string $name)
    {
        $trimmedName = trim($name);
        $query = Industry::find()->andWhere(['trim(lower(name))' => strtolower($trimmedName)]);

        if (!$query->exists()) {
            CompanyHelper::createObject($trimmedName, Industry::class);
        }
        $record = $query->one();
        return $record->id;
    }

    public static function getSectorIdByName(string $name)
    {
        $trimmedName = trim($name);
        $query = Sector::find()->andWhere(['trim(lower(name))' => strtolower($trimmedName)]);

        if (!$query->exists()) {
            CompanyHelper::createObject($trimmedName, Sector::class);
        }
        $record = $query->one();
        return $record->id;
    }

    private static function createObject(string $name, string $className)
    {
        $object = new $className();
        $object->name = $name;
        if (!$object->save()) {
            Yii::error(VarDumper::dumpAsString([
                $object->getErrors()
            ]));
        }
    }
}