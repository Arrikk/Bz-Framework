<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405224531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balances (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, wallet_id VARCHAR(20) NOT NULL, wallet_balance DOUBLE PRECISION DEFAULT 0 NOT NULL, withdrawable_balance DOUBLE PRECISION DEFAULT 0 NOT NULL, non_withdrawable_balance DOUBLE PRECISION DEFAULT 0 NOT NULL, withdrwable_status ENUM(\'enabled\', \'disabled\') DEFAULT \'enabled\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_balances_user (user_id), INDEX idx_balances_wallet (wallet_id), PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE balances');
    }
}
