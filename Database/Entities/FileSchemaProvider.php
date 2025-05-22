<?php

namespace Database\Entities;

use App\Models\File;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class FileSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(File::table());
        $table->addColumn('id', 'integer', [
            'unsigned' => true,
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('user_id', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('storage_size', 'float', [
            'notnull' => false
        ]);
        $table->addColumn('file_data', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('file_path', 'string', [
            'length' => 225,
            'notnull' => false
        ]);
        $table->addColumn('folder_id', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('deleted', 'string', [
            'length' => 3,
            'default' => 'no',
            'notnull' => true
        ]);
        $table->addColumn('status', 'string', [
            'length' => 8,
            'default' => 'enabled',
            'notnull' => false
        ]);
        $table->addColumn('shared', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('visibility', 'string', [
            'length' => 7,
            'notnull' => false
        ]);
        $table->addColumn('share_link', 'string', [
            'length' => 255,
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