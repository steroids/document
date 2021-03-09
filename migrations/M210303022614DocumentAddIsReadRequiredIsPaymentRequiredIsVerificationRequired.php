<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210303022614DocumentAddIsReadRequiredIsPaymentRequiredIsVerificationRequired extends Migration
{
    public function safeUp()
    {
        $this->addColumn('documents', 'isReadRequired', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('documents', 'isPaymentRequired', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('documents', 'isVerificationRequired', $this->boolean()->notNull()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('documents', 'isReadRequired');
        $this->dropColumn('documents', 'isPaymentRequired');
        $this->dropColumn('documents', 'isVerificationRequired');
    }
}
