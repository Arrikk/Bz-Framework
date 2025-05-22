<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, _id VARCHAR(30) NOT NULL, message VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, status ENUM(\'open\', \'closed\', \'awaiting_reply\', \'in_progress\') DEFAULT \'open\' NOT NULL, priority ENUM(\'low\', \'medium\', \'high\') DEFAULT \'medium\' NOT NULL, category VARCHAR(255) DEFAULT \'general\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tickets');
    }
}
