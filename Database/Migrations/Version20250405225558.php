<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifications (id INT UNSIGNED AUTO_INCREMENT NOT NULL, _id INT NOT NULL, from_id VARCHAR(80) DEFAULT NULL, to_id VARCHAR(80) DEFAULT NULL, message VARCHAR(50) DEFAULT NULL, category VARCHAR(20) DEFAULT NULL, status VARCHAR(7) DEFAULT \'unread\', tag VARCHAR(25) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notifications');
    }
}
