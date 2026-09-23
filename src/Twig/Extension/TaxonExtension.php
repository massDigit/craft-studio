<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Expose le taxon courant dans les templates Twig à partir du slug de la requête.
 * Nécessaire car en Sylius 2.x avec TwigHooks, le taxon n'est pas injecté
 * dans le bloc `content` des templates overridés.
 */
class TaxonExtension extends AbstractExtension
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly LocaleContextInterface $localeContext,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ztc_current_taxon', $this->getCurrentTaxon(...)),
            new TwigFunction('ztc_get_taxon_by_code', $this->getTaxonByCode(...)),
            new TwigFunction('ztc_get_subtaxons_with_products', $this->getSubtaxonsWithProducts(...)),
            new TwigFunction('ztc_short_taxon_name', $this->getShortTaxonName(...)),
            new TwigFunction('ztc_is_taxon_active', $this->isTaxonActive(...)),
        ];
    }

    public function isTaxonActive(\Sylius\Component\Taxonomy\Model\TaxonInterface $menuTaxon): bool
    {
        $currentTaxon = $this->getCurrentTaxon();
        if (null === $currentTaxon) {
            return false;
        }

        if ($currentTaxon->getId() === $menuTaxon->getId()) {
            return true;
        }

        $parent = $currentTaxon->getParent();
        while (null !== $parent) {
            if ($parent->getId() === $menuTaxon->getId()) {
                return true;
            }
            $parent = $parent->getParent();
        }

        return false;
    }

    public function getTaxonByCode(string $code): ?TaxonInterface
    {
        return $this->taxonRepository->findOneBy(['code' => $code]);
    }

    public function getCurrentTaxon(): ?TaxonInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        $slug = $request->attributes->get('slug');
        if (null === $slug || '' === $slug) {
            return null;
        }

        try {
            $locale = $this->localeContext->getLocaleCode();
        } catch (\Throwable) {
            $locale = 'fr';
        }

        return $this->taxonRepository->findOneBySlug((string) $slug, $locale);
    }

    /**
     * @return array<TaxonInterface>
     */
    public function getSubtaxonsWithProducts(TaxonInterface $taxon): array
    {
        $children = $taxon->getChildren();
        if ($children->isEmpty()) {
            return [];
        }

        $subtaxonsWithProducts = [];
        $connection = $this->entityManager->getConnection();

        foreach ($children as $child) {
            $count = (int) $connection->fetchOne(
                'SELECT COUNT(pt.id) 
                 FROM sylius_product_taxon pt 
                 JOIN sylius_product p ON p.id = pt.product_id 
                 WHERE pt.taxon_id = :taxonId AND p.enabled = 1',
                ['taxonId' => $child->getId()]
            );

            if ($count > 0) {
                $subtaxonsWithProducts[] = $child;
            }
        }

        return $subtaxonsWithProducts;
    }

    public function getShortTaxonName(TaxonInterface $taxon): string
    {
        $code = (string) $taxon->getCode();
        $name = (string) $taxon->getName();

        $mapping = [
            'instruments' => 'Instruments à Vent',
            'luminaires' => 'Luminaires',
            'decorations' => 'Décoration',
            'luce' => 'Lumière',
        ];

        if (isset($mapping[$code])) {
            return $mapping[$code];
        }

        // Split on '&' if present (take first part)
        if (str_contains($name, '&')) {
            $parts = explode('&', $name);
            return trim($parts[0]);
        }

        // Split on '/' if present
        if (str_contains($name, '/')) {
            $parts = explode('/', $name);
            return trim($parts[0]);
        }

        return $name;
    }
}
