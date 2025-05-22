<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_responses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ticket_id INT UNSIGNED NOT NULL, user_id VARCHAR(70) NOT NULL, message LONGTEXT NOT NULL, role ENUM(\'admin\', \'user\') DEFAULT \'user\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        // $this->addSql('ALTER TABLE ticket_responses ADD CONSTRAINT FK_AB6545DEBF396750 FOREIGN KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_responses DROP FOREIGN KEY FK_AB6545DEBF396750');
        $this->addSql('DROP TABLE ticket_responses');
    }
}
