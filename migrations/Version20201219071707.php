<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201219071707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creating table student';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student (id VARCHAR(36) NOT NULL, last_name VARCHAR(64) NOT NULL, first_name VARCHAR(64) NOT NULL, birth_date DATE NOT NULL --(DC2Type:date_immutable)
        , PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE student');
    }
}
