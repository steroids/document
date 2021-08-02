<?php

namespace steroids\document;

use steroids\core\helpers\ModuleHelper;
use steroids\document\components\DocumentBuilder;
use yii\base\Component;
use steroids\core\base\Module;

class DocumentModule extends Module
{
    const EVENT_ON_DOCUMENT_SIGN = 'on_document_sign';

    /**
     * @var array
     */
    public array $refClasses = [];

    /**
     * @var string
     */
    public string $documentsDir = '@files/documents';

    /**
     * @var int
     */
    public int $defaultCodeNumberMinLength = 6;

    /**
     * @var DocumentBuilder|array|null
     */
    public $builder;

    protected function coreComponents()
    {
        return [
            'builder' => [
                'class' => DocumentBuilder::class,
            ],
        ];
    }
}
