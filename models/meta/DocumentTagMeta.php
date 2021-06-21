<?php

namespace steroids\document\models\meta;

use steroids\core\base\Model;
use steroids\core\behaviors\TimestampBehavior;
use \Yii;

/**
 * @property string $id
 * @property string $name
 * @property string $title
 * @property string $createTime
 * @property string $updateTime
 */
abstract class DocumentTagMeta extends Model
{
    public static function tableName()
    {
        return 'document_tags';
    }

    public function fields()
    {
        return [
            'name',
            'title',
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            [['name', 'title'], 'string', 'max' => 255],
            ['title', 'required'],
        ];
    }

    public function behaviors()
    {
        return [
            ...parent::behaviors(),
            TimestampBehavior::class,
        ];
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('app', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => false
            ],
            'name' => [
                'label' => Yii::t('app', 'Имя латиницей'),
                'isPublishToFrontend' => true
            ],
            'title' => [
                'label' => Yii::t('app', 'Название'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'createTime' => [
                'label' => Yii::t('app', 'Добавлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => false,
                'touchOnUpdate' => false
            ],
            'updateTime' => [
                'label' => Yii::t('app', 'Обновлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => false,
                'touchOnUpdate' => true
            ]
        ]);
    }
}
