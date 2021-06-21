<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210617042502DocumentJunctionTags extends Migration
{
    public function safeUp()
    {
        $this->createTable('document_tags', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'title' => $this->string(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
        $this->createTable('document_tags_junction', [
            'documentId' => $this->integer()->notNull(),
            'tagId' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('document_tags_junction');
        $this->dropTable('document_tags');
    }
}
