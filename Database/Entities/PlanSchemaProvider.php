<?php

namespace Database\Entities;

use App\Models\Plan;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class PlanSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Plan::table());
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('user_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_name', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_discount', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_features', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_amount', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_desc', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('plan_duration', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('amount_id', 'string', [
            'length' => 75,
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