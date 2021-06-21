<?php

namespace steroids\document\forms\meta;

use steroids\core\base\SearchModel;
use steroids\document\models\DocumentTag;
use \Yii;

abstract class DocumentTagsSearchMeta extends SearchModel
{
    public ?string $title = null;

    public function rules()
    {
        return [
            ...parent::rules(),
            ['title', 'string', 'max' => 255],
        ];
    }

    public function sortFields()
    {
        return [];
    }

    public function createQuery()
    {
        return DocumentTag::find();
    }

    public static function meta()
    {
        return [
            'title' => [
                'label' => Yii::t('app', 'Название'),
                'isSortable' => false
            ]
        ];
    }
}
