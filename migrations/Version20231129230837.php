<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129230837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DB03A8386 FOREIGN KEY (created_by_id) REFERENCES blog_user (id)');
        $this->addSql('CREATE INDEX IDX_BA5AE01DB03A8386 ON blog_post (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01DB03A8386');
        $this->addSql('DROP INDEX IDX_BA5AE01DB03A8386 ON blog_post');
        $this->addSql('ALTER TABLE blog_post DROP created_by_id');
    }
}
