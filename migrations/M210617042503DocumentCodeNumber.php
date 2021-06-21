<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210617042503DocumentCodeNumber extends Migration
{
    public function safeUp()
    {
        $this->update('documents', ['codeLastNumber' => 0], ['codeLastNumber' => null]);
        $this->alterColumn('documents', 'codeLastNumber', $this->integer()->notNull()->defaultValue(0));
    }
}
