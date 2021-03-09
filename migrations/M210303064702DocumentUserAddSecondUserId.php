<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210303064702DocumentUserAddSecondUserId extends Migration
{
    public function safeUp()
    {
        $this->addColumn('document_users', 'secondUserId', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('document_users', 'secondUserId');
    }
}
