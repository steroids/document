<?php


namespace steroids\document\controllers;

use steroids\auth\forms\ConfirmForm;
use steroids\auth\models\AuthConfirm;
use steroids\document\forms\DocumentUploadScanForm;
use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use steroids\file\FileModule;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class DocumentUserController extends Controller
{
    public static function apiMap()
    {
        return [
            'document-user' => [
                'items' => [
                    'batch-sign-start' => 'POST /api/v1/document/batch-sign-start',
                    'batch-sign-confirm' => 'POST /api/v1/document/batch-sign-confirm',
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
        return $this->actionBatchSignStart($uid);
    }

    /**
     * @param string $uids
     * @return AuthConfirm|null
     * @throws NotFoundHttpException
     */
    public function actionBatchSignStart(string $uids)
    {
        $uids = StringHelper::explode($uids);

        // Start on first document
        $model = DocumentUser::findOrPanic(['uid' => array_shift($uids)]);
        $confirm = $model->signStart();

        // Other
        foreach ($uids as $uid) {
            DocumentUser::findOrPanic(['uid' => $uid])->signStart($confirm);
        }

        return $confirm;
    }

    /**
     * @param string $uid
     * @return ConfirmForm
     * @throws NotFoundHttpException
     */
    public function actionSignConfirm(string $uid)
    {
        return $this->actionBatchSignConfirm($uid);
    }

    /**
     * @param string $uids
     * @return ConfirmForm
     * @throws NotFoundHttpException
     */
    public function actionBatchSignConfirm(string $uids)
    {
        $uids = StringHelper::explode($uids);

        $anyDocumentUser = DocumentUser::findOrPanic(['uid' => $uids[0]]);

        $model = new ConfirmForm();
        $model->login = $anyDocumentUser->firstSignConfirm->uid;
        $model->load(\Yii::$app->request->post());
        $model->confirm();

        if (!$model->hasErrors() && $model->confirm->isConfirmed) {
            $documentUsers = DocumentUser::findAll(['uid' => $uids]);
            foreach ($documentUsers as $documentUser) {
                $documentUser->signComplete();
            }
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