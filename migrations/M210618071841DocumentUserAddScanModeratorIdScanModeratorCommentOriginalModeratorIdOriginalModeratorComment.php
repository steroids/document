<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210618071841DocumentUserAddScanModeratorIdScanModeratorCommentOriginalModeratorIdOriginalModeratorComment extends Migration
{
    public function safeUp()
    {
        $this->addColumn('document_users', 'scanModeratorId', $this->integer());
        $this->addColumn('document_users', 'scanModeratorComment', $this->text());
        $this->addColumn('document_users', 'originalModeratorId', $this->integer());
        $this->addColumn('document_users', 'originalModeratorComment', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('document_users', 'scanModeratorId');
        $this->dropColumn('document_users', 'scanModeratorComment');
        $this->dropColumn('document_users', 'originalModeratorId');
        $this->dropColumn('document_users', 'originalModeratorComment');
    }
}
