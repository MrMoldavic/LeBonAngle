<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221006114107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E663CCE3900');
        $this->addSql('DROP INDEX IDX_23A0E663CCE3900 ON article');
        $this->addSql('ALTER TABLE article ADD city_id INT DEFAULT NULL, DROP city_id_id');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E668BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_23A0E668BAC62AF ON article (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E668BAC62AF');
        $this->addSql('DROP INDEX IDX_23A0E668BAC62AF ON article');
        $this->addSql('ALTER TABLE article ADD city_id_id INT NOT NULL, DROP city_id');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E663CCE3900 FOREIGN KEY (city_id_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_23A0E663CCE3900 ON article (city_id_id)');
    }
}
