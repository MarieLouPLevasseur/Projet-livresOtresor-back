<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220817130206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author_book (author_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_2F0A2BEEF675F31B (author_id), INDEX IDX_2F0A2BEE16A2B381 (book_id), PRIMARY KEY(author_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, is_win INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, isbn INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, publisher VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_kid (id INT AUTO_INCREMENT NOT NULL, kid_id INT DEFAULT NULL, book_id INT DEFAULT NULL, category_id INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, rating INT DEFAULT NULL, is_read TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B45EF1EA6A973770 (kid_id), INDEX IDX_B45EF1EA16A2B381 (book_id), INDEX IDX_B45EF1EA12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE diploma (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, is_win INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kid (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, user_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_avatar VARCHAR(255) NOT NULL, INDEX IDX_4523887CD60322AC (role_id), INDEX IDX_4523887CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kid_diploma (kid_id INT NOT NULL, diploma_id INT NOT NULL, INDEX IDX_C944E8C16A973770 (kid_id), INDEX IDX_C944E8C1A99ACEB5 (diploma_id), PRIMARY KEY(kid_id, diploma_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kid_avatar (kid_id INT NOT NULL, avatar_id INT NOT NULL, INDEX IDX_1CC4449D6A973770 (kid_id), INDEX IDX_1CC4449D86383B10 (avatar_id), PRIMARY KEY(kid_id, avatar_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author_book ADD CONSTRAINT FK_2F0A2BEEF675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_book ADD CONSTRAINT FK_2F0A2BEE16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_kid ADD CONSTRAINT FK_B45EF1EA6A973770 FOREIGN KEY (kid_id) REFERENCES kid (id)');
        $this->addSql('ALTER TABLE book_kid ADD CONSTRAINT FK_B45EF1EA16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_kid ADD CONSTRAINT FK_B45EF1EA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE kid ADD CONSTRAINT FK_4523887CD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE kid ADD CONSTRAINT FK_4523887CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE kid_diploma ADD CONSTRAINT FK_C944E8C16A973770 FOREIGN KEY (kid_id) REFERENCES kid (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kid_diploma ADD CONSTRAINT FK_C944E8C1A99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kid_avatar ADD CONSTRAINT FK_1CC4449D6A973770 FOREIGN KEY (kid_id) REFERENCES kid (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kid_avatar ADD CONSTRAINT FK_1CC4449D86383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_book DROP FOREIGN KEY FK_2F0A2BEEF675F31B');
        $this->addSql('ALTER TABLE author_book DROP FOREIGN KEY FK_2F0A2BEE16A2B381');
        $this->addSql('ALTER TABLE book_kid DROP FOREIGN KEY FK_B45EF1EA6A973770');
        $this->addSql('ALTER TABLE book_kid DROP FOREIGN KEY FK_B45EF1EA16A2B381');
        $this->addSql('ALTER TABLE book_kid DROP FOREIGN KEY FK_B45EF1EA12469DE2');
        $this->addSql('ALTER TABLE kid DROP FOREIGN KEY FK_4523887CD60322AC');
        $this->addSql('ALTER TABLE kid DROP FOREIGN KEY FK_4523887CA76ED395');
        $this->addSql('ALTER TABLE kid_diploma DROP FOREIGN KEY FK_C944E8C16A973770');
        $this->addSql('ALTER TABLE kid_diploma DROP FOREIGN KEY FK_C944E8C1A99ACEB5');
        $this->addSql('ALTER TABLE kid_avatar DROP FOREIGN KEY FK_1CC4449D6A973770');
        $this->addSql('ALTER TABLE kid_avatar DROP FOREIGN KEY FK_1CC4449D86383B10');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE author_book');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_kid');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE diploma');
        $this->addSql('DROP TABLE kid');
        $this->addSql('DROP TABLE kid_diploma');
        $this->addSql('DROP TABLE kid_avatar');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE user');
    }
}
