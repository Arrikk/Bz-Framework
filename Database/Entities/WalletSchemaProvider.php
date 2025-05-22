<?php

namespace Database\Entities;

use App\Models\Wallet;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class WalletSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable('wallets');
        $table->addColumn('id', 'integer', [
            'unsigned' => true,
            'autoincrement' => true
        ]);
        $table->addColumn('wallet_id', 'string', [
            'length' => 20,
            'notnull' => true
        ]);
        $table->addColumn('wallet_name', 'string', [
            'length' => 30,
            'notnull' => true
        ]);
        $table->addColumn('wallet_symbol', 'string', [
            'length' => 10,
            'notnull' => true
        ]);
        $table->addColumn('wallet_decimal', 'integer', [
            'notnull' => true
        ]);
        $table->addColumn('wallet_description', 'string', [
            'length' => 100,
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
        $table->addColumn('status', 'string', [
            'length' => 8,
            'default' => 'enabled',
            'notnull' => true
        ]);
        $table->setPrimaryKey(['id']);
        return $schema;
    }
}