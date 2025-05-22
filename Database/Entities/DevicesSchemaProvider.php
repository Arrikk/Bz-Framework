<?php
namespace Database\Entities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class DevicesSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        
        $table = $schema->createTable('devices');
        
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);
        
        $table->addColumn('_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        
        $table->addColumn('user_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        
        $table->addColumn('device_token', 'text', [
            'notnull' => false
        ]);
        
        $table->addColumn('device_ip', 'string', [
            'length' => 100,
            'notnull' => false
        ]);
        
        $table->addColumn('device_browser', 'string', [
            'length' => 100,
            'notnull' => false
        ]);
        
        $table->addColumn('device_os', 'string', [
            'length' => 100,
            'notnull' => false
        ]);
        
        $table->addColumn('device_type', 'string', [
            'length' => 100,
            'notnull' => false
        ]);
        
        $table->addColumn('status', 'enum', [
            'values' => ['active', 'inactive'],
            'notnull' => true,
            'default' => 'inactive'
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
        $table->addIndex(['_id'], 'idx_devices_id');
        $table->addIndex(['user_id'], 'idx_devices_user');
        $table->addIndex(['status'], 'idx_devices_status');
        
        return $schema;
    }
}