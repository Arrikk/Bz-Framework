<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408064922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tickets CHANGE status status ENUM(\'open\', \'closed\', \'awaiting_reply\', \'in_progress\', \'resolved\') DEFAULT \'open\' NOT NULL, CHANGE _id user_id VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {$this->addSql('ALTER TABLE tickets CHANGE status status ENUM(\'open\', \'closed\', \'awaiting_reply\', \'in_progress\') DEFAULT \'open\' NOT NULL, CHANGE user_id _id VARCHAR(30) NOT NULL');
    }
}
