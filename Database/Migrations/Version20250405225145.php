<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, wallet_id VARCHAR(12) NOT NULL, transaction_type VARCHAR(6) DEFAULT NULL, transaction_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, balance_before DOUBLE PRECISION DEFAULT 0 NOT NULL, balance_after DOUBLE PRECISION DEFAULT 0 NOT NULL, transaction_reference VARCHAR(100) DEFAULT NULL, transaction_meta LONGTEXT NOT NULL, transaction_status VARCHAR(9) DEFAULT \'pending\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE transactions');
    }
}
