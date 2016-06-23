<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160623155352 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record_user (record_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6824479D4DFD750C (record_id), INDEX IDX_6824479DA76ED395 (user_id), PRIMARY KEY(record_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_user ADD CONSTRAINT FK_6824479D4DFD750C FOREIGN KEY (record_id) REFERENCES record (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE record_user ADD CONSTRAINT FK_6824479DA76ED395 FOREIGN KEY (user_id) REFERENCES recup_user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE record_user_profile');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE record_user_profile (record_id INT NOT NULL, user_profile_id INT NOT NULL, INDEX IDX_F549DA094DFD750C (record_id), INDEX IDX_F549DA096B9DD454 (user_profile_id), PRIMARY KEY(record_id, user_profile_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE record_user_profile ADD CONSTRAINT FK_F549DA094DFD750C FOREIGN KEY (record_id) REFERENCES record (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE record_user_profile ADD CONSTRAINT FK_F549DA096B9DD454 FOREIGN KEY (user_profile_id) REFERENCES recup_user_profile (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE record_user');
    }
}
