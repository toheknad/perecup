<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221010160915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_checked ADD parse_url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url_checked ADD CONSTRAINT FK_1070ECDF88CCE813 FOREIGN KEY (parse_url_id) REFERENCES parse_url (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1070ECDF88CCE813 ON url_checked (parse_url_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_checked DROP FOREIGN KEY FK_1070ECDF88CCE813');
        $this->addSql('DROP INDEX UNIQ_1070ECDF88CCE813 ON url_checked');
        $this->addSql('ALTER TABLE url_checked DROP parse_url_id');
    }
}
