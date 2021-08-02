<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210729141154DocumentUserAddTerminationTime extends Migration
{
    public function safeUp()
    {
        $this->addColumn('document_users', 'terminationTime', $this->dateTime());
    }

    public function safeDown()
    {
        $this->dropColumn('document_users', 'terminationTime');
    }
}
