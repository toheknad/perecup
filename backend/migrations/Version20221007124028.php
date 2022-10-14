<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221007124028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parse_url DROP region, DROP real_estate_name, DROP real_estate_type, DROP terms');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parse_url ADD region VARCHAR(50) NOT NULL, ADD real_estate_name VARCHAR(255) NOT NULL, ADD real_estate_type VARCHAR(255) DEFAULT NULL, ADD terms VARCHAR(50) NOT NULL');
    }
}
