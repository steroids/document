<?php


namespace steroids\document\controllers;

use steroids\auth\forms\ConfirmForm;
use steroids\auth\models\AuthConfirm;
use steroids\document\forms\DocumentUploadScanForm;
use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use steroids\file\FileModule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class DocumentUserController extends Controller
{
    public static function apiMap()
    {
        return [
            'document-user' => [
                'items' => [
                    'create' => 'POST /api/v1/document/<name>',
                    'upload' => 'PUT /api/v1/document/<name>/upload',
                    'sign-start' => 'POST /api/v1/document/<uid>/sign-start',
                    'sign-confirm' => 'POST /api/v1/document/<uid>/sign-confirm',
                    'mark-read' => 'POST /api/v1/document/<uid>/mark-read',
                ],
            ],
        ];
    }

    public function actionCreate(string $name)
    {
        return DocumentUser::findOrCreate(\Yii::$app->user->model->primaryKey, $name);
    }

    public function actionUpload($name)
    {
        // Upload file
        $file = FileModule::getInstance()->upload();

        // Save scan
        $model = new DocumentUploadScanForm([
            'user' => \Yii::$app->user->model,
            'names' => [$name],
        ]);
        $model->scans[$name] = $file->id;
        $model->upload();

        return $file->getExtendedAttributes();
    }

    /**
     * @param string $uid
     * @return AuthConfirm|null
     * @throws NotFoundHttpException
     */
    public function actionSignStart(string $uid)
    {
        $model = DocumentUser::findOrPanic(['uid' => $uid]);
        return $model->signStart();
    }

    /**
     * @param string $uid
     * @return ConfirmForm
     * @throws NotFoundHttpException
     */
    public function actionSignConfirm(string $uid)
    {
        $documentUser = DocumentUser::findOrPanic(['uid' => $uid]);

        $model = new ConfirmForm();
        $model->login = $documentUser->firstSignConfirm->uid;
        $model->load(\Yii::$app->request->post());
        $model->confirm();

        if (!$model->hasErrors() && $model->confirm->isConfirmed) {
            $documentUser->signComplete();
        }

        return $model;
    }

    public function actionRead(string $uid)
    {
        $model = DocumentUser::findOrPanic(['uid' => $uid]);
        $model->markRead();
        return $model;
    }
}