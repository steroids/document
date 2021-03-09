<?php

namespace steroids\document\migrations;

use steroids\core\base\Migration;

class M210302020824CreateDocument extends Migration
{
    public function safeUp()
    {
        $this->createTable('documents', [
            'id' => $this->primaryKey(),
            'fileId' => $this->integer(),
            'categoryId' => $this->integer(),
            'name' => $this->string(),
            'type' => $this->string(),
            'title' => $this->string(),
            'templateHtml' => $this->text(),
            'codePrefix' => $this->string(),
            'codeLastNumber' => $this->integer(),
            'signMode' => $this->string(),
            'isSignRequired' => $this->boolean(),
            'isScanRequired' => $this->boolean(),
            'isOriginalRequired' => $this->boolean(),
            'isVisible' => $this->boolean(),
            'versionTime' => $this->dateTime(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
        $this->createTable('document_categories', [
            'id' => $this->primaryKey(),
            'parentId' => $this->integer(),
            'name' => $this->string(),
            'title' => $this->string(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
        $this->createTable('document_params', [
            'id' => $this->primaryKey(),
            'documentId' => $this->integer(),
            'name' => $this->string(),
            'label' => $this->string(),
            'type' => $this->string(),
            'typeValues' => $this->text(),
            'isRequired' => $this->boolean(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
        $this->createTable('document_users', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(),
            'documentId' => $this->integer(),
            'userId' => $this->integer(),
            'refId' => $this->integer(),
            'codeNumber' => $this->integer(),
            'firstSignConfirmId' => $this->integer(),
            'firstSignStatus' => $this->string(),
            'firstSignStatusTime' => $this->dateTime(),
            'secondSignConfirmId' => $this->integer(),
            'secondSignStatus' => $this->string(),
            'secondSignStatusTime' => $this->dateTime(),
            'scanStatus' => $this->string(),
            'scanStatusTime' => $this->dateTime(),
            'originalStatus' => $this->string(),
            'originalStatusTime' => $this->dateTime(),
            'paramsJson' => $this->text(),
            'versionTime' => $this->dateTime(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);

        $this->createForeignKey('document_categories', 'parentId', 'document_categories', 'id');
        $this->createForeignKey('documents', 'fileId', '{{%files}}', 'id');
        $this->createForeignKey('documents', 'categoryId', 'document_categories', 'id');
        $this->createForeignKey('document_params', 'documentId', 'documents', 'id');
        $this->createForeignKey('document_users', 'documentId', 'documents', 'id');
        $this->createForeignKey('document_users', 'userId', 'users', 'id');
        $this->createForeignKey('document_users', 'firstSignConfirmId', 'auth_confirms', 'id');
        $this->createForeignKey('document_users', 'secondSignConfirmId', 'auth_confirms', 'id');
    }

    public function safeDown()
    {
        $this->deleteForeignKey('document_users', 'documentId', 'documents', 'id');
        $this->deleteForeignKey('document_users', 'userId', 'users', 'id');
        $this->deleteForeignKey('document_users', 'firstSignConfirmId', 'auth_confirms', 'id');
        $this->deleteForeignKey('document_users', 'secondSignConfirmId', 'auth_confirms', 'id');
        $this->deleteForeignKey('document_params', 'documentId', 'documents', 'id');
        $this->deleteForeignKey('document_categories', 'parentId', 'document_categories', 'id');
        $this->deleteForeignKey('documents', 'fileId', '{{%files}}', 'id');
        $this->deleteForeignKey('documents', 'categoryId', 'document_categories', 'id');

        $this->dropTable('document_users');
        $this->dropTable('document_params');
        $this->dropTable('document_categories');
        $this->dropTable('documents');
    }
}
