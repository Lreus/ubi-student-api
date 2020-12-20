<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201220115553 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Implement mark table associated with student on foreign key';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mark (id VARCHAR(36) NOT NULL, student_id VARCHAR(36) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, subject VARCHAR(64) NOT NULL, PRIMARY KEY(id), FOREIGN KEY (student_id) REFERENCES student(id))');
        $this->addSql('CREATE INDEX IDX_6674F271CB944F1A ON mark (student_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mark');
    }
}
