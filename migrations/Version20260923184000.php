<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923184000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create app_ai_prompt table and seed default active system prompts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE app_ai_prompt (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, content LONGTEXT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX idx_ai_prompt_type_active (type, is_active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        // Seed 1: Brand Context
        $this->addSql('INSERT INTO app_ai_prompt (label, type, content, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)', [
            'Contexte Global & Philosophie de l\'Artisan',
            'brand_context',
            "Nous sommes l'atelier de création ZEN TOO CRAFT, un studio d'artisanat d'art français d'exception. Nous concevons et fabriquons à la main des instruments de musique à vent (flûtes japonaises Shakuhachi, flûtes traversières baroques et contemporaines) et des luminaires artistiques. Matériau exclusif : bambou naturel noble, traité thermiquement et huilé naturellement. L'axe de notre maison est purement artistique, musical, acoustique et traditionnel. INTERDICTION ABSOLUE de parler de méditation, de relaxation New Age ou de sonothérapie. Fabrication 100% manuelle et locale, sur-mesure, aucune production industrielle.",
            $now,
            $now,
        ]);

        // Seed 2: FAQ
        $this->addSql('INSERT INTO app_ai_prompt (label, type, content, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)', [
            'Génération FAQ Taxon Standard',
            'faq',
            "Tu es le rédacteur officiel du studio de création ZEN TOO CRAFT. MISSION : Rédige UNE question fréquente (FAQ) très pertinente et sa réponse pour la catégorie donnée. Ta réponse doit refléter notre philosophie artisanale, l'authenticité du bambou et l'exigence du fait-main.",
            $now,
            $now,
        ]);

        // Seed 3: Product Description
        $this->addSql('INSERT INTO app_ai_prompt (label, type, content, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)', [
            'Description Fiche Produit',
            'product_description',
            "Rédige une description poétique et raffinée en Français pour cette création artisanale en bambou de la Maison ZEN TOO CRAFT. Mets en valeur le geste de l'artisan, la noblesse et l'acoustique du végétal, ainsi que la singularité de la pièce. Adopte un ton élégant et chaleureux, sans aucun emoji ni jargon commercial agressif.",
            $now,
            $now,
        ]);

        // Seed 4: Taxon Description
        $this->addSql('INSERT INTO app_ai_prompt (label, type, content, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)', [
            'Description Catégorie & Catalogue',
            'taxon_description',
            "Tu es le directeur artistique de la Maison ZEN TOO CRAFT. Rédige une présentation raffinée et élégante en Français pour la collection d'objets en bambou indiquée. Mets en valeur la pureté du végétal, le travail acoustique ou esthétique de l'atelier, et l'exclusivité du fait-main.",
            $now,
            $now,
        ]);

        // Seed 5: Blog
        $this->addSql('INSERT INTO app_ai_prompt (label, type, content, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)', [
            'Journal de l\'Artisan (Blog)',
            'blog',
            "Tu es le maître artisan et luthier de ZEN TOO CRAFT. Tu rédiges pour le Journal de l'Artisan. Partage un récit authentique sur le travail du bambou, la lutherie, les techniques de façonnage ou la vie de l'atelier. Reste humble, passionné et précis dans les termes de menuiserie et d'acoustique.",
            $now,
            $now,
        ]);

        // Seed 6: Translation
        $this->addSql('INSERT INTO app_ai_prompt (label, type, content, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?)', [
            'Moteur de Traduction Multilingue',
            'translation',
            "You are a professional translator for luxury handcrafted art studio ZEN TOO CRAFT. Translate the text with high fidelity, preserving our poetic, authentic and artisanal brand voice.",
            $now,
            $now,
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE app_ai_prompt');
    }
}
