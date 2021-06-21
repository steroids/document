<?php

namespace steroids\document\models\meta;

use steroids\core\base\Model;
use steroids\document\enums\DocumentType;
use steroids\document\enums\DocumentSignMode;
use steroids\core\behaviors\TimestampBehavior;
use \Yii;
use yii\db\ActiveQuery;
use steroids\file\models\File;
use steroids\document\models\DocumentCategory;
use steroids\document\models\DocumentParam;
use steroids\document\models\DocumentTag;

/**
 * @property string $id
 * @property integer $fileId
 * @property integer $categoryId
 * @property string $name
 * @property string $type
 * @property string $title
 * @property string $templateHtml
 * @property string $codePrefix
 * @property integer $codeLastNumber
 * @property string $signMode
 * @property boolean $isSignRequired
 * @property boolean $isScanRequired
 * @property boolean $isOriginalRequired
 * @property boolean $isReadRequired
 * @property boolean $isPaymentRequired
 * @property boolean $isVerificationRequired
 * @property boolean $isVisible
 * @property string $versionTime
 * @property string $createTime
 * @property string $updateTime
 * @property integer $codeNumberMinLength
 * @property boolean $isScanMultiple
 * @property integer $position
 * @property-read File $file
 * @property-read DocumentCategory $category
 * @property-read DocumentParam[] $params
 * @property-read DocumentTag[] $tags
 */
abstract class DocumentMeta extends Model
{
    public static function tableName()
    {
        return 'documents';
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'type',
            'title',
            'signMode',
            'isSignRequired',
            'isScanRequired',
            'isOriginalRequired',
            'isReadRequired',
            'isPaymentRequired',
            'isVerificationRequired',
            'versionTime',
            'isScanMultiple',
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            [['fileId', 'categoryId', 'codeLastNumber', 'codeNumberMinLength', 'position'], 'integer'],
            [['name', 'title', 'codePrefix'], 'string', 'max' => 255],
            [['name', 'type', 'title'], 'required'],
            ['type', 'in', 'range' => DocumentType::getKeys()],
            ['templateHtml', 'string'],
            ['signMode', 'in', 'range' => DocumentSignMode::getKeys()],
            [['isSignRequired', 'isScanRequired', 'isOriginalRequired', 'isReadRequired', 'isPaymentRequired', 'isVerificationRequired', 'isVisible', 'isScanMultiple'], 'steroids\\core\\validators\\ExtBooleanValidator'],
            ['versionTime', 'date', 'format' => 'php:Y-m-d H:i:s'],
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
    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(DocumentCategory::class, ['id' => 'categoryId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany(DocumentParam::class, ['documentId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(DocumentTag::class, ['id' => 'documentId'])
            ->viaTable('document_tags_junction', ['tagId' => 'id']);
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('steroids', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => true
            ],
            'fileId' => [
                'label' => Yii::t('steroids', 'Файл'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'categoryId' => [
                'label' => Yii::t('steroids', 'Категория'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'name' => [
                'label' => Yii::t('steroids', 'Системное имя'),
                'hint' => Yii::t('steroids', 'латиницей'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'type' => [
                'label' => Yii::t('steroids', 'Тип документа'),
                'appType' => 'enum',
                'isRequired' => true,
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentType::class
            ],
            'title' => [
                'label' => Yii::t('steroids', 'Название'),
                'isRequired' => true,
                'isPublishToFrontend' => true
            ],
            'templateHtml' => [
                'label' => Yii::t('steroids', 'HTML шаблон'),
                'appType' => 'html',
                'isPublishToFrontend' => false
            ],
            'codePrefix' => [
                'label' => Yii::t('steroids', 'Префикс номера документа'),
                'isPublishToFrontend' => false
            ],
            'codeLastNumber' => [
                'label' => Yii::t('steroids', 'Последний номер документа'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'signMode' => [
                'label' => Yii::t('steroids', 'Режим подписи'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentSignMode::class
            ],
            'isSignRequired' => [
                'label' => Yii::t('steroids', 'Необходимо подписать'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isScanRequired' => [
                'label' => Yii::t('steroids', 'Необходимо загрузить скан'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isOriginalRequired' => [
                'label' => Yii::t('steroids', 'Необходимо отправить оригинал'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isReadRequired' => [
                'label' => Yii::t('steroids', 'Необходимо ознакомиться'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isPaymentRequired' => [
                'label' => Yii::t('steroids', 'Необходима оплата'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isVerificationRequired' => [
                'label' => Yii::t('steroids', 'Необходима проверка'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isVisible' => [
                'label' => Yii::t('steroids', 'Отображать на сайте?'),
                'appType' => 'boolean',
                'isPublishToFrontend' => false
            ],
            'versionTime' => [
                'label' => Yii::t('steroids', 'Временная метка версии'),
                'appType' => 'dateTime',
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
            'codeNumberMinLength' => [
                'label' => Yii::t('steroids', 'Минимальная длина номера документа'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'isScanMultiple' => [
                'label' => Yii::t('steroids', 'Можно загружать несколько сканов'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'position' => [
                'label' => Yii::t('steroids', 'Порядок'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ]
        ]);
    }
}
