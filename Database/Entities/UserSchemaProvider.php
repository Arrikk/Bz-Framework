<?php

namespace Database\Entities;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class UserSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('_id', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('username', 'string', [
            'length' => 20,
            'notnull' => false
        ]);
        $table->addColumn('email', 'string', [
            'length' => 50,
            'notnull' => true
        ]);
        $table->addColumn('fullname', 'string', [
            'length' => 40,
            'notnull' => false
        ]);
        $table->addColumn('avatar', 'text', [
            'notnull' => false
        ]);
        $table->addColumn('first_name', 'string', [
            'length' => 20,
            'notnull' => false
        ]);
        $table->addColumn('last_name', 'string', [
            'length' => 20,
            'notnull' => false
        ]);
        $table->addColumn('role', 'enum', [
            'values' => [ADMIN, USER],
            'notnull' => false,
            'default' => USER
        ]);
        $table->addColumn('gender', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('phone', 'string', [
            'length' => 15,
            'notnull' => false
        ]);
        $table->addColumn('is_verified', 'string', [
            'length' => 3,
            'default' => 'no',
            'notnull' => true
        ]);
        $table->addColumn('is_active', 'string', [
            'length' => 3,
            'default' => 'no',
            'notnull' => true
        ]);
        $table->addColumn('status', 'enum', [
            'values' => [ENABLED, ACTIVE, SUSPENDED, BLOCKED],
            'default' => ACTIVE,
            'notnull' => true
        ]);
        $table->addColumn('document_expiration', 'string', [
            'length' => 20,
            'notnull' => false
        ]);
        $table->addColumn('signature_reminders', 'string', [
            'length' => 70,
            'notnull' => false
        ]);
        $table->addColumn('zip_code', 'integer', [
            'notnull' => false
        ]);
        $table->addColumn('country', 'string', [
            'length' => 75,
            'notnull' => false
        ]);
        $table->addColumn('company_name', 'string', [
            'length' => 50,
            'notnull' => false
        ]);
        $table->addColumn('company_email', 'string', [
            'length' => 50,
            'notnull' => false
        ]);
        $table->addColumn('address', 'string', [
            'length' => 100,
            'notnull' => false
        ]);
        $table->addColumn('company_zip', 'string', [
            'length' => 50,
            'notnull' => false
        ]);
        $table->addColumn('password_hash', 'string', [
            'length' => 80,
            'notnull' => false
        ]);
        $table->addColumn('password_reset_hash', 'string', [
            'length' => 80,
            'notnull' => false
        ]);
        $table->addColumn('password_reset_expiry', 'string', [
            'length' => 80,
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