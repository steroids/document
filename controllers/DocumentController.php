<?php

namespace steroids\document\controllers;

use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DocumentController extends Controller
{
    public static function apiMap()
    {
        return [
            'document' => [
                'items' => [
                    'download' => '/api/v1/document/download/<name>',
                    'download-user' => '/api/v1/document/download-user/<id:\d+>',
                ],
            ],
        ];
    }

    /**
     * @param $name
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionDownload($name)
    {
        $document = Document::getByName($name);
        return $this->response->sendFile($document->download());
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionDownloadUser($id)
    {
        $documentUser = DocumentUser::findOrPanic(['id' => (int)$id]);
        return $this->response->sendFile($documentUser->download());
    }
}
