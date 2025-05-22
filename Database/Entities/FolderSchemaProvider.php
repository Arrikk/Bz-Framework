<?php

namespace Database\Entities;

use App\Models\Folder;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class FolderSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Folder::table());
        $table->addColumn('id', 'integer', [
            'unsigned' => true,
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('name', 'string', [
            'length' => 25,
            'notnull' => true
        ]);
        $table->addColumn('company_id', 'integer', [
            'notnull' => false
        ]);
        $table->addColumn('user_id', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('collaborators', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('shared', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('visibility', 'string', [
            'length' => 7,
            'default' => 'private',
            'notnull' => true
        ]);
        $table->addColumn('share_link', 'string', [
            'length' => 225,
            'notnull' => false
        ]);
        $table->addColumn('path', 'string', [
            'length' => 255,
            'notnull' => false
        ]);
        $table->addColumn('storage_size', 'bigint', [
            'notnull' => false
        ]);
        $table->addColumn('created_at', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'notnull' => true
        ]);
        $table->addColumn('updated_at', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'notnull' => true
        ]);
        $table->setPrimaryKey(['id']);
        return $schema;
    }
}