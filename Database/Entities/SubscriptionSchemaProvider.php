<?php

namespace Database\Entities;

use App\Models\Subscription;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class SubscriptionSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Subscription::table());
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('status', 'string', [
            'length' => 50,
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
        $table->addColumn('stripe_subscription_id', 'string', [
            'length' => 75,
            'notnull' => false,
            'default' => null
        ]);
        $table->addColumn('stripe_customer_id', 'string', [
            'length' => 75,
            'notnull' => false,
            'default' => null
        ]);
        $table->addColumn('stripe_session_id', 'string', [
            'length' => 75,
            'notnull' => false,
            'default' => null
        ]);
        $table->addColumn('stripe_session_data', 'text', [
            'notnull' => false,
            'default' => null
        ]);
        $table->addColumn('subscription_on', 'string', [
            'length' => 30,
            'notnull' => false
        ]);
        $table->addColumn('subscription_expiry', 'string', [
            'length' => 30,
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