<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160407204418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record_comment ADD record_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE record_comment ADD CONSTRAINT FK_23AB52114DFD750C FOREIGN KEY (record_id) REFERENCES record (id)');
        $this->addSql('CREATE INDEX IDX_23AB52114DFD750C ON record_comment (record_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE record_comment DROP FOREIGN KEY FK_23AB52114DFD750C');
        $this->addSql('DROP INDEX IDX_23AB52114DFD750C ON record_comment');
        $this->addSql('ALTER TABLE record_comment DROP record_id');
    }
}
