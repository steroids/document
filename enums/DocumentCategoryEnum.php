<?php

namespace steroids\document\enums;

use steroids\document\models\DocumentCategory;
use steroids\core\base\Enum;
use yii\helpers\ArrayHelper;

class DocumentCategoryEnum extends Enum
{
    public static function getLabels()
    {
        return ArrayHelper::map(static::toFrontend(), 'id', 'label');
    }

    public static function toFrontend()
    {
        return array_map(
            function (DocumentCategory $document) {
                return $document->toFrontend([
                    'id' => 'name',
                    'label' => 'title',
                    'parentId',
                ]);
            },
            DocumentCategory::getAll()
        );
    }
}
