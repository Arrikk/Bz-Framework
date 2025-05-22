<?php
namespace Database\Entities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class ActiveSessionsSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        
        $table = $schema->createTable('active_sessions');
        
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);
        
        $table->addColumn('user_id', 'integer', [
            'notnull' => true
        ]);
        
        $table->addColumn('token', 'text', [
            'notnull' => false
        ]);
        
        $table->addColumn('ip_address', 'string', [
            'length' => 20,
            'notnull' => false
        ]);
        
        $table->addColumn('location', 'string', [
            'length' => 225,
            'notnull' => false
        ]);
        
        $table->addColumn('device_type', 'string', [
            'length' => 225,
            'notnull' => false
        ]);
        
        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);
        
        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);
        
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_id'], 'idx_active_sessions_user');
        
        return $schema;
    }
}