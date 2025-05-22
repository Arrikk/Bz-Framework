<?php

namespace Database\Entities;

use App\Models\Setting;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class SettingSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Setting::table());
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('user_id', 'integer', [
            'notnull' => true
        ]);
        $table->addColumn('options', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('app_access', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('notification_settings', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('email_settings', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('stripe_settings', 'text', [
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