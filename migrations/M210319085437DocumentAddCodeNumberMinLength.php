<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210319085437DocumentAddCodeNumberMinLength extends Migration
{
    public function safeUp()
    {
        $this->addColumn('documents', 'codeNumberMinLength', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('documents', 'codeNumberMinLength');
    }
}
