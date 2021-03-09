<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentScanStatusMeta extends Enum
{
    const UPLOADED = 'uploaded';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    public static function getLabels()
    {
        return [
            self::UPLOADED => Yii::t('app', 'Загружен, на рассмотрении'),
            self::ACCEPTED => Yii::t('app', 'Скан принят'),
            self::REJECTED => Yii::t('app', 'Скан отклонен')
        ];
    }
}
