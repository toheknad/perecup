<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023220917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribe (id INT AUTO_INCREMENT NOT NULL, telegram_user_id INT DEFAULT NULL, `from` DATETIME NOT NULL, `to` DATETIME NOT NULL, UNIQUE INDEX UNIQ_68B95F3EFC28B263 (telegram_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3EFC28B263 FOREIGN KEY (telegram_user_id) REFERENCES telegram_user (id)');
        $this->addSql('ALTER TABLE telegram_user ADD subscribe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telegram_user ADD CONSTRAINT FK_F180F059C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F180F059C72A4771 ON telegram_user (subscribe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE telegram_user DROP FOREIGN KEY FK_F180F059C72A4771');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('DROP INDEX UNIQ_F180F059C72A4771 ON telegram_user');
        $this->addSql('ALTER TABLE telegram_user DROP subscribe_id');
    }
}
