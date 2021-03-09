<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentSignModeMeta extends Enum
{
    const ONE = 'one';
    const TWO = 'two';

    public static function getLabels()
    {
        return [
            self::ONE => Yii::t('app', 'Подпись с одной стороны'),
            self::TWO => Yii::t('app', 'Подпись двумя пользователями')
        ];
    }
}
