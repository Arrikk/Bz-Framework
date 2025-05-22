<?php

declare(strict_types=1);

namespace Esignature\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405225537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE referrals (id INT UNSIGNED AUTO_INCREMENT NOT NULL, referred_id INT UNSIGNED NOT NULL, status ENUM(\'pending\', \'converted\', \'expired\') DEFAULT \'pending\' NOT NULL, source ENUM(\'email\', \'direct\', \'affiliate\', \'social-media\') DEFAULT \'direct\' NOT NULL, plan VARCHAR(20) NOT NULL, commission NUMERIC(10, 2) DEFAULT 0 NOT NULL, referrer_id INT UNSIGNED NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX idx_referral_referred (referred_id), INDEX idx_referral_referrer (referrer_id), INDEX idx_referral_status (status), INDEX idx_referral_plan (plan), PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE referrals');
    }
}
