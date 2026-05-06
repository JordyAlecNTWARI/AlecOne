<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506011901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE borrow (id INT AUTO_INCREMENT NOT NULL, borrowed_at DATE NOT NULL, due_at DATE NOT NULL, returned_at DATE DEFAULT NULL, status VARCHAR(20) NOT NULL, user_id INT NOT NULL, resource_id INT NOT NULL, INDEX IDX_55DBA8B0A76ED395 (user_id), INDEX IDX_55DBA8B089329D25 (resource_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, ceated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resource (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(500) DEFAULT NULL, cover_image VARCHAR(255) DEFAULT NULL, published_at DATE NOT NULL, is_available TINYINT NOT NULL, created_at DATE NOT NULL, playlist_id INT NOT NULL, INDEX IDX_BC91F4166BBD148 (playlist_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, rating INT NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATE NOT NULL, is_approved TINYINT NOT NULL, user_id INT NOT NULL, resource_id INT NOT NULL, INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C689329D25 (resource_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE borrow ADD CONSTRAINT FK_55DBA8B0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE borrow ADD CONSTRAINT FK_55DBA8B089329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F4166BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C689329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE borrow DROP FOREIGN KEY FK_55DBA8B0A76ED395');
        $this->addSql('ALTER TABLE borrow DROP FOREIGN KEY FK_55DBA8B089329D25');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F4166BBD148');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C689329D25');
        $this->addSql('DROP TABLE borrow');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user');
    }
}
