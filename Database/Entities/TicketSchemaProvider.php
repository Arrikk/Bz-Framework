<?php
namespace Database\Entities;
use App\Models\Ticket;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class TicketSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
    
        $table = $schema->createTable(Ticket::table());
    
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
            ]);
    
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 30,
            ]);
            $table->addColumn('message', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('subject', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('status', 'enum', [
                'values' => [OPEN, CLOSED, AWAITING_REPLY, IN_PROGRESS, RESOLVED],
                'notnull' => true,
                'default' => OPEN
            ]);
            $table->addColumn('priority', 'enum', [
                'values' => [LOW, MEDIUM, HIGH],
                'notnull' => true,
                'default' => MEDIUM,
                'default' => 'medium'
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 255,
                'default' => 'general'
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
                'default' => 'CURRENT_TIMESTAMP'
            ]);
            $table->addColumn('updated_at', 'datetime', [
                'notnull' => true,
                'default' => 'CURRENT_TIMESTAMP',
                // 'attributes' => ['on update CURRENT_TIMESTAMP']
            ]);
    
            $table->setPrimaryKey(array('id'));
    
            return $schema;
    }
}