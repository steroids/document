<?php

namespace steroids\document\models;

use steroids\document\DocumentModule;
use steroids\document\models\meta\DocumentCategoryMeta;
use steroids\billing\models\BillingCurrency;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class DocumentCategory extends DocumentCategoryMeta
{
    private static ?array $_instances = null;

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public static function getByName($name)
    {
        // Lazy fetch
        static::getAll();

        $model = ArrayHelper::getValue(static::$_instances, $name);
        if (!$model) {
            throw new Exception('Not found document category by name: ' . $name);
        }

        return $model;
    }

    /**
     * @return BillingCurrency[]
     */
    public static function getAll()
    {
        if (!static::$_instances) {
            static::$_instances = static::find()->indexBy('name')->all();
        }
        return array_values(static::$_instances);
    }

    /**
     * @inheritDoc
     */
    public static function instantiate($row)
    {
        return DocumentModule::instantiateClass(static::class, $row);
    }
}
