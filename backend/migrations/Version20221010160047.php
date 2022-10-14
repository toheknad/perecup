<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221010160047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parse_url DROP FOREIGN KEY FK_3521B24688CCE813');
        $this->addSql('DROP INDEX IDX_3521B24688CCE813 ON parse_url');
        $this->addSql('ALTER TABLE parse_url DROP parse_url_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parse_url ADD parse_url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE parse_url ADD CONSTRAINT FK_3521B24688CCE813 FOREIGN KEY (parse_url_id) REFERENCES url_checked (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3521B24688CCE813 ON parse_url (parse_url_id)');
    }
}
