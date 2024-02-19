<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209094251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD related_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7490C989 FOREIGN KEY (related_post_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7490C989 ON post (related_post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D7490C989');
        $this->addSql('DROP INDEX IDX_5A8A6C8D7490C989 ON post');
        $this->addSql('ALTER TABLE post DROP related_post_id');
    }
}
