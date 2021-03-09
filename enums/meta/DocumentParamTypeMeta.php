<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentParamTypeMeta extends Enum
{
    const STRING = 'string';
    const INTEGER = 'integer';
    const BOOLEAN = 'boolean';
    const FILE = 'file';
    const LIST = 'list';
    const TEXT = 'text';
    const HTML = 'html';
    const FLOAT = 'float';

    public static function getLabels()
    {
        return [
            self::STRING => Yii::t('app', 'Строка'),
            self::INTEGER => Yii::t('app', 'Целое число'),
            self::BOOLEAN => Yii::t('app', 'Булев (чекбокс)'),
            self::FILE => Yii::t('app', 'Файл'),
            self::LIST => Yii::t('app', 'Список'),
            self::TEXT => Yii::t('app', 'Текст'),
            self::HTML => Yii::t('app', 'Контент (html)'),
            self::FLOAT => Yii::t('app', 'Дробное число')
        ];
    }
}
