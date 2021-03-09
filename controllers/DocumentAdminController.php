<?php

namespace steroids\document\controllers;

use steroids\document\forms\DocumentSearch;
use steroids\document\models\Document;
use steroids\document\models\DocumentCategory;
use steroids\core\base\CrudApiController;
use steroids\file\FileModule;
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
            'categoryId',
            'name',
            'type',
            'title',
            'templateHtml',
            'codePrefix',
            'codeLastNumber',
            'signMode',
            'isSignRequired',
            'isScanRequired',
            'isOriginalRequired',
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
        ];
    }

    public function actionUpload()
    {
        return FileModule::getInstance()->upload()->getExtendedAttributes();
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
