<?php

namespace steroids\document\controllers;

use steroids\document\forms\DocumentTagsSearch;
use steroids\core\base\CrudApiController;
use steroids\document\models\DocumentTag;

class DocumentTagsAdminController extends CrudApiController
{
    public static $modelClass = DocumentTag::class;
    public static $searchModelClass = DocumentTagsSearch::class;

    public static function apiMap()
    {
        return [
            'admin.document-tags' => static::apiMapCrud('/api/v1/admin/document/tags', [
                'items' => [
                    'all' => '/api/v1/admin/document/tags/all',
                ]
            ]),
        ];
    }

    public function actionAll()
    {
        return DocumentTag::find()
            ->select([
                'id',
                'label' => 'title',
            ])
            ->asArray()
            ->limit(1000)
            ->all();
    }
}
