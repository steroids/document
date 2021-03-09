<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210303030801DocumentUserAddIsReadIsPaidVerificationStatusVerificationStatusTime extends Migration
{
    public function safeUp()
    {
        $this->addColumn('document_users', 'isRead', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('document_users', 'isPaid', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('document_users', 'verificationStatus', $this->string());
        $this->addColumn('document_users', 'verificationStatusTime', $this->dateTime());
        $this->addColumn('document_users', 'paidTime', $this->dateTime());
        $this->addColumn('document_users', 'readTime', $this->dateTime());
    }

    public function safeDown()
    {
        $this->dropColumn('document_users', 'isRead');
        $this->dropColumn('document_users', 'isPaid');
        $this->dropColumn('document_users', 'verificationStatus');
        $this->dropColumn('document_users', 'verificationStatusTime');
        $this->dropColumn('document_users', 'paidTime');
        $this->dropColumn('document_users', 'readTime');
    }
}
