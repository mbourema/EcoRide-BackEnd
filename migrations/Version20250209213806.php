<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209213806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement ADD covoiturage_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (covoiturage_id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E62671590 ON paiement (covoiturage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E62671590');
        $this->addSql('DROP INDEX IDX_B1DC7A1E62671590 ON paiement');
        $this->addSql('ALTER TABLE paiement DROP covoiturage_id');
    }
}
