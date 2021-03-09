<?php

namespace steroids\document;

use steroids\core\helpers\ModuleHelper;
use yii\base\Component;
use steroids\core\base\Module;

class DocumentModule extends Module
{
    public array $refClasses = [];

    public string $documentsDir = '@files/documents';
}
