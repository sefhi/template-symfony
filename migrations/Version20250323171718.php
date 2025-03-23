<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323171718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Created app_books table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_books (id UUID NOT NULL, title VARCHAR(50) NOT NULL, author VARCHAR(100) NOT NULL, stock INT NOT NULL, isbn VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN app_books.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE app_books');
    }
}
