<?php
namespace Database\Entities;

use App\Models\Referral;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

class ReferralSchemaProvider implements SchemaProvider {

    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(Referral::table());
        $table->addColumn('id', 'integer', [
            'unsigned' => true,
            'autoincrement' => true,
        ]);
        $table->addColumn('referred_id', 'integer', [
            'unsigned' => true,
            'notnull' => true
        ]);
        $table->addColumn('status', 'enum', [
            'values' => [PENDING, CONVERTED, EXPIRED],
            'notnull' => true,
            'default' => PENDING
        ]);
        $table->addColumn('source', 'enum', [
            'values' => ['email', 'direct', 'affiliate', 'social-media'],
            'notnull' => true,
            'default' => 'direct'
        ]);
        $table->addColumn('plan', 'string', [
            'length' => 20,
            'notnull' => true
        ]);
        $table->addColumn('commission', 'decimal', [
            'precision' => 10,
            'scale' => 2,
            'notnull' => true,
            'default' => 0.00
        ]);
        $table->addColumn('referrer_id', 'integer', [
            'unsigned' => true,
            'notnull' => true
        ]);
        $table->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        
        $table->setPrimaryKey(['id']);
        $table->addIndex(['referred_id'], 'idx_referral_referred');
        $table->addIndex(['referrer_id'], 'idx_referral_referrer');
        $table->addIndex(['status'], 'idx_referral_status');
        $table->addIndex(['plan'], 'idx_referral_plan');
        return $schema;
    }
}