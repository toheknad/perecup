<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221029153103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribe ADD telegram_user_id INT DEFAULT NULL, ADD type INT NOT NULL, DROP `from`, DROP `to`');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3EFC28B263 FOREIGN KEY (telegram_user_id) REFERENCES telegram_user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68B95F3EFC28B263 ON subscribe (telegram_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribe DROP FOREIGN KEY FK_68B95F3EFC28B263');
        $this->addSql('DROP INDEX UNIQ_68B95F3EFC28B263 ON subscribe');
        $this->addSql('ALTER TABLE subscribe ADD `from` DATETIME NOT NULL, ADD `to` DATETIME NOT NULL, DROP telegram_user_id, DROP type');
    }
}
