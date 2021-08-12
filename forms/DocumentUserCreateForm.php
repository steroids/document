<?php

namespace steroids\document\forms;

use steroids\auth\UserInterface;
use steroids\core\exceptions\ModelSaveException;
use steroids\document\forms\meta\DocumentUserCreateFormMeta;
use steroids\document\models\Document;
use steroids\document\models\DocumentUser;
use yii\base\UnknownPropertyException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\IdentityInterface;

class DocumentUserCreateForm extends DocumentUserCreateFormMeta
{
    /**
     * @var Document
     */
    public $document;

    /**
     * @var UserInterface|IdentityInterface
     */
    public $user;

    /**
     * Any params to fill document params
     * @var array
     */
    public $params;

    /**
     * Param names from DocumentParam that isRequired. Used for 'required' validation rule
     * @var array
     */
    private $requiredParams = [];

    /**
     * Values of document params filled by user. Used for set 'paramsJson' in DocumentUser
     * @var array
     */
    private $userDocumentParams = [];

    /**
     * Redefine Component __set() base method for dynamically add attributes to Form
     * @param string $name
     * @param mixed $value
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        /**
         * If $name in list of DocumentParam names, then dynamically add this attribute to Form
         * And we can use validation rules from child Form in standard yii format
         * For other names use default yii __set method
         */
        $documentParamNames = ArrayHelper::getColumn($this->document->params, 'name');
        if (in_array($name, $documentParamNames)) {
            $this->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Add dynamically attributes to form from DocumentParam and set it by user values from $params
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        if ($this->document instanceof Document) {
            foreach ($this->document->params as $documentParam) {
                $attribute = $documentParam->name;
                // add new attribute and set value
                $this->$attribute = ArrayHelper::getValue($this->params, $attribute);
                // also add this value $userDocumentParams
                $this->userDocumentParams[$attribute] = $this->$attribute;
                if ($documentParam->isRequired) {
                    // fill requiredParams for add required rule
                    $this->requiredParams[] = $attribute;
                }
            }
        }
    }

    /**
     * Add required rule for DocumentParams that isRequired
     * and custom document params rules
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [[$this->requiredParams, 'required']],
            $this->prepareDocumentParamRules(),
        );
    }

    /**
     * Custom rules for document params
     * @return array
     */
    public function documentParamRules()
    {
        return [];
    }

    /**
     * @return DocumentUser|void
     * @throws ModelSaveException
     */
    public function create()
    {
        if ($this->validate()) {
            $userDocument = new DocumentUser([
                'documentId' => $this->document->getPrimaryKey(),
                'userId' => $this->user->getId(),
                'refId' => $this->refId,
                'paramsJson' => Json::encode($this->userDocumentParams),
            ]);
            $userDocument->saveOrPanic();
            return $userDocument;
        }

        return null;
    }

    /**
     * Parse documentParamRules() and take rules with attributes, that in our document param names only
     * For example:
     * Document param names are ['doc1', 'doc2'] and custom rules are
     * [
     *      [['doc1', 'otherDoc1', 'otherDoc2'], ... some_rule_1 ...],
     *      ['otherDocParam1', ... some_rule_2 ...],
     *      ['doc2', ... some_rule_3 ...],
     * ]
     * As result rules will
     * [
     *      ['doc1', ... some_rule_1...],
     *      ['doc2', ... some_rule_3 ...],
     * ]
     *
     * @return array
     */
    private function prepareDocumentParamRules()
    {
        $result = [];
        foreach ($this->documentParamRules() as $paramRule) {
            foreach ($this->document->params as $param) {
                // If document param is equal rule attribute or in attributes list, take it
                if ($param->name === $paramRule[0] || is_array($paramRule[0]) && in_array($param->name, $paramRule[0])) {
                    // Set rule attribute to param. Its if we have list of attributes in rule
                    $paramRule[0] = $param->name;
                    $result[] = $paramRule;
                }
            }
        }

        return $result;
    }
}
