<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, _id VARCHAR(70) DEFAULT NULL, username VARCHAR(20) DEFAULT NULL, email VARCHAR(50) NOT NULL, fullname VARCHAR(40) DEFAULT NULL, avatar LONGTEXT DEFAULT NULL, first_name VARCHAR(20) DEFAULT NULL, last_name VARCHAR(20) DEFAULT NULL, role VARCHAR(70) DEFAULT NULL, gender VARCHAR(70) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, is_verified VARCHAR(3) DEFAULT \'no\' NOT NULL, is_active VARCHAR(3) DEFAULT \'no\' NOT NULL, status VARCHAR(1) DEFAULT \'1\' NOT NULL, document_expiration VARCHAR(20) DEFAULT NULL, signature_reminders VARCHAR(70) DEFAULT NULL, zip_code INT DEFAULT NULL, country VARCHAR(75) DEFAULT NULL, company_name VARCHAR(50) DEFAULT NULL, company_email VARCHAR(50) DEFAULT NULL, address VARCHAR(100) DEFAULT NULL, company_zip VARCHAR(50) DEFAULT NULL, password_hash VARCHAR(80) DEFAULT NULL, password_reset_hash VARCHAR(80) DEFAULT NULL, password_reset_expiry VARCHAR(80) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
    }
}
