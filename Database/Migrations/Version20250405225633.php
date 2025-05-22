<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE files (id INT UNSIGNED AUTO_INCREMENT NOT NULL, _id VARCHAR(70) DEFAULT NULL, user_id VARCHAR(70) DEFAULT NULL, storage_size DOUBLE PRECISION DEFAULT NULL, file_data LONGTEXT DEFAULT NULL, file_path VARCHAR(225) DEFAULT NULL, folder_id VARCHAR(70) DEFAULT NULL, deleted VARCHAR(3) DEFAULT \'no\' NOT NULL, status VARCHAR(8) DEFAULT \'enabled\', shared LONGTEXT DEFAULT NULL, visibility VARCHAR(7) DEFAULT NULL, share_link VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE files');
    }
}
