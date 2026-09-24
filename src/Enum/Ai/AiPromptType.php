<?php

declare(strict_types=1);

namespace App\Enum\Ai;

enum AiPromptType: string
{
    case BRAND_CONTEXT = 'brand_context';
    case FAQ = 'faq';
    case PRODUCT_DESCRIPTION = 'product_description';
    case TAXON_DESCRIPTION = 'taxon_description';
    case BLOG = 'blog';
    case TRANSLATION = 'translation';

    public function getLabel(): string
    {
        return match ($this) {
            self::BRAND_CONTEXT => 'Contexte Artisan & Marque',
            self::FAQ => 'Génération FAQ (Questions / Réponses)',
            self::PRODUCT_DESCRIPTION => 'Description Produit',
            self::TAXON_DESCRIPTION => 'Description Catégorie (Taxon)',
            self::BLOG => 'Article de Blog',
            self::TRANSLATION => 'Traduction',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::BRAND_CONTEXT => 'bg-purple-subtle text-purple',
            self::FAQ => 'bg-azure-subtle text-azure',
            self::PRODUCT_DESCRIPTION => 'bg-green-subtle text-green',
            self::TAXON_DESCRIPTION => 'bg-teal-subtle text-teal',
            self::BLOG => 'bg-orange-subtle text-orange',
            self::TRANSLATION => 'bg-indigo-subtle text-indigo',
        };
    }
}
