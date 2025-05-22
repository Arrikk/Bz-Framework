<?php

namespace Database\Entities;

use App\Models\Storage;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class StorageSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Storage::table());
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('user_id', 'integer', [
            'notnull' => true
        ]);
        $table->addColumn('storage_space', 'bigint', [
            'notnull' => true
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