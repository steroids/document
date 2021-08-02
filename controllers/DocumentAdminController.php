<?php

namespace steroids\document\controllers;

use steroids\document\forms\DocumentSearch;
use steroids\document\models\Document;
use steroids\document\models\DocumentCategory;
use steroids\core\base\CrudApiController;
use steroids\document\models\DocumentUser;
use steroids\file\FileModule;
use steroids\file\models\File;
use yii\web\ForbiddenHttpException;

class DocumentAdminController extends CrudApiController
{
    public static $modelClass = Document::class;
    public static $searchModelClass = DocumentSearch::class;

    public static function apiMap()
    {
        return [
            'admin.document' => static::apiMapCrud('/api/v1/admin/document/documents', [
                'items' => [
                    'upload' => 'PUT /api/v1/admin/document/documents/upload',
                    'upload-editor' => 'POST /api/v1/admin/document/documents/upload-editor',
                ],
            ]),
        ];
    }

    /**
     * @return array|null
     */
    public function detailFields()
    {
        return [
            'id',
            'fileId',
            'file',
            'categoryId',
            'name',
            'type',
            'title',
            'templateHtml',
            'codePrefix',
            'codeLastNumber',
            'signMode',
            'isReadRequired',
            'isSignRequired',
            'isScanRequired',
            'isOriginalRequired',
            'isPaymentRequired',
            'isVerificationRequired',
            'isVisible',
            'versionTime',
            'createTime',
            'updateTime',
            'params' => [
                'name',
                'label',
                'type',
                'typeValues',
                'isRequired',
            ],
            'examples' => function(Document $document) {
                return DocumentUser::anyToFrontend(
                    DocumentUser::find()
                        ->where(['documentId' => $document->id])
                        ->orderBy(['id' => SORT_DESC])
                        ->limit(10)
                        ->all(),
                    [
                        'id',
                        'uid',
                        'code',
                        'codeNumber',
                        'link',
                    ]
                );
            }
        ];
    }

    public function actionUpload()
    {
        return FileModule::getInstance()->upload()->getExtendedAttributes();
    }

    public function actionUploadEditor()
    {
        /** @var File $file */
        $file = FileModule::getInstance()->upload();
        return $file->getImagePreview(FileModule::PREVIEW_FULLSCREEN)->toFrontend([
            'url',
        ]);
    }

    /**
     * @param Document $model
     * @param array $data
     * @throws ForbiddenHttpException
     */
    public function loadModel($model, $data)
    {
        $model->listenRelationData('params');

        if (isset($data['categoryName'])) {
            $data['categoryId'] = DocumentCategory::getByName($data['categoryName'])->id;
        }

        parent::loadModel($model, $data);

        if (isset($data['isVersionUpdate'])) {
            $model->versionTime = date('Y-m-d H:i:s');
        }
    }
}
