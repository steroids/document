<?php

namespace app\document\components;

use steroids\document\enums\DocumentOriginalStatus;
use steroids\document\enums\DocumentScanStatus;
use steroids\document\enums\DocumentSignMode;
use steroids\document\enums\DocumentSignStatus;
use steroids\document\enums\DocumentVerificationStatus;
use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use yii\base\BaseObject;
use yii\base\Exception;

class DocumentGroupedStatus extends BaseObject
{
    const ACTION_READ = 'read';
    const ACTION_SIGN = 'sign';
    const ACTION_UPLOAD_SCAN = 'upload_scan';
    const ACTION_PAYMENT = 'payment';

    /**
     * Статус
     * @var string|null
     */
    public ?string $statusLabel = null;

    /**
     * Заголовок действия
     * @var string|null
     */
    public ?string $actionLabel = null;

    /**
     * Системное название действия
     * @var string|null
     */
    public ?string $actionName = null;

    /**
     * Цвет (info, success, error)
     * @var string|null
     */
    public ?string $color = null;

    /**
     * Дата обновления статуса в формате YYYY-MM-DD
     * @var string|null
     */
    public ?string $date = null;

    /**
     * @param Document $document
     * @param DocumentUser|null $documentUser
     * @param null $contextUser
     * @return static|null
     * @throws Exception
     */
    public static function create(Document $document, DocumentUser $documentUser = null, $contextUser = null)
    {
        $successResult = null;

        // Отметить как прочитанное
        if ($document->isReadRequired) {
            if (!$documentUser || !$documentUser->isRead) {
                return new static([
                    'statusLabel' => \Yii::t('app', 'Необходимо ознакомиться'),
                    'actionLabel' => \Yii::t('app', 'Прочитал'),
                    'actionName' => self::ACTION_READ,
                    'color' => 'info',
                    'date' => null,
                ]);
            }

            $successResult = [
                'statusLabel' => \Yii::t('app', 'Прочитано'),
                'color' => 'success',
                'date' => date('Y-m-d', strtotime($documentUser->readTime)),
            ];
        }

        // Подписать по СМС
        if ($document->isSignRequired) {
            $isContextUserNotSigned = $contextUser && $documentUser && (
                    ($contextUser->id === $documentUser->userId && $documentUser->firstSignStatus !== DocumentSignStatus::SIGNED)
                    || $contextUser->id === $documentUser->secondUserId && $documentUser->secondSignStatus !== DocumentSignStatus::SIGNED);
            switch ($document->signMode) {
                // Подпись двумя пользователями
                case DocumentSignMode::TWO:
                    if (!$documentUser || $documentUser->firstSignStatus !== DocumentSignStatus::SIGNED
                        || $documentUser->secondSignStatus !== DocumentSignStatus::SIGNED) {
                        return new static([
                            'statusLabel' => $documentUser
                                ? ($isContextUserNotSigned
                                    ? \Yii::t('app', 'Ожидаем вашей подписи')
                                    : \Yii::t('app', 'Ожидаем подписи со второй стороны')
                                )
                                : \Yii::t('app', 'Ожидаем подписи с двух сторон'),
                            'actionLabel' => $isContextUserNotSigned ? \Yii::t('app', 'Подписать') : null,
                            'actionName' => $isContextUserNotSigned ? self::ACTION_SIGN : null,
                            'color' => 'info',
                            'date' => null,
                        ]);
                    }
                    break;

                // Подпись одним пользователем
                case DocumentSignMode::ONE:
                    if (!$documentUser || $documentUser->firstSignStatus !== DocumentSignStatus::SIGNED) {
                        return new static([
                            'statusLabel' => \Yii::t('app', 'Необходимо подписать по СМС'),
                            'actionLabel' => $isContextUserNotSigned ? \Yii::t('app', 'Подписать') : null,
                            'actionName' => $isContextUserNotSigned ? self::ACTION_SIGN : null,
                            'color' => 'info',
                            'date' => null,
                        ]);
                    }
                    break;

                default:
                    throw new Exception('Incorrect sign mode: ' . $document->signMode);
            }

            $successResult = [
                'statusLabel' => \Yii::t('app', 'Подписано СМС кодом'),
                'color' => 'success',
                'date' => date('Y-m-d', max(strtotime($documentUser->firstSignStatusTime), strtotime($documentUser->secondSignStatusTime))),
            ];
        }

        // Загрузка скана
        if ($document->isScanRequired) {
            if (!$documentUser || $documentUser->scanStatus !== DocumentScanStatus::ACCEPTED) {
                return new static([
                    'statusLabel' => $documentUser && $documentUser->scanStatus
                        ? DocumentScanStatus::getLabel($documentUser->scanStatus)
                        : \Yii::t('app', 'Необходимо загрузить скан'),
                    'actionLabel' => \Yii::t('app', 'Загрузить скан'),
                    'actionName' => self::ACTION_UPLOAD_SCAN,
                    'color' => $documentUser && $documentUser->scanStatus === DocumentScanStatus::REJECTED ? 'error' : 'info',
                    'date' => $documentUser && $documentUser->scanStatusTime ? date('Y-m-d', strtotime($documentUser->scanStatusTime)) : null,
                ]);
            }

            $successResult = [
                'statusLabel' => DocumentScanStatus::getLabel($documentUser->scanStatus),
                'color' => 'success',
                'date' => date('Y-m-d', strtotime($documentUser->scanStatusTime)),
            ];
        }

        // Верификация
        if ($documentUser && $document->isVerificationRequired) {
            if ($documentUser->verificationStatus !== DocumentVerificationStatus::ACCEPTED) {
                return new static([
                    'statusLabel' => $documentUser->verificationStatus
                        ? DocumentVerificationStatus::getLabel($documentUser->verificationStatus)
                        : \Yii::t('app', 'Требуется верификация'),
                    'color' => $documentUser->verificationStatus === DocumentVerificationStatus::REJECTED ? 'error' : 'info',
                    'date' => $documentUser->verificationStatusTime ? date('Y-m-d', strtotime($documentUser->verificationStatusTime)) : null,
                ]);
            }

            $successResult = [
                'statusLabel' => DocumentScanStatus::getLabel($documentUser->scanStatus),
                'color' => 'success',
                'date' => date('Y-m-d', strtotime($documentUser->verificationStatusTime)),
            ];
        }

        // Отправка оригинала
        if ($document->isOriginalRequired) {
            if (!$documentUser || $documentUser->originalStatus !== DocumentOriginalStatus::ACCEPTED) {
                return new static([
                    'statusLabel' => $documentUser && $documentUser->originalStatus
                        ? DocumentScanStatus::getLabel($documentUser->originalStatus)
                        : \Yii::t('app', 'Требуется оригинал'),
                    'color' => $documentUser && $documentUser->originalStatus === DocumentOriginalStatus::REJECTED ? 'error' : 'info',
                    'date' => $documentUser && $documentUser->originalStatusTime ? date('Y-m-d', strtotime($documentUser->originalStatusTime)) : null,
                ]);
            }

            $successResult = [
                'statusLabel' => DocumentScanStatus::getLabel($documentUser->originalStatus),
                'color' => 'success',
                'date' => date('Y-m-d', strtotime($documentUser->originalStatusTime)),
            ];
        }

        // Оплата
        if ($document->isPaymentRequired) {
            if (!$documentUser || !$documentUser->isPaid) {
                return new static([
                    'statusLabel' => \Yii::t('app', 'Требуется оплата'),
                    'color' => 'info',
                    'date' => null,
                    'actionLabel' => \Yii::t('app', 'Перейти к оплате'),
                    'actionName' => self::ACTION_PAYMENT,
                ]);
            }

            $successResult = [
                'statusLabel' => 'Оплачено',
                'color' => 'success',
                'date' => date('Y-m-d', strtotime($documentUser->paidTime)),
            ];
        }

        return $successResult ? new static($successResult) : null;
    }

}
