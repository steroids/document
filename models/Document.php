<?php

namespace steroids\document\models;

use steroids\document\enums\DocumentType;
use steroids\document\models\meta\DocumentMeta;
use steroids\billing\models\BillingCurrency;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * Class Document
 * @package steroids\document\models
 */
class Document extends DocumentMeta
{
    private static ?array $_instances = null;

    /**
     * @param $name
     * @return static
     * @throws Exception
     */
    public static function getByName($name)
    {
        return static::findOrPanic(['name' => $name]);
    }

    /**
     * @return BillingCurrency[]
     */
    public static function getAll()
    {
        if (!static::$_instances) {
            static::$_instances = static::find()
                ->where(['isVisible' => true])
                ->indexBy('name')
                ->all();
        }
        return array_values(static::$_instances);
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ...parent::rules(),
            ['versionTime', 'default', 'value' => date('Y-m-d H:i:s')],
            ['codeLastNumber', 'default', 'value' => 0],
            [['!versionTime', '!codeLastNumber'], 'safe'],
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function download()
    {
        if (!$this->file) {
            throw new NotFoundHttpException('No file for document "' . $this->name . '"');
        }
        return $this->file->path;
    }
}
