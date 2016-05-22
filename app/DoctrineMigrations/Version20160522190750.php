<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160522190750 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recup_user_profile ADD username INT DEFAULT NULL, CHANGE birth birth DATE DEFAULT NULL, CHANGE gender gender INT NOT NULL');
        $this->addSql('ALTER TABLE recup_user_profile ADD CONSTRAINT FK_6FA4F53DF85E0677 FOREIGN KEY (username) REFERENCES recup_user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FA4F53DF85E0677 ON recup_user_profile (username)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recup_user_profile DROP FOREIGN KEY FK_6FA4F53DF85E0677');
        $this->addSql('DROP INDEX UNIQ_6FA4F53DF85E0677 ON recup_user_profile');
        $this->addSql('ALTER TABLE recup_user_profile DROP username, CHANGE birth birth DATETIME DEFAULT NULL, CHANGE gender gender TINYINT(1) NOT NULL');
    }
}
