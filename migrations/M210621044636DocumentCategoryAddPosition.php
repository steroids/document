<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210621044636DocumentCategoryAddPosition extends Migration
{
    public function safeUp()
    {
        $this->addColumn('document_categories', 'position', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('document_categories', 'position');
    }
}
