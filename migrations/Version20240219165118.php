<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219165118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD featured_img_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DD0E9ACF4 FOREIGN KEY (featured_img_id) REFERENCES file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8DD0E9ACF4 ON post (featured_img_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DD0E9ACF4');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8DD0E9ACF4 ON post');
        $this->addSql('ALTER TABLE post DROP featured_img_id');
    }
}
