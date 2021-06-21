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
    public array $documents;

    /**
     * @var DocumentUser[]
     */
    public array $userDocuments = [];

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        // Preload documents models
        $this->documents = Document::find()
            ->where(['name' => $this->names])
            ->indexBy('name')
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_ASC,
            ])
            ->all();
        if (count($this->documents) !== count($this->names)) {
            throw new Exception('Cannot find documents: ' . array_diff($this->names, array_keys($this->documents)));
        }
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
                'scans' => function(DocumentUser $documentUser) {
                    return array_map(
                        function(File $file) {
                            $preview = $file->getImagePreview();
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

                // User document
                $documentUser = DocumentUser::findOrCreate($name, $this->user->getId());
                $documentUser->listenRelationIds('scans');

                // Save, if have changes
                if (count(array_diff($fileIds, $documentUser->scansIds)) > 0) {
                    $documentUser->scansIds = array_merge(
                        $fileIds,
                        $document->isScanMultiple
                            ? $documentUser->scansIds
                            : []
                    );
                    $documentUser->scanStatus = DocumentScanStatus::UPLOADED;
                    $documentUser->saveOrPanic();
                }

                $this->scans[$document->name] = $documentUser->scansIds;
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