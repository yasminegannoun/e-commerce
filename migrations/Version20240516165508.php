<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516165508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE details_livre (id INT AUTO_INCREMENT NOT NULL, qte INT NOT NULL, prix DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, panier_id INT DEFAULT NULL, livre_id INT DEFAULT NULL, INDEX IDX_3C8CE976F77D927C (panier_id), INDEX IDX_3C8CE97637D925CB (livre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE details_livre ADD CONSTRAINT FK_3C8CE976F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE details_livre ADD CONSTRAINT FK_3C8CE97637D925CB FOREIGN KEY (livre_id) REFERENCES livres (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE details_livre DROP FOREIGN KEY FK_3C8CE976F77D927C');
        $this->addSql('ALTER TABLE details_livre DROP FOREIGN KEY FK_3C8CE97637D925CB');
        $this->addSql('DROP TABLE details_livre');
    }
}
