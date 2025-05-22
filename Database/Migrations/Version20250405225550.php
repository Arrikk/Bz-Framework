<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plans (id INT AUTO_INCREMENT NOT NULL, _id VARCHAR(75) DEFAULT NULL, user_id VARCHAR(75) DEFAULT NULL, plan_id VARCHAR(75) DEFAULT NULL, plan_name VARCHAR(75) DEFAULT NULL, plan_discount VARCHAR(75) DEFAULT NULL, plan_features VARCHAR(75) DEFAULT NULL, plan_amount VARCHAR(75) DEFAULT NULL, plan_desc VARCHAR(75) DEFAULT NULL, plan_duration VARCHAR(75) DEFAULT NULL, amount_id VARCHAR(75) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plans');
    }
}
