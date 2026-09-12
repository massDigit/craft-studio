<?php

declare(strict_types=1);

namespace App\Twig\Extension;

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
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ztc_current_taxon', $this->getCurrentTaxon(...)),
            new TwigFunction('ztc_get_taxon_by_code', $this->getTaxonByCode(...)),
        ];
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
}
