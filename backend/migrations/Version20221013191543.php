<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013191543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_checked DROP FOREIGN KEY FK_1070ECDFA76ED395');
        $this->addSql('DROP INDEX IDX_1070ECDFA76ED395 ON url_checked');
        $this->addSql('ALTER TABLE url_checked CHANGE user_id telegram_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url_checked ADD CONSTRAINT FK_1070ECDFFC28B263 FOREIGN KEY (telegram_user_id) REFERENCES telegram_user (id)');
        $this->addSql('CREATE INDEX IDX_1070ECDFFC28B263 ON url_checked (telegram_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url_checked DROP FOREIGN KEY FK_1070ECDFFC28B263');
        $this->addSql('DROP INDEX IDX_1070ECDFFC28B263 ON url_checked');
        $this->addSql('ALTER TABLE url_checked CHANGE telegram_user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE url_checked ADD CONSTRAINT FK_1070ECDFA76ED395 FOREIGN KEY (user_id) REFERENCES telegram_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1070ECDFA76ED395 ON url_checked (user_id)');
    }
}
