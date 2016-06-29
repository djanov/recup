<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160629002155 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record (id INT AUTO_INCREMENT NOT NULL, username_id INT DEFAULT NULL, song_name VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, about VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, is_downloadable TINYINT(1) NOT NULL, is_published TINYINT(1) NOT NULL, INDEX IDX_9B349F91ED766068 (username_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE record_user (record_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6824479D4DFD750C (record_id), INDEX IDX_6824479DA76ED395 (user_id), PRIMARY KEY(record_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_favorite (record_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_88486AD94DFD750C (record_id), INDEX IDX_88486AD9A76ED395 (user_id), PRIMARY KEY(record_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE record_comment (id INT AUTO_INCREMENT NOT NULL, record_id INT NOT NULL, username VARCHAR(255) NOT NULL, user_avatar_filename VARCHAR(255) NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_23AB52114DFD750C (record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, thread_id VARCHAR(255) DEFAULT NULL, author_id INT DEFAULT NULL, body LONGTEXT NOT NULL, ancestors VARCHAR(1024) NOT NULL, depth INT NOT NULL, created_at DATETIME NOT NULL, state INT NOT NULL, INDEX IDX_9474526CE2904019 (thread_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thread (id VARCHAR(255) NOT NULL, permalink VARCHAR(255) NOT NULL, is_commentable TINYINT(1) NOT NULL, num_comments INT NOT NULL, last_comment_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recup_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_FA40FD6292FC23A8 (username_canonical), UNIQUE INDEX UNIQ_FA40FD62A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recup_user_profile (id INT AUTO_INCREMENT NOT NULL, username INT DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, genre LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', birth DATE DEFAULT NULL, about LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, gender INT NOT NULL, path VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FA4F53DF85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record ADD CONSTRAINT FK_9B349F91ED766068 FOREIGN KEY (username_id) REFERENCES recup_user_profile (id)');
        $this->addSql('ALTER TABLE record_user ADD CONSTRAINT FK_6824479D4DFD750C FOREIGN KEY (record_id) REFERENCES record (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE record_user ADD CONSTRAINT FK_6824479DA76ED395 FOREIGN KEY (user_id) REFERENCES recup_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_favorite ADD CONSTRAINT FK_88486AD94DFD750C FOREIGN KEY (record_id) REFERENCES record (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_favorite ADD CONSTRAINT FK_88486AD9A76ED395 FOREIGN KEY (user_id) REFERENCES recup_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE record_comment ADD CONSTRAINT FK_23AB52114DFD750C FOREIGN KEY (record_id) REFERENCES record (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES recup_user (id)');
        $this->addSql('ALTER TABLE recup_user_profile ADD CONSTRAINT FK_6FA4F53DF85E0677 FOREIGN KEY (username) REFERENCES recup_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record_user DROP FOREIGN KEY FK_6824479D4DFD750C');
        $this->addSql('ALTER TABLE user_favorite DROP FOREIGN KEY FK_88486AD94DFD750C');
        $this->addSql('ALTER TABLE record_comment DROP FOREIGN KEY FK_23AB52114DFD750C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CE2904019');
        $this->addSql('ALTER TABLE record_user DROP FOREIGN KEY FK_6824479DA76ED395');
        $this->addSql('ALTER TABLE user_favorite DROP FOREIGN KEY FK_88486AD9A76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE recup_user_profile DROP FOREIGN KEY FK_6FA4F53DF85E0677');
        $this->addSql('ALTER TABLE record DROP FOREIGN KEY FK_9B349F91ED766068');
        $this->addSql('DROP TABLE record');
        $this->addSql('DROP TABLE record_user');
        $this->addSql('DROP TABLE user_favorite');
        $this->addSql('DROP TABLE record_comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE recup_user');
        $this->addSql('DROP TABLE recup_user_profile');
    }
}
