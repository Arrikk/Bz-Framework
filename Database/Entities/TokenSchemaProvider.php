<?php

namespace Database\Entities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class TokenSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable('tokens');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('user_id', 'integer', [
            'notnull' => false
        ]);
        $table->addColumn('fcm', 'string', [
            'length' => 255,
            'notnull' => false
        ]);
        $table->addColumn('web', 'string', [
            'length' => 255,
            'notnull' => false
        ]);
        $table->addColumn('app', 'string', [
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