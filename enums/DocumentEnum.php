<?php

namespace steroids\document\enums;

use steroids\document\models\Document;
use steroids\core\base\Enum;
use yii\helpers\ArrayHelper;

class DocumentEnum extends Enum
{
    public static function getLabels()
    {
        return ArrayHelper::map(static::toFrontend(), 'id', 'label');
    }

    public static function toFrontend()
    {
        return array_map(
            function (Document $document) {
                return $document->toFrontend([
                    'id' => 'name',
                    'label' => 'title',
                ]);
            },
            Document::getAll()
        );
    }
}
