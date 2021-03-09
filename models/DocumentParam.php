<?php

namespace steroids\document\models;

use steroids\document\enums\DocumentParamType;
use steroids\document\models\meta\DocumentParamMeta;

class DocumentParam extends DocumentParamMeta
{
    public function rules()
    {
        return [
            ...parent::rules(),
            ['type', 'default', 'value' => DocumentParamType::STRING],
        ];
    }
}
