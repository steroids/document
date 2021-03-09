<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentVerificationStatusMeta extends Enum
{
    const CREATED = 'created';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    public static function getLabels()
    {
        return [
            self::CREATED => Yii::t('app', 'На верификации'),
            self::ACCEPTED => Yii::t('app', 'Верифицирован'),
            self::REJECTED => Yii::t('app', 'Отклонен')
        ];
    }
}
