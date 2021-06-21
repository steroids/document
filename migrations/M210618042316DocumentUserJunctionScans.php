<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210618042316DocumentUserJunctionScans extends Migration
{
    public function safeUp()
    {
        $this->createTable('document_user_files_junction', [
            'fileId' => $this->integer()->notNull(),
            'documentUserId' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('document_user_files_junction');
    }
}
