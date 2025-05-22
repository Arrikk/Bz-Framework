<?php

namespace Database\Entities;

use App\Models\Ticket;
use App\Models\TicketResponse;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class TicketResponseSchemaProvider implements SchemaProvider
{

    public function createSchema(): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable(TicketResponse::table());
        $table->addColumn(
            'id',
            'integer',
            [
                'unsigned' => true,
                'autoincrement' => true
            ]
        );
        $table->addColumn('ticket_id', 'integer', ['unsigned' => true]);
        $table->addColumn('user_id', 'string', [
            'length' => 70,
            'notnull' => true
        ]);
        $table->addColumn('message', 'text');
        $table->addColumn('role', 'enum', [
            'values' => [ADMIN, USER],
            'default' => USER
        ]);
        $table->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint(Ticket::table(), ['id'], ['ticket_id'], ['onDelete' => 'CASCADE']);
        return $schema;
    }
}
