<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208225959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suspension DROP FOREIGN KEY FK_82AF05001B65292');
        $this->addSql('DROP INDEX IDX_82AF05001B65292 ON suspension');
        $this->addSql('ALTER TABLE suspension DROP employe_id');
        $this->addSql('ALTER TABLE voiture CHANGE marque_id marque_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suspension ADD employe_id INT NOT NULL');
        $this->addSql('ALTER TABLE suspension ADD CONSTRAINT FK_82AF05001B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('CREATE INDEX IDX_82AF05001B65292 ON suspension (employe_id)');
    }
}
