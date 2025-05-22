<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408223623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'admin\', \'user\') DEFAULT \'user\', CHANGE status status ENUM(\'enabled\', \'active\', \'suspended\', \'blocked\') DEFAULT \'active\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
       $this->addSql('ALTER TABLE users CHANGE role role VARCHAR(70) DEFAULT NULL, CHANGE status status VARCHAR(100) DEFAULT \'active\' NOT NULL');
    }
}
