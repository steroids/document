<?php

namespace steroids\document\forms\meta;

use steroids\core\base\SearchModel;
use \Yii;
use steroids\document\models\DocumentUser;

abstract class DocumentUserSearchMeta extends SearchModel
{
    /**
    * @var integer
    */
    public $categoryId;
    /**
    * @var string
    */
    public $title;
    /**
    * @var integer
    */
    public $codeNumber;
    /**
    * @var integer
    */
    public $userId;

    public function rules()
    {
        return [
            ...parent::rules(),
            [['categoryId', 'codeNumber', 'userId'], 'integer'],
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
                'label' => Yii::t('steroids', 'Категория'),
                'appType' => 'integer',
                'isSortable' => false
            ],
            'title' => [
                'label' => Yii::t('steroids', 'Название'),
                'isSortable' => false
            ],
            'codeNumber' => [
                'label' => Yii::t('steroids', 'Номер договора'),
                'appType' => 'integer',
                'isSortable' => false
            ],
            'userId' => [
                'label' => Yii::t('steroids', 'Пользователь'),
                'appType' => 'integer',
                'isSortable' => false
            ]
        ];
    }
}
