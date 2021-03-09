<?php

namespace steroids\document\models\meta;

use steroids\core\base\Model;
use steroids\core\behaviors\TimestampBehavior;
use \Yii;
use yii\db\ActiveQuery;
use steroids\document\models\DocumentCategory;

/**
 * @property string $id
 * @property integer $parentId
 * @property string $name
 * @property string $title
 * @property string $createTime
 * @property string $updateTime
 * @property-read DocumentCategory $parent
 */
abstract class DocumentCategoryMeta extends Model
{
    public static function tableName()
    {
        return 'document_categories';
    }

    public function fields()
    {
        return [
            'id',
            'parentId',
            'name',
            'title',
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            ['parentId', 'integer'],
            [['name', 'title'], 'string', 'max' => 255],
            [['name', 'title'], 'required'],
        ];
    }

    public function behaviors()
    {
        return [
            ...parent::behaviors(),
            TimestampBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(DocumentCategory::class, ['id' => 'parentId']);
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('app', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => true
            ],
            'parentId' => [
                'label' => Yii::t('app', 'Родительская категория'),
                'appType' => 'integer',
                'isPublishToFrontend' => true
            ],
            'name' => [
                'label' => Yii::t('app', 'Системное имя'),
                'hint' => Yii::t('app', 'латиницей'),
                'isRequired' => true,
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
