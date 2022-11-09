<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221107215326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribe DROP INDEX UNIQ_68B95F3EFC28B263, ADD INDEX IDX_68B95F3EFC28B263 (telegram_user_id)');
        $this->addSql('ALTER TABLE telegram_user DROP FOREIGN KEY FK_F180F059C72A4771');
        $this->addSql('DROP INDEX UNIQ_F180F059C72A4771 ON telegram_user');
        $this->addSql('ALTER TABLE telegram_user DROP subscribe_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribe DROP INDEX IDX_68B95F3EFC28B263, ADD UNIQUE INDEX UNIQ_68B95F3EFC28B263 (telegram_user_id)');
        $this->addSql('ALTER TABLE telegram_user ADD subscribe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telegram_user ADD CONSTRAINT FK_F180F059C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F180F059C72A4771 ON telegram_user (subscribe_id)');
    }
}
