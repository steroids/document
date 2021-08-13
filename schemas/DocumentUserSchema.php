<?php

namespace steroids\document\schemas;

use steroids\core\base\BaseSchema;

class DocumentUserSchema extends BaseSchema
{
    /**
     * @return array
     */
    public function fields()
    {
        return $this->model ? $this->model->fields() : [];
    }
}