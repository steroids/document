<?php

namespace steroids\document\forms\meta;

use \Yii;
use steroids\core\base\SearchModel;
use steroids\document\enums\DocumentType;
use steroids\document\models\Document;

abstract class DocumentSearchMeta extends SearchModel
{
    public ?string $title = null;
    public ?string $type = null;
    public ?string $categoryName = null;

    public function rules()
    {
        return [
            ...parent::rules(),
            ['title', 'string', 'max' => 255],
            ['type', 'in', 'range' => DocumentType::getKeys()],
            ['categoryName', 'string'],
        ];
    }

    public function sortFields()
    {
        return [];
    }

    public function createQuery()
    {
        return Document::find();
    }

    public static function meta()
    {
        return [
            'title' => [
                'label' => Yii::t('app', 'Название'),
                'isSortable' => false
            ],
            'type' => [
                'label' => Yii::t('app', 'Тип'),
                'appType' => 'enum',
                'isSortable' => false,
                'enumClassName' => DocumentType::class
            ],
            'categoryName' => [
                'label' => Yii::t('app', 'Категория'),
                'appType' => 'string',
                'isSortable' => false
            ]
        ];
    }
}
