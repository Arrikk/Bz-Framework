<?php

namespace Database\Entities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class TransactionSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable('transactions');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('user_id', 'integer', [
            'notnull' => true
        ]);
        $table->addColumn('wallet_id', 'string', [
            'length' => 12,
            'default' => null,
        ]);
        $table->addColumn('transaction_type', 'string', [
            'length' => 6,
            'default' => null,
        ]);
        $table->addColumn('transaction_amount', 'float', [
            'default' => 0,
            'notnull' => true
        ]);
        $table->addColumn('balance_before', 'float', [
            'default' => 0,
            'notnull' => true
        ]);
        $table->addColumn('balance_after', 'float', [
            'default' => 0,
            'notnull' => true
        ]);
        $table->addColumn('transaction_reference', 'string', [
            'length' => 100,
            'default' => null,
        ]);
        $table->addColumn('transaction_meta', 'text', [
            'default' => null,
        ]);
        $table->addColumn('transaction_status', 'string', [
            'length' => 9,
            'default' => 'pending',
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