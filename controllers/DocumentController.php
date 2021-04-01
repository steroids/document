<?php

namespace steroids\document\controllers;

use steroids\document\DocumentModule;
use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DocumentController extends Controller
{
    public static function apiMap()
    {
        return [
            'document' => [
                'items' => [
                    'download' => '/backend/document/download/<name>',
                    'download-user' => '/backend/document/download-user/<uid>/<name>',
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
        // Remove extension
        $name = substr($name, 0, strrpos($name, '.'));

        $document = Document::getByName($name);
        return $this->response->sendFile($document->download(), $document->downloadName, ['inline' => true]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionDownloadUser($uid, $name)
    {
        $documentUserClass = DocumentModule::instantiateClass(DocumentUser::class);
        $documentUser = $documentUserClass::findOrPanic(['uid' => $uid]);
        if (YII_DEBUG && isset($_GET['html'])) {
            return $documentUser->download();
        }
        return $this->response->sendFile($documentUser->download(), $documentUser->downloadName, ['inline' => true]);
    }
}
