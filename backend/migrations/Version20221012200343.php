<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221012200343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parse_url DROP FOREIGN KEY FK_3521B246A76ED395');
        $this->addSql('ALTER TABLE parse_url ADD CONSTRAINT FK_3521B246A76ED395 FOREIGN KEY (user_id) REFERENCES telegram_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parse_url DROP FOREIGN KEY FK_3521B246A76ED395');
        $this->addSql('ALTER TABLE parse_url ADD CONSTRAINT FK_3521B246A76ED395 FOREIGN KEY (user_id) REFERENCES parse_url (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
