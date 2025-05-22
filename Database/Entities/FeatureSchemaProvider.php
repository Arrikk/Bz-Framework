<?php

namespace Database\Entities;

use App\Models\Feature;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class FeatureSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Feature::table());
        $table->addColumn('id', 'integer', [
            'unsigned' => true,
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('feature_name', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('feature_key', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('feature_value', 'string', [
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