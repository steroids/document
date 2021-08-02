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
                    'batch-sign-start' => 'POST /api/v1/document/user/batch-sign-start',
                    'batch-sign-confirm' => 'POST /api/v1/document/user/batch-sign-confirm',
                    'create' => 'POST /api/v1/document/<name>',
                    'upload' => 'PUT /api/v1/document/<name>/upload',
                    'user-get-last' => 'GET /api/v1/document/user/<name>/last',
                    'sign-start' => 'POST /api/v1/document/user/<uid>/sign-start',
                    'sign-confirm' => 'POST /api/v1/document/user/<uid>/sign-confirm',
                    'mark-read' => 'POST /api/v1/document/user/<uid>/mark-read',
                    'user-get' => 'GET /api/v1/document/user/<uid>',
                ],
            ],
        ];
    }

    /**
     * @param string $uid
     * @return DocumentUser|array
     */
    public function actionUserGet(string $uid)
    {
        return DocumentUser::findOrPanic(['uid' => $uid])->toFrontend([
            '*',
            'document',
        ]);
    }

    /**
     * @param string $name
     * @return array|DocumentUser|\yii\db\ActiveRecord|null
     * @throws \yii\base\Exception
     */
    public function actionUserGetLast(string $name)
    {
        $document = Document::getByName($name);
        if (!$document) {
            throw new NotFoundHttpException('Document not found: ' . $name);
        }

        $params = [
            'userId' => \Yii::$app->user->id,
            'documentId' => $document->id,
        ];

        $documentUser = DocumentUser::find()
            ->where($params)
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
        if (!$documentUser) {
            // Create blank
            $documentUser = new DocumentUser($params);
            $documentUser->populateRelation('document', $document);
        }
        return $documentUser->toFrontend([
            '*',
            'document',
        ]);
    }

    /**
     * @param string $name
     * @return DocumentUser|null
     */
    public function actionCreate(string $name)
    {
        return DocumentUser::findOrCreate($name, \Yii::$app->user->id);
    }

    /**
     * @param $name
     * @return array
     * @throws \steroids\core\exceptions\ModelSaveException
     * @throws \steroids\file\exceptions\FileException
     * @throws \steroids\file\exceptions\FileUserException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
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