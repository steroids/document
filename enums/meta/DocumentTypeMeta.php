<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentTypeMeta extends Enum
{
    const BLANK = 'blank';
    const PDF = 'pdf';
    const TEMPLATE_HTML = 'template_html';

    public static function getLabels()
    {
        return [
            self::BLANK => Yii::t('app', 'Пустой'),
            self::PDF => Yii::t('app', 'PDF'),
            self::TEMPLATE_HTML => Yii::t('app', 'HTML шаблон')
        ];
    }
}
