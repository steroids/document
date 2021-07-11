<?php

namespace steroids\document\models;

use steroids\document\DocumentModule;
use steroids\document\models\meta\DocumentTagMeta;

class DocumentTag extends DocumentTagMeta
{
    /**
     * @inheritDoc
     */
    public static function instantiate($row)
    {
        return DocumentModule::instantiateClass(static::class, $row);
    }
}
