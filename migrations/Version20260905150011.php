<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260905150011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_faq_item (id INT AUTO_INCREMENT NOT NULL, taxon_id INT DEFAULT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_12852DD5DE13F470 (taxon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_faq_item_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, question VARCHAR(255) DEFAULT NULL, answer LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_E1D9DF0F2C2AC5D3 (translatable_id), UNIQUE INDEX app_faq_item_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_faq_item ADD CONSTRAINT FK_12852DD5DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_faq_item_translation ADD CONSTRAINT FK_E1D9DF0F2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES app_faq_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_faq_item DROP FOREIGN KEY FK_12852DD5DE13F470');
        $this->addSql('ALTER TABLE app_faq_item_translation DROP FOREIGN KEY FK_E1D9DF0F2C2AC5D3');
        $this->addSql('DROP TABLE app_faq_item');
        $this->addSql('DROP TABLE app_faq_item_translation');
    }
}
