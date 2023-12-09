<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231209140146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update Avatar Links';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("DELETE FROM avatar");

        $this->addSql("INSERT INTO avatar (id, url, is_win) VALUES 
            (1,  'img/avatars/0default_avatar.png',   0),
            (2,  'img/avatars/1monstre_violet.png',   1),
            (3,  'img/avatars/2monstre_bleu.png',     4),
            (4,  'img/avatars/3monstre_bleu.png',     7),
            (5,  'img/avatars/4monstre_rose.png',     10),
            (6,  'img/avatars/5monstre_violet.png',   13),
            (7,  'img/avatars/6monstre_rose.png',     16),
            (8,  'img/avatars/7poisson_orange.png',   19),
            (9,  'img/avatars/8pieuvre_orange.png',   23),
            (10, 'img/avatars/9poisson_violet.png',   28),
            (11, 'img/avatars/10fille.png',           33),
            (12, 'img/avatars/11garcon.png',          33),
            (13, 'img/avatars/12garcon.png',          38),
            (14, 'img/avatars/13fille.png',           38),
            (15, 'img/avatars/14garcon.png',          43),
            (16, 'img/avatars/15fille.png',           43),
            (17, 'img/avatars/16garcon.png',          48),
            (18, 'img/avatars/17fille.png',           48),
            (19, 'img/avatars/18garcon.png',          53),
            (20, 'img/avatars/19monstre_lunette.png', 58),
            (21, 'img/avatars/20monstre_lunette.png', 63),
            (22, 'img/avatars/21cat_black.png',       68),
            (23, 'img/avatars/22-a_cat_white.png',    73),
            (24, 'img/avatars/22-b_cat_white.png',    78),
            (25, 'img/avatars/23lion.png',            83),
            (26, 'img/avatars/24dog.png',             88),
            (27, 'img/avatars/25chouette.png',        93),
            (28, 'img/avatars/26monster_music.png',   98),
            (29, 'img/avatars/27dragon.png',          98),
            (30, 'img/avatars/28monster.png',         103),
            (31, 'img/avatars/29monster_red.png',     108),
            (32, 'img/avatars/30monster_green.png',   113)
        ");
    

         $this->addSql("UPDATE kid SET profile_avatar = 'img/avatars/0default_avatar.png'");


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM avatar");

        $this->addSql("INSERT INTO avatar (id, url, is_win) VALUES 
            (1,	    'https://zupimages.net/up/22/34/7g4i.png',	0),
            (2,	    'https://zupimages.net/up/22/34/e9mx.png',	1),
            (3,	    'https://zupimages.net/up/22/34/iyfi.png',	4),
            (4,	    'https://zupimages.net/up/22/34/mcth.png',	7),
            (5,	    'https://zupimages.net/up/22/34/k9ko.png',	10),
            (6,	    'https://zupimages.net/up/22/34/uadk.png',	13),
            (7,	    'https://zupimages.net/up/22/34/efu4.png',	16),
            (8,	    'https://zupimages.net/up/22/34/m1yr.png',	19),
            (9,	    'https://zupimages.net/up/22/34/ghr9.png',	23),
            (10,	'https://zupimages.net/up/22/34/v8hz.png',	28),
            (11,	'https://zupimages.net/up/22/34/h1rm.png',	33),
            (12,	'https://zupimages.net/up/22/34/i9gr.png',	33),
            (13,	'https://zupimages.net/up/22/34/4qva.png',	38),
            (14,	'https://zupimages.net/up/22/34/2nf1.png',	38),
            (15,	'https://zupimages.net/up/22/34/k70i.png',	43),
            (16,	'https://zupimages.net/up/22/34/y139.png',	43),
            (17,	'https://zupimages.net/up/22/34/1x4x.png',	48),
            (18,	'https://zupimages.net/up/22/34/xst4.png',	48),
            (19,	'https://zupimages.net/up/22/34/4x7f.png',	53),
            (20,	'https://zupimages.net/up/22/34/ceve.png',	58),
            (21,	'https://zupimages.net/up/22/34/q3t5.png',	63),
            (22,	'https://zupimages.net/up/22/34/6yp7.png',	68),
            (23,	'https://zupimages.net/up/22/34/b1dr.png',	73),
            (24,	'https://zupimages.net/up/22/34/jsyv.png',	78),
            (25,	'https://zupimages.net/up/22/34/mexa.png',	83),
            (26,	'https://zupimages.net/up/22/34/8d6j.png',	88),
            (27,	'https://zupimages.net/up/22/34/1e19.png',	93),
            (28,	'https://zupimages.net/up/22/34/y380.png',	98),
            (29,	'https://zupimages.net/up/22/34/0zvc.png',	98),
            (30,	'https://zupimages.net/up/22/34/bfc8.png',	103),
            (31,	'https://zupimages.net/up/22/34/reoy.png',	108),
            (32,	'https://zupimages.net/up/22/34/w2j0.png',	113);
        ");

    }
}
