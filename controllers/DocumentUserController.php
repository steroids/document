<?php


namespace steroids\document\controllers;

use steroids\auth\forms\ConfirmForm;
use steroids\auth\models\AuthConfirm;
use steroids\document\models\DocumentUser;
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