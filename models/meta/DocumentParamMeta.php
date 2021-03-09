<?php

namespace steroids\document\models\meta;

use steroids\core\base\Model;
use steroids\document\enums\DocumentParamType;
use steroids\core\behaviors\TimestampBehavior;
use \Yii;
use yii\db\ActiveQuery;
use steroids\document\models\Document;

/**
 * @property string $id
 * @property integer $documentId
 * @property string $name
 * @property string $label
 * @property string $type
 * @property string $typeValues
 * @property boolean $isRequired
 * @property string $createTime
 * @property string $updateTime
 * @property-read Document $document
 */
abstract class DocumentParamMeta extends Model
{
    public static function tableName()
    {
        return 'document_params';
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'label',
            'type',
            'typeValues',
            'isRequired',
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            ['documentId', 'integer'],
            [['name', 'label'], 'string', 'max' => 255],
            [['name', 'label'], 'required'],
            ['type', 'in', 'range' => DocumentParamType::getKeys()],
            ['typeValues', 'string'],
            ['isRequired', 'steroids\\core\\validators\\ExtBooleanValidator'],
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
    public function getDocument()
    {
        return $this->hasOne(Document::class, ['id' => 'documentId']);
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('steroids', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => true
            ],
            'documentId' => [
                'label' => Yii::t('steroids', 'ИД документа'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Системное имя'),
                'hint' => Yii::t('steroids', 'латиницей'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'label' => [
                'label' => Yii::t('steroids', 'Название'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'type' => [
                'label' => Yii::t('steroids', 'Тип'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentParamType::class
            ],
            'typeValues' => [
                'label' => Yii::t('steroids', 'Значения списка'),
                'hint' => Yii::t('steroids', 'Каждое на новой строке'),
                'appType' => 'text',
                'isPublishToFrontend' => true
            ],
            'isRequired' => [
                'label' => Yii::t('steroids', 'Обязательное?'),
                'appType' => 'boolean',
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
            ]
        ]);
    }
}
