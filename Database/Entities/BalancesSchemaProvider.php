<?php
namespace Database\Entities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class BalancesSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        
        $table = $schema->createTable('balances');
        
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
        ]);
        
        $table->addColumn('user_id', 'integer', [
            'notnull' => true
        ]);
        
        $table->addColumn('wallet_id', 'string', [
            'length' => 20,
            'notnull' => true
        ]);
        
        $table->addColumn('wallet_balance', 'float', [
            'notnull' => true,
            'default' => 0
        ]);
        
        $table->addColumn('withdrawable_balance', 'float', [
            'notnull' => true,
            'default' => 0
        ]);
        
        $table->addColumn('non_withdrawable_balance', 'float', [
            'notnull' => true,
            'default' => 0
        ]);
        
        $table->addColumn('withdrwable_status', 'enum', [
            'values' => ['enabled', 'disabled'],
            'notnull' => true,
            'default' => 'enabled'
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
        $table->addIndex(['user_id'], 'idx_balances_user');
        $table->addIndex(['wallet_id'], 'idx_balances_wallet');
        
        return $schema;
    }
}