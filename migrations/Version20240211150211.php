<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211150211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update structure + role Admin + catégories';
    }

    public function up(Schema $schema): void
    {
        // structure
        $this->addSql('ALTER TABLE book CHANGE description description LONGTEXT NOT NULL');
        
        // role
        $this->addSql("INSERT INTO role ( `name`) VALUES ('ROLE_ADMIN');");
        $this->addSql("INSERT INTO role ( `name`) VALUES ('ROLE_USER');");
        $this->addSql("INSERT INTO role ( `name`) VALUES ('ROLE_KID');");
        
      // Categories
        $categoriesArray = ['Non-classé', 'Aventure','BD','Contes','Documentaires', 'Fantastique','Humour', 'Policier', 'Philosophique','Science-fiction'];
        foreach ($categoriesArray as $category) {
            $this->addSql('INSERT INTO category (`name`) VALUES (?)', [$category]);
        }

        // DIPLOMES

        $diplomasArray = [
            ['url'=>'/img/diplomes/diplome_1.png',
            'isWin'=> 1],
            ['url'=>'/img/diplomes/diplome_10.png',
            'isWin'=> 10],
            ['url'=>'/img/diplomes/diplome_20.png',
            'isWin'=> 20],
            ['url'=>'/img/diplomes/diplome_30.png',
            'isWin'=> 30],
            ['url'=>'/img/diplomes/diplome_40.png',
            'isWin'=> 40]

        ];
        foreach ($diplomasArray as $diploma) {
            $this->addSql('INSERT INTO diploma (`url`, is_win) VALUES (?, ?)', [$diploma['url'], $diploma['isWin']]);
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql("DELETE FROM role WHERE name = 'ROLE_ADMIN';");
        $this->addSql("DELETE FROM category WHERE name IN ('Non-classé', 'Aventure','BD','Contes','Documentaires', 'Fantastique','Humour', 'Policier', 'Philosophique','Science-fiction');");
        $this->addSql('DELETE FROM diploma');

    }
}
