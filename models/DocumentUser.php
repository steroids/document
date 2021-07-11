<?php

namespace steroids\document\models;

use steroids\auth\AuthModule;
use steroids\auth\UserInterface;
use steroids\core\base\Model;
use steroids\core\behaviors\UidBehavior;
use steroids\document\components\DocumentGroupedStatus;
use steroids\document\components\DocumentHtmlToPdfBuilder;
use steroids\document\DocumentModule;
use steroids\document\enums\DocumentSignStatus;
use steroids\document\enums\DocumentType;
use steroids\document\exceptions\DocumentWrongFlowException;
use steroids\document\IDocumentReference;
use steroids\document\models\meta\DocumentUserMeta;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Class DocumentUser
 * @package steroids\document\models
 * @property-read array $groupedStatus
 * @property-read UserInterface|IdentityInterface $user
 * @property-read UserInterface|IdentityInterface $secondUser
 * @property-read Model|null $ref
 * @property-read array $params
 * @property-read array $userParams
 * @property-read array $refParams
 * @property-read string $code
 * @property-read string $downloadName
 */
class DocumentUser extends DocumentUserMeta
{
    /**
     * @var IDocumentReference|Model|null|bool
     */
    public $_ref = false;

    public static function findOrCreate(string $name, int $userId, $refId = null)
    {
        $document = Document::getByName($name);

        $params = [
            'documentId' => $document->primaryKey,
            'userId' => $userId,
            'refId' => $refId,
        ];
        $model = static::findOne($params);
        if (!$model) {
            $model = new static($params);
            $model->saveOrPanic();
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public static function instantiate($row)
    {
        return DocumentModule::instantiateClass(static::class, $row);
    }

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
                'code',
                'name' => 'document.name',
                'link',
                'groupedStatus',
            ]
        );
    }

    public function behaviors()
    {
        return [
            ...parent::behaviors(),
            UidBehavior::class,
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert || !$this->codeNumber) {
            $this->codeNumber = $this->document->codeLastNumber + 1;
            $this->document->codeLastNumber = $this->codeNumber;
            $this->document->saveOrPanic();
        }

        if (!$insert) {
            // Update time for appropriate attributes
            $timeAttributesMap = $this->getTimeAttributesMap();
            foreach ($this->dirtyAttributes as $attribute => $value) {
                if (array_key_exists($attribute, $timeAttributesMap)) {
                    $timeAttribute = $timeAttributesMap[$attribute];
                    $this->$timeAttribute = date('Y-m-d H:i:s');
                }
            }
        }

        return parent::beforeSave($insert);
    }

    public function getLink()
    {
        if ($this->isNewRecord) {
            return null;
        }
        if ($this->document->type === DocumentType::BLANK) {
            return count($this->scans) > 0 ? $this->scans[0]->url : null;
        }
        return \Yii::$app->params['backendOrigin'] . Url::to(['/document/document/download-user', 'uid' => $this->uid, 'name' => $this->downloadName]);
    }

    public function getDownloadName($suffix = '')
    {
        return $this->document->getDownloadName('_' . $this->codeNumber . $suffix);
    }

    public function download()
    {
        switch ($this->document->type) {
            case DocumentType::PDF:
                return $this->document->download();

            case DocumentType::TEMPLATE_HTML:
                return DocumentHtmlToPdfBuilder::build(
                    $this->document->name . '_' . $this->uid,
                    $this->document->templateHtml,
                    array_merge(
                        $this->userParams,
                        $this->refParams,
                        $this->params,
                    )
                );

            case DocumentType::BLANK:
                throw new Exception('Nothing to download. Document type - "blank"');
        }

        throw new Exception('Unsupported document type: ' . $this->document->type);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->paramsJson ? Json::decode($this->paramsJson) : [];
    }

    /**
     * @return array
     */
    public function getRefParams()
    {
        return $this->ref && $this->ref instanceof IDocumentReference ? $this->ref->getDocumentParams() : [];
    }

    /**
     * @return array
     */
    public function getUserParams()
    {
        return $this->user && $this->user instanceof IDocumentReference ? $this->user->getDocumentParams() : [];
    }

    /**
     * @return IDocumentReference|Model|null
     * @throws \yii\base\Exception
     */
    public function getRef()
    {
        if ($this->_ref === false) {
            /** @var Model $refClass */
            $refClass = ArrayHelper::getValue(DocumentModule::getInstance()->refClasses, $this->document->name);
            $this->_ref = $refClass && $this->refId ? $refClass::findOne(['id' => $this->refId]) : null;
        }
        return $this->_ref;
    }

    /**
     * @return ActiveQuery
     * @throws Exception
     */
    public function getUser()
    {
        return $this->hasOne(AuthModule::getInstance()->userClass, ['id' => 'userId']);
    }

    /**
     * @return ActiveQuery
     * @throws Exception
     */
    public function getSecondUser()
    {
        return $this->hasOne(AuthModule::getInstance()->userClass, ['id' => 'secondUserId']);
    }

    /**
     * @return DocumentGroupedStatus|null
     * @throws Exception
     */
    public function getGroupedStatus($user = null)
    {
        return DocumentGroupedStatus::create($this->document, $this, $user);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->document->codePrefix
            . str_pad((string)$this->codeNumber, $this->document->codeNumberMinLength, '0', STR_PAD_LEFT);
    }

    public function markRead()
    {
        if (!$this->document->isReadRequired) {
            throw new DocumentWrongFlowException('The document cannot be read');
        }

        if (!$this->isRead) {
            $this->isRead = true;
            $this->saveOrPanic();
        }
    }

    public function signStart($confirm = null)
    {
        if (!$this->document->isSignRequired) {
            throw new DocumentWrongFlowException('The document cannot be signed');
        }

        $confirm = $confirm ?: AuthModule::getInstance()->confirm($this->user);
        if ($confirm) {
            $this->firstSignConfirmId = $confirm->primaryKey;
            $this->firstSignStatus = DocumentSignStatus::START;
            $this->saveOrPanic();
        }
        return $confirm;
    }

    public function signComplete()
    {
        if (!$this->document->isSignRequired) {
            throw new DocumentWrongFlowException('The document cannot be signed');
        }

        if ($this->firstSignStatus === DocumentSignStatus::SIGNED) {
            return;
        }

        // TODO add for second user logic
        $this->firstSignStatus = DocumentSignStatus::SIGNED;
        $this->saveOrPanic();

        // Trigger event
        $this->trigger(DocumentModule::EVENT_ON_DOCUMENT_SIGN);
    }

    private function getTimeAttributesMap()
    {
        return [
            'firstSignStatus' => 'firstSignStatusTime',
            'scanStatus' => 'scanStatusTime',
            'secondSignStatus' => 'secondSignStatusTime',
            'originalStatus' => 'originalStatusTime',
            'verificationStatus' => 'verificationStatusTime',
            'isPaid' => 'paidTime',
            'isRead' => 'readTime',
        ];
    }
}
