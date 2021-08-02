<?php

namespace steroids\document\models\meta;

use steroids\core\base\Model;
use steroids\document\enums\DocumentSignStatus;
use steroids\document\enums\DocumentScanStatus;
use steroids\document\enums\DocumentOriginalStatus;
use steroids\document\enums\DocumentVerificationStatus;
use steroids\core\behaviors\TimestampBehavior;
use \Yii;
use yii\db\ActiveQuery;
use steroids\document\models\Document;
use steroids\auth\models\AuthConfirm;
use steroids\file\models\File;

/**
 * @property string $id
 * @property string $uid
 * @property integer $documentId
 * @property integer $userId
 * @property integer $refId
 * @property integer $codeNumber
 * @property integer $firstSignConfirmId
 * @property string $firstSignStatus
 * @property string $firstSignStatusTime
 * @property integer $secondSignConfirmId
 * @property string $secondSignStatus
 * @property string $secondSignStatusTime
 * @property string $scanStatus
 * @property string $scanStatusTime
 * @property string $originalStatus
 * @property string $originalStatusTime
 * @property string $paramsJson
 * @property string $versionTime
 * @property string $createTime
 * @property string $updateTime
 * @property boolean $isRead
 * @property boolean $isPaid
 * @property string $verificationStatus
 * @property string $verificationStatusTime
 * @property string $paidTime
 * @property string $readTime
 * @property integer $secondUserId
 * @property integer $scanModeratorId
 * @property string $scanModeratorComment
 * @property integer $originalModeratorId
 * @property string $originalModeratorComment
 * @property string $terminationTime
 * @property-read Document $document
 * @property-read AuthConfirm $firstSignConfirm
 * @property-read AuthConfirm $secondSignConfirm
 * @property-read File[] $scans
 */
abstract class DocumentUserMeta extends Model
{
    public array $scansIds = [];

    public static function tableName()
    {
        return 'document_users';
    }

