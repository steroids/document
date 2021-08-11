<?php

namespace steroids\document\forms\meta;

use steroids\core\base\FormModel;
use \Yii;

abstract class DocumentUserCreateFormMeta extends FormModel
{
    public $refId;

    public function rules()
    {
        return [
            ...parent::rules(),
            ['refId', 'integer'],
        ];
    }

    public static function meta()
    {
        return [
            'refId' => [
                'isSortable' => false
            ]
        ];
    }
}
