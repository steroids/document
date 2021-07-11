<?php

namespace steroids\document\models;

use steroids\document\DocumentModule;
use steroids\document\enums\DocumentParamType;
use steroids\document\models\meta\DocumentParamMeta;

class DocumentParam extends DocumentParamMeta
{
    /**
     * @inheritDoc
     */
    public static function instantiate($row)
    {
        return DocumentModule::instantiateClass(static::class, $row);
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            ['type', 'default', 'value' => DocumentParamType::STRING],
        ];
    }
}
