<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129212112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_post (id INT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, tags LONGTEXT NOT NULL, reactions INT NOT NULL, create_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(2048) NOT NULL, address VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reactions (id INT AUTO_INCREMENT NOT NULL, blog_user_id INT DEFAULT NULL, blog_post_id INT DEFAULT NULL, has_reaction TINYINT(1) NOT NULL, INDEX IDX_38737FB34B986E16 (blog_user_id), INDEX IDX_38737FB3A77FBEAF (blog_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reactions ADD CONSTRAINT FK_38737FB34B986E16 FOREIGN KEY (blog_user_id) REFERENCES blog_user (id)');
        $this->addSql('ALTER TABLE reactions ADD CONSTRAINT FK_38737FB3A77FBEAF FOREIGN KEY (blog_post_id) REFERENCES blog_post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reactions DROP FOREIGN KEY FK_38737FB34B986E16');
        $this->addSql('ALTER TABLE reactions DROP FOREIGN KEY FK_38737FB3A77FBEAF');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_user');
        $this->addSql('DROP TABLE reactions');
    }
}
