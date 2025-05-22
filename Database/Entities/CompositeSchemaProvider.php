<?php

namespace Database\Entities;

use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\DBAL\Schema\Schema;

class CompositeSchemaProvider implements SchemaProvider
{
    private array $providers;

    public function __construct()
    {
        $this->providers = [
            new ActiveSessionsSchemaProvider(),
            new BalancesSchemaProvider(),
            new DevicesSchemaProvider(),
            new DocumentTemplateSchemaProvider(),
            new FeatureSchemaProvider(),
            new FileSchemaProvider(),
            new FolderSchemaProvider(),
            new LogSchemaProvider(),
            new NotificationSchemaProvider(),
            new PlanSchemaProvider(),
            new RecipientSchemaProvider(),
            new ReferralSchemaProvider(),
            new ReminderScheduleSchemaProvider(),
            new ReminderSchemaProvider(),
            new SentReminderSchemaProvider(),
            new SettingSchemaProvider(),
            new StorageSchemaProvider(),
            new SubscriptionSchemaProvider(),
            new TicketSchemaProvider(),
            new TicketResponseSchemaProvider(),
            new TokenSchemaProvider(),
            new TransactionSchemaProvider(),
            new UserSchemaProvider(),
            new WalletSchemaProvider(),
        ];
    }

    public function createSchema(): Schema
    {
        $schema = new Schema();

        foreach ($this->providers as $provider) {
            $providerSchema = $provider->createSchema();
            foreach ($providerSchema->getTables() as $table) {
                if (!$schema->hasTable($table->getName())) {
                    $newTable = $schema->createTable($table->getName());
                    foreach ($table->getColumns() as $column) {
                        $newTable->addColumn(
                            $column->getName(),
                            $column->getType()->getName(),
                            array_merge($column->toArray(), ['comment' => $column->getComment()])
                        );
                    }
                    foreach ($table->getIndexes() as $index) {
                        if (!$index->isPrimary()) {
                            $newTable->addIndex($index->getColumns(), $index->getName(), $index->getFlags(), $index->getOptions());
                        }
                    }
                    foreach ($table->getForeignKeys() as $foreignKey) {
                        $newTable->addForeignKeyConstraint(
                            $foreignKey->getForeignTableName(),
                            $foreignKey->getLocalColumns(),
                            $foreignKey->getForeignColumns(),
                            $foreignKey->getOptions()
                        );
                    }
                    if ($table->getPrimaryKey()) {
                        $newTable->setPrimaryKey($table->getPrimaryKey()->getColumns());
                    }
                }
            }
        }

        return $schema;
    }
}