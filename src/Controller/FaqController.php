<?php

declare(strict_types=1);

namespace App\Controller;

use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Taxonomy\Taxon;

class FaqController extends AbstractController
{
    public function __construct(
        private readonly TaxonRepositoryInterface $taxonRepository
    ) {
    }

    #[Route('/{_locale}/faq', name: 'app_shop_faq', methods: ['GET'], requirements: ['_locale' => '^[a-z]{2}(?:_[A-Z]{2})?$'])]
    public function index(): Response
    {
        $allTaxons = $this->taxonRepository->findAll();
        $taxonsWithFaq = [];
        
        foreach ($allTaxons as $taxon) {
            if (method_exists($taxon, 'getFaqItems') && $taxon->getFaqItems()->count() > 0) {
                $taxonsWithFaq[] = $taxon;
            }
        }

        return $this->render('shop/faq/index.html.twig', [
            'taxons' => $taxonsWithFaq,
        ]);
    }
}
