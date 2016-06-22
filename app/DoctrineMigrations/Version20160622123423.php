<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160622123423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_genre DROP FOREIGN KEY FK_6192C8A04296D31F');
        $this->addSql('CREATE TABLE recup_user_profile (id INT AUTO_INCREMENT NOT NULL, username INT DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, genre LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', birth DATE DEFAULT NULL, about LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, gender INT NOT NULL, path VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FA4F53DF85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recup_user_profile ADD CONSTRAINT FK_6FA4F53DF85E0677 FOREIGN KEY (username) REFERENCES recup_user (id)');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE user_genre');
        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91ED766068');
        $this->addSql('ALTER TABLE record ADD genre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91ED766068 FOREIGN KEY (username_id) REFERENCES recup_user_profile (id)');
        $this->addSql('ALTER TABLE recup_user DROP country, DROP birth, DROP about, DROP website, DROP gender, DROP path, DROP name');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91ED766068');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_genre (id INT AUTO_INCREMENT NOT NULL, genre_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_6192C8A0A76ED395 (user_id), INDEX IDX_6192C8A04296D31F (genre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_genre ADD CONSTRAINT FK_6192C8A04296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE user_genre ADD CONSTRAINT FK_6192C8A0A76ED395 FOREIGN KEY (user_id) REFERENCES recup_user (id)');
        $this->addSql('DROP TABLE recup_user_profile');
        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91ED766068');
        $this->addSql('ALTER TABLE record DROP genre');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91ED766068 FOREIGN KEY (username_id) REFERENCES recup_user (id)');
        $this->addSql('ALTER TABLE recup_user ADD country VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD birth DATE DEFAULT NULL, ADD about LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD website VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD gender INT DEFAULT NULL, ADD path VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
