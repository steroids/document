<?php

namespace steroids\document\controllers;

use steroids\document\forms\DocumentCategorySearch;
use steroids\document\models\DocumentCategory;
use steroids\core\base\CrudApiController;

class DocumentCategoryAdminController extends CrudApiController
{
    public static $modelClass = DocumentCategory::class;
    public static $searchModelClass = DocumentCategorySearch::class;

    public static function apiMap()
    {
        return [
            'admin.document-categories' => static::apiMapCrud('/api/v1/admin/document/categories', [
                'items' => [
                    'all' => '/api/v1/admin/document/categories/all',
                ]
            ]),
        ];
    }

    public function actionAll()
    {
        return DocumentCategory::find()
            ->select([
                'id',
                'label' => 'title',
            ])
            ->asArray()
            ->limit(1000)
            ->all();
    }
}
