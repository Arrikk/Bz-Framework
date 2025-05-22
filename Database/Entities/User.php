<?php

use App\Models\User;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final class CustomSchemaProvider implements SchemaProvider
{
    public function createSchema(): Schema
    {
        $schema = new Schema();
    
        $table = $schema->createTable(User::table());
    
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
            ]);
    
            $table->addColumn('username', 'string', [
                'notnull' => false,
            ]);
    
            $table->setPrimaryKey(array('id'));
    
            return $schema;
    }
}