<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210617094318DocumentAddIsScanMultiple extends Migration
{
    public function safeUp()
    {
        $this->addColumn('documents', 'isScanMultiple', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('documents', 'isScanMultiple');
    }
}