    public function fields()
    {
        return [
            'id',
            'uid',
            'codeNumber',
            'firstSignStatus',
            'firstSignStatusTime',
            'secondSignStatus',
            'secondSignStatusTime',
            'scanStatus',
            'scanStatusTime',
            'originalStatus',
            'originalStatusTime',
            'paramsJson',
            'versionTime',
            'createTime',
            'updateTime',
            'isRead',
            'isPaid',
            'verificationStatus',
            'verificationStatusTime',
            'paidTime',
            'readTime',
            'scanModeratorId',
            'scanModeratorComment',
            'originalModeratorId',
            'originalModeratorComment',
            'terminationTime',
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            ['uid', 'string', 'max' => 255],
            [['documentId', 'userId', 'refId', 'codeNumber', 'firstSignConfirmId', 'secondSignConfirmId', 'secondUserId', 'scanModeratorId', 'originalModeratorId'], 'integer'],
            [['firstSignStatus', 'secondSignStatus'], 'in', 'range' => DocumentSignStatus::getKeys()],
            [['firstSignStatusTime', 'secondSignStatusTime', 'scanStatusTime', 'originalStatusTime', 'versionTime', 'verificationStatusTime', 'paidTime', 'readTime', 'terminationTime'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['scanStatus', 'in', 'range' => DocumentScanStatus::getKeys()],
            ['originalStatus', 'in', 'range' => DocumentOriginalStatus::getKeys()],
            [['paramsJson', 'scanModeratorComment', 'originalModeratorComment'], 'string'],
            [['isRead', 'isPaid'], 'steroids\\core\\validators\\ExtBooleanValidator'],
            ['verificationStatus', 'in', 'range' => DocumentVerificationStatus::getKeys()],
            ['scansIds', 'each', 'rule' => ['integer']],
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

    /**
     * @return ActiveQuery
     */
    public function getFirstSignConfirm()
    {
        return $this->hasOne(AuthConfirm::class, ['id' => 'firstSignConfirmId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSecondSignConfirm()
    {
        return $this->hasOne(AuthConfirm::class, ['id' => 'secondSignConfirmId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getScans()
    {
        return $this->hasMany(File::class, ['id' => 'fileId'])
            ->viaTable('document_user_files_junction', ['documentUserId' => 'id']);
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('steroids', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => true
            ],
            'uid' => [
                'label' => Yii::t('steroids', 'Uid'),
                'isPublishToFrontend' => true
            ],
            'documentId' => [
                'label' => Yii::t('steroids', 'ИД документа'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'userId' => [
                'label' => Yii::t('steroids', 'Пользователь'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'refId' => [
                'label' => Yii::t('steroids', 'Реф ИД'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'codeNumber' => [
                'label' => Yii::t('steroids', 'Номер договора'),
                'appType' => 'integer',
                'isPublishToFrontend' => true
            ],
            'firstSignConfirmId' => [
                'label' => Yii::t('steroids', 'ИД подтверждения первой подписи'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'firstSignStatus' => [
                'label' => Yii::t('steroids', 'Статус первой подписи'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentSignStatus::class
            ],
            'firstSignStatusTime' => [
                'label' => Yii::t('steroids', 'Время изменения статуса первой подписи'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'secondSignConfirmId' => [
                'label' => Yii::t('steroids', 'ИД подтверждения второй подписи'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'secondSignStatus' => [
                'label' => Yii::t('steroids', 'Статус второй подписи'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentSignStatus::class
            ],
            'secondSignStatusTime' => [
                'label' => Yii::t('steroids', 'Время изменения статуса второй подписи'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'scanStatus' => [
                'label' => Yii::t('steroids', 'Статус загрузки скана'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentScanStatus::class
            ],
            'scanStatusTime' => [
                'label' => Yii::t('steroids', 'Время изменения статуса загрузки скана'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'originalStatus' => [
                'label' => Yii::t('steroids', 'Статус отправки оригинала'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentOriginalStatus::class
            ],
            'originalStatusTime' => [
                'label' => Yii::t('steroids', 'Время изменения статуса отправки оригинала'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'paramsJson' => [
                'label' => Yii::t('steroids', 'Параметры'),
                'appType' => 'text',
                'isPublishToFrontend' => true
            ],
            'versionTime' => [
                'label' => Yii::t('steroids', 'Временная метка версии'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'createTime' => [
                'label' => Yii::t('steroids', 'Добавлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => true,
                'touchOnUpdate' => false
            ],
            'updateTime' => [
                'label' => Yii::t('steroids', 'Обновлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => true,
                'touchOnUpdate' => true
            ],
            'isRead' => [
                'label' => Yii::t('steroids', 'Прочитан?'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'isPaid' => [
                'label' => Yii::t('steroids', 'Оплачен?'),
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'verificationStatus' => [
                'label' => Yii::t('steroids', 'Статус верификации'),
                'appType' => 'enum',
                'isPublishToFrontend' => true,
                'enumClassName' => DocumentVerificationStatus::class
            ],
            'verificationStatusTime' => [
                'label' => Yii::t('steroids', 'Время изменения статуса верификации'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'paidTime' => [
                'label' => Yii::t('steroids', 'Время оплаты'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'readTime' => [
                'label' => Yii::t('steroids', 'Время прочтения'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ],
            'secondUserId' => [
                'label' => Yii::t('steroids', 'Второй пользователь'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'scansIds' => [
                'label' => Yii::t('steroids', 'Загруженные сканы'),
                'appType' => 'files',
                'isPublishToFrontend' => false,
                'relationName' => 'scans'
            ],
            'scanModeratorId' => [
                'label' => Yii::t('steroids', 'Модератор скана'),
                'appType' => 'integer',
                'isPublishToFrontend' => true
            ],
            'scanModeratorComment' => [
                'label' => Yii::t('steroids', 'Комментарий модератора по скану'),
                'appType' => 'text',
                'isPublishToFrontend' => true
            ],
            'originalModeratorId' => [
                'label' => Yii::t('steroids', 'Модератор оригинала'),
                'appType' => 'integer',
                'isPublishToFrontend' => true
            ],
            'originalModeratorComment' => [
                'label' => Yii::t('steroids', 'Комментарий модератора по оригиналу'),
                'appType' => 'text',
                'isPublishToFrontend' => true
            ],
            'terminationTime' => [
                'label' => Yii::t('steroids', 'Дата расторжения договора'),
                'appType' => 'dateTime',
                'isPublishToFrontend' => true
            ]
        ]);
    }
}
