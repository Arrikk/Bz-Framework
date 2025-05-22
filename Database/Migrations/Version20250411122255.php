<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411122255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
       $this->addSql('ALTER TABLE transactions CHANGE transaction_type transaction_type VARCHAR(6) NOT NULL, CHANGE transaction_amount transaction_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, CHANGE balance_before balance_before DOUBLE PRECISION DEFAULT 0 NOT NULL, CHANGE balance_after balance_after DOUBLE PRECISION DEFAULT 0 NOT NULL, CHANGE transaction_reference transaction_reference VARCHAR(100) NOT NULL, CHANGE transaction_meta transaction_meta LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transactions CHANGE transaction_type transaction_type VARCHAR(6) DEFAULT NULL, CHANGE transaction_amount transaction_amount DOUBLE PRECISION DEFAULT \'0\', CHANGE balance_before balance_before DOUBLE PRECISION DEFAULT \'0\', CHANGE balance_after balance_after DOUBLE PRECISION DEFAULT \'0\', CHANGE transaction_reference transaction_reference VARCHAR(100) DEFAULT NULL, CHANGE transaction_meta transaction_meta LONGTEXT DEFAULT NULL');
    }
}
