<?php

namespace steroids\document\models;

use app\document\components\DocumentGroupedStatus;
use app\helpers\components\DocumentHtmlToPdfBuilder;
use steroids\auth\AuthModule;
use steroids\auth\UserInterface;
use steroids\core\base\Model;
use steroids\core\behaviors\UidBehavior;
use steroids\document\DocumentModule;
use steroids\document\enums\DocumentType;
use steroids\document\IDocumentReference;
use steroids\document\models\meta\DocumentUserMeta;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
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
 */
class DocumentUser extends DocumentUserMeta
{
    /**
     * @var IDocumentReference|Model|null|bool
     */
    public $_ref = false;

    public function fields()
    {
        return array_merge(
            parent::fields(),
            [
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
}
