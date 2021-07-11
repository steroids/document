<?php

namespace steroids\document\forms;

use steroids\auth\UserInterface;
use steroids\document\enums\DocumentScanStatus;
use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use steroids\file\models\File;
use Yii;
use steroids\document\forms\meta\DocumentUploadScanFormMeta;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class DocumentUploadScanForm
 * @package steroids\document\forms
 * @property-read Document[] $documents
 */
class DocumentUploadScanForm extends DocumentUploadScanFormMeta
{
    /**
     * @var UserInterface
     */
    public $user;

    /**
     * @var string[]
     */
    public array $names = [];

    /**
     * @var string[]
     */
    public array $required = [];

    /**
     * @var array
     */
    public array $scans = [];

    /**
     * @var Document[]
     */
    public ?array $_documents = null;

    /**
     * @var DocumentUser[]
     */
    public array $userDocuments = [];

    /**
     * @var int|null
     */
    public ?int $refId = null;

    public function getDocuments()
    {
        if (!$this->_documents) {
            // Preload documents models
            $this->_documents = Document::find()
                ->where(['name' => $this->names])
                ->indexBy('name')
                ->orderBy([
                    'position' => SORT_ASC,
                    'id' => SORT_ASC,
                ])
                ->all();
            if (count($this->_documents) !== count($this->names)) {
                throw new Exception('Cannot find documents: ' . array_diff($this->names, array_keys($this->_documents)));
            }
        }
        return $this->_documents;
    }

    public function fields()
    {
        return [
            'userDocuments' => [
                'uid',
                'link',
                'document' => [
                    'id',
                    'type',
                    'name',
                    'title',
                    'file',
                    'link',
                    'isSignRequired',
                    'isScanRequired',
                    'isOriginalRequired',
                    'isReadRequired',
                    'isPaymentRequired',
                    'isVerificationRequired',
                    'isScanMultiple',
                    'category' => [
                        'name',
                        'title',
                    ],
                    'updateTime',
                    'versionTime',
                ],
                'groupedStatus',
                'scanStatus',
                'scanStatusTime',
                'scanModeratorComment',
                'scans' => function (DocumentUser $documentUser) {
                    return array_map(
                        function (File $file) {
                            $preview = $file->isImage() ? $file->getImagePreview() : null;
                            return array_merge($file->getExtendedAttributes(), [
                                'thumbnailUrl' => $preview ? $preview->url : null,
                            ]);
                        },
                        $documentUser->scans
                    );
                },
                'updateTime',
            ],
            'scans',
            'required',
        ];
    }

    /**
     * @throws \Exception
     */
    public function fetch()
    {
        // Find exists user documents
        /** @var DocumentUser[] $userDocuments */
        $userDocuments = DocumentUser::find()
            ->where([
                'userId' => $this->user->getId(),
                'documentId' => ArrayHelper::getColumn($this->documents, 'id'),
            ])
            ->with('scans')
            ->indexBy('documentId')
            ->all();

        // Store all user documents
        foreach ($this->documents as $document) {
            // Find DocumentUser
            $userDocument = ArrayHelper::getValue($userDocuments, $document->id);
            if (!$userDocument) {
                $userDocument = new DocumentUser([
                    'userId' => $this->user->getId(),
                    'documentId' => $document->id,
                ]);
            }
            $userDocument->populateRelation('document', $document);

            // Add ids value
            $this->scans[$document->name] = ArrayHelper::getColumn($userDocument->scans, 'id');

            // Add to frontend
            $this->userDocuments[] = $userDocument;
        }
    }

    /**
     * @throws \steroids\core\exceptions\ModelSaveException
     */
    public function upload()
    {
        if ($this->validate()) {
            foreach ($this->scans as $name => $fileIds) {
                // Force array
                if (!is_array($fileIds)) {
                    $fileIds = $fileIds ? [$fileIds] : [];
                }

                // Get document
                $document = $this->documents[$name];

                // User document params
                $documentParams = [
                    'documentId' => $document->primaryKey,
                    'userId' => $this->user->getId(),
                ];

                // User document
                $documentUser = DocumentUser::findOne($documentParams);
                if ($documentUser) {
                    $documentUser->listenRelationIds('scans');
                }
                $scansIds = $documentUser ? $documentUser->scansIds : [];

                // Save, if have changes
                if (count(array_diff($fileIds, $scansIds)) > 0) {
                    $scansIds = array_merge(
                        $fileIds,
                        $document->isScanMultiple
                            ? $scansIds
                            : []
                    );
                    if ($documentUser || count($scansIds) > 0) {
                        if (!$documentUser) {
                            $documentUser = new DocumentUser($documentParams);
                            $documentUser->listenRelationIds('scans');
                        }

                        $documentUser->scansIds = $scansIds;
                        $documentUser->refId = $this->refId ?: $documentUser->refId;
                        $documentUser->scanStatus = DocumentScanStatus::UPLOADED;
                        $documentUser->saveOrPanic();
                    }
                }

                $this->scans[$document->name] = $scansIds;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        // Check required
        foreach ($this->required as $name) {
            $fileIds = ArrayHelper::getValue($this->scans, $name);
            if (empty($fileIds)) {
                $this->addError($name, Yii::t('steroids', 'Обязательно к загрузке'));
            }
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function onUnsafeAttribute($name, $value)
    {
        if (in_array($name, $this->names)) {
            $this->scans[$name] = $value;
        } else {
            parent::onUnsafeAttribute($name, $value);
        }
    }
}
