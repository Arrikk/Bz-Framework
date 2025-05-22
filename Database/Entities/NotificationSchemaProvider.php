<?php

namespace Database\Entities;

use App\Models\Notification;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class NotificationSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Notification::table());
        $table->addColumn('id', 'integer', [
            'unsigned' => true,
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'integer', [
            'notnull' => true
        ]);
        $table->addColumn('from_id', 'string', [
            'length' => 80,
            'notnull' => false
        ]);
        $table->addColumn('to_id', 'string', [
            'length' => 80,
            'notnull' => false
        ]);
        $table->addColumn('message', 'string', [
            'length' => 50,
            'notnull' => false
        ]);
        $table->addColumn('category', 'string', [
            'length' => 20,
            'notnull' => false
        ]);
        $table->addColumn('status', 'string', [
            'length' => 7,
            'default' => 'unread',
            'notnull' => false
        ]);
        $table->addColumn('tag', 'string', [
            'length' => 25,
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