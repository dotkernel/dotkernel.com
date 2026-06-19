<?php

declare(strict_types=1);

namespace Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260618121357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (name LONGTEXT NOT NULL, slug LONGTEXT NOT NULL, bio LONGTEXT DEFAULT NULL, email LONGTEXT NOT NULL, id UUID NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_BDAFD8C8989D9B62 (slug), UNIQUE INDEX UNIQ_BDAFD8C8E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (name LONGTEXT NOT NULL, slug LONGTEXT NOT NULL, isVisible TINYINT NOT NULL, id UUID NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post (title LONGTEXT NOT NULL, slug LONGTEXT NOT NULL, post_date DATETIME NOT NULL, excerpt LONGTEXT NOT NULL, status ENUM(\'draft\', \'published\', \'private\') DEFAULT \'draft\' NOT NULL, id UUID NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, category_id UUID NOT NULL, author_id UUID NOT NULL, UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8D12469DE2 (category_id), INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE post');
    }
}
