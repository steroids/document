<?php

namespace steroids\document\forms\meta;

use steroids\core\base\SearchModel;
use \Yii;
use steroids\document\models\DocumentUser;

abstract class DocumentUserSearchMeta extends SearchModel
{
    public ?int $categoryId = null;
    public ?string $title = null;
    public ?int $codeNumber = null;

    public function rules()
    {
        return [
            ...parent::rules(),
            [['categoryId', 'codeNumber'], 'integer'],
            ['title', 'string', 'max' => 255],
        ];
    }

    public function sortFields()
    {
        return [];
    }

    public function createQuery()
    {
        return DocumentUser::find();
    }

    public static function meta()
    {
        return [
            'categoryId' => [
                'label' => Yii::t('app', 'Категория'),
                'appType' => 'integer',
                'isSortable' => false
            ],
            'title' => [
                'label' => Yii::t('app', 'Название'),
                'isSortable' => false
            ],
            'codeNumber' => [
                'label' => Yii::t('app', 'Номер договора'),
                'appType' => 'integer',
                'isSortable' => false
            ]
        ];
    }
}
