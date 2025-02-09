<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209105942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F62671590');
        $this->addSql('DROP INDEX IDX_E9E2810F62671590 ON voiture');
        $this->addSql('ALTER TABLE voiture DROP covoiturage_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture ADD covoiturage_id INT NOT NULL');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (covoiturage_id)');
        $this->addSql('CREATE INDEX IDX_E9E2810F62671590 ON voiture (covoiturage_id)');
    }
}
