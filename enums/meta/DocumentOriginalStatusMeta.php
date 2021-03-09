<?php

namespace steroids\document\enums\meta;

use Yii;
use steroids\core\base\Enum;

abstract class DocumentOriginalStatusMeta extends Enum
{
    const SENT = 'sent';
    const RECEIVED = 'received';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    public static function getLabels()
    {
        return [
            self::SENT => Yii::t('app', 'Отправлен'),
            self::RECEIVED => Yii::t('app', 'Получен, на рассмотрении'),
            self::ACCEPTED => Yii::t('app', 'Оригинал принят'),
            self::REJECTED => Yii::t('app', 'Оригинал отклонен')
        ];
    }
}
