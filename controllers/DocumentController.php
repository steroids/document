<?php

namespace steroids\document\controllers;

use steroids\document\DocumentModule;
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
                    'download-user' => '/api/v1/document/download-user/<uid>',
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
    public function actionDownloadUser($uid)
    {
        $documentUserClass = DocumentModule::instantiateClass(DocumentUser::class);
        $documentUser = $documentUserClass::findOrPanic(['uid' => $uid]);
        return $this->response->sendFile($documentUser->download());
    }
}
