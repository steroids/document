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
 * @property integer $position
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
            [['parentId', 'position'], 'integer'],
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
                'label' => Yii::t('steroids', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => true
            ],
            'parentId' => [
                'label' => Yii::t('steroids', 'Родительская категория'),
                'appType' => 'integer',
                'isPublishToFrontend' => true
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Системное имя'),
                'hint' => Yii::t('steroids', 'латиницей'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'title' => [
                'label' => Yii::t('steroids', 'Название'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'createTime' => [
                'label' => Yii::t('steroids', 'Добавлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => false,
                'touchOnUpdate' => false
            ],
            'updateTime' => [
                'label' => Yii::t('steroids', 'Обновлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => false,
                'touchOnUpdate' => true
            ],
            'position' => [
                'label' => Yii::t('steroids', 'Порядок'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ]
        ]);
    }
}
