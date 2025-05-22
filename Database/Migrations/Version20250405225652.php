<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devices (id INT AUTO_INCREMENT NOT NULL, _id VARCHAR(75) DEFAULT NULL, user_id VARCHAR(75) DEFAULT NULL, device_token LONGTEXT DEFAULT NULL, device_ip VARCHAR(100) DEFAULT NULL, device_browser VARCHAR(100) DEFAULT NULL, device_os VARCHAR(100) DEFAULT NULL, device_type VARCHAR(100) DEFAULT NULL, status ENUM(\'active\', \'inactive\') DEFAULT \'inactive\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_devices_id (_id), INDEX idx_devices_user (user_id), INDEX idx_devices_status (status), PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE devices');
    }
}
