<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentSignStatusMeta extends Enum
{
    const START = 'start';
    const SIGNED = 'signed';

    public static function getLabels()
    {
        return [
            self::START => Yii::t('app', 'СМС отправлено'),
            self::SIGNED => Yii::t('app', 'Подписано')
        ];
    }
}
