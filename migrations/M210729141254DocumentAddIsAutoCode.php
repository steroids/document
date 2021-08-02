<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210729141254DocumentAddIsAutoCode extends Migration
{
    public function safeUp()
    {
        $this->addColumn('documents', 'isAutoCode', $this->boolean()->notNull()->defaultValue(false));
        $this->update('documents', ['isAutoCode' => true]);
    }

    public function safeDown()
    {
        $this->dropColumn('documents', 'isAutoCode');
    }
}
