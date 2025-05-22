<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411121910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transactions CHANGE transaction_amount transaction_amount DOUBLE PRECISION DEFAULT 0, CHANGE balance_before balance_before DOUBLE PRECISION DEFAULT 0, CHANGE balance_after balance_after DOUBLE PRECISION DEFAULT 0, CHANGE transaction_meta transaction_meta LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
       $this->addSql('ALTER TABLE transactions CHANGE transaction_amount transaction_amount DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE balance_before balance_before DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE balance_after balance_after DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE transaction_meta transaction_meta LONGTEXT NOT NULL');
    }
}
