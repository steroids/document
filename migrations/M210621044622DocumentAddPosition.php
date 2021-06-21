<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210621044622DocumentAddPosition extends Migration
{
    public function safeUp()
    {
        $this->addColumn('documents', 'position', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('documents', 'position');
    }
}
