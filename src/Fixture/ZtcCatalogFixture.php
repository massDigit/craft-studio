<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Product\Product;
use App\Entity\Product\ProductAudio;
use App\Entity\Product\ProductTaxon;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ZtcCatalogFixture extends AbstractFixture
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RepositoryInterface $taxonRepository,
        private RepositoryInterface $productRepository,
        private RepositoryInterface $channelRepository,
        private FactoryInterface $taxonFactory,
        private FactoryInterface $productFactory,
        private FactoryInterface $productVariantFactory,
        private FactoryInterface $channelPricingFactory
    ) {
    }

    public function getName(): string
    {
        return 'ztc_catalog';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        // Custom fixture options if needed
    }

    public function load(array $options): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy([]);
        $locale = $channel ? $channel->getDefaultLocale()->getCode() : 'fr_FR';

        // Root Taxon "category"
        /** @var TaxonInterface|null $rootTaxon */
        $rootTaxon = $this->taxonRepository->findOneBy(['code' => 'category']);
        if (!$rootTaxon) {
            /** @var TaxonInterface $rootTaxon */
            $rootTaxon = $this->taxonFactory->createNew();
            $rootTaxon->setCode('category');
            $rootTaxon->setCurrentLocale($locale);
            $rootTaxon->setFallbackLocale($locale);
            $rootTaxon->setName('Catégories ZEN TOO Craft');
            $rootTaxon->setSlug('categories');
            $this->entityManager->persist($rootTaxon);
        }

        // 1. Taxon Instruments
        $instrumentsTaxon = $this->getOrCreateTaxon('instruments', 'Instruments à Vent & Créations Sonores', 'instruments-a-vent', $rootTaxon, $locale);

        // 2. Taxon Luminaires
        $luminairesTaxon = $this->getOrCreateTaxon('luminaires', 'Luminaires Artistiques Ajourés', 'luminaires-ajoures', $rootTaxon, $locale);

        // 3. Taxon Décoration
        $decorationsTaxon = $this->getOrCreateTaxon('decorations', 'Objets Décoratifs', 'objets-decoratifs', $rootTaxon, $locale);

        $this->entityManager->flush();

        // Sample Product 1: Flûte Shakuhachi
        $this->createSampleProduct(
            'flute-shakuhachi-meditative',
            'Flûte Shakuhachi Méditative',
            'Flûte artisanale en tige de bambou séchée naturellement. Sonorité profonde et spirituelle accordée en La 440 Hz.',
            $instrumentsTaxon,
            $channel,
            $locale,
            18000,
            'demo_shakuhachi.mp3'
        );

        // Sample Product 2: Luminaire Bambou
        $this->createSampleProduct(
            'luminaire-ombre-bambou',
            'Luminaire Ombre & Bambou',
            'Structure en bambou ajouré ciselée à la main. Projette des motifs d\'ombres dorées chaleureuses sur vos murs.',
            $luminairesTaxon,
            $channel,
            $locale,
            24000
        );

        // Sample Product 3: Totem Végétal
        $this->createSampleProduct(
            'totem-vegetal-equilibre',
            'Totem Végétal Équilibre',
            'Création décorative épurée assemblant différentes variétés de bambou poli à la cire naturelle.',
            $decorationsTaxon,
            $channel,
            $locale,
            15000
        );

        $this->entityManager->flush();
    }

    private function getOrCreateTaxon(string $code, string $name, string $slug, TaxonInterface $parent, string $locale): TaxonInterface
    {
        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);
        if (!$taxon) {
            /** @var TaxonInterface $taxon */
            $taxon = $this->taxonFactory->createNew();
            $taxon->setCode($code);
            $taxon->setParent($parent);
            $taxon->setCurrentLocale($locale);
            $taxon->setFallbackLocale($locale);
            $taxon->setName($name);
            $taxon->setSlug($slug);
            $this->entityManager->persist($taxon);
        }

        return $taxon;
    }

    private function createSampleProduct(
        string $code,
        string $name,
        string $description,
        TaxonInterface $taxon,
        ?ChannelInterface $channel,
        string $locale,
        int $priceInCents = 10000,
        ?string $audioFileName = null
    ): void {
        /** @var Product|null $product */
        $product = $this->productRepository->findOneBy(['code' => $code]);
        if (!$product) {
            /** @var Product $product */
            $product = $this->productFactory->createNew();
            $product->setCode($code);
            $product->setCurrentLocale($locale);
            $product->setFallbackLocale($locale);
            $product->setName($name);
            $product->setSlug($code);
            $product->setDescription($description);
            $product->setShortDescription(mb_substr($description, 0, 100) . '...');
            $product->setMainTaxon($taxon);

            $productTaxon = new ProductTaxon();
            $productTaxon->setProduct($product);
            $productTaxon->setTaxon($taxon);
            $product->addProductTaxon($productTaxon);

            if ($channel) {
                $product->addChannel($channel);
            }

            if ($audioFileName) {
                $audio = new ProductAudio();
                $audio->setPath($audioFileName);
                $audio->setOriginalName('Extrait Sonore — ' . $name . '.mp3');
                $audio->setMimeType('audio/mpeg');
                $audio->setIsPrimary(true);
                $product->addAudio($audio);
            }

            $this->entityManager->persist($product);
        }

        if ($product->getVariants()->isEmpty()) {
            /** @var ProductVariantInterface $variant */
            $variant = $this->productVariantFactory->createNew();
            $variant->setCode($code . '-default');
            $variant->setName($name);
            $variant->setProduct($product);

            if ($channel) {
                /** @var ChannelPricingInterface $channelPricing */
                $channelPricing = $this->channelPricingFactory->createNew();
                $channelPricing->setChannelCode($channel->getCode());
                $channelPricing->setPrice($priceInCents);
                $channelPricing->setProductVariant($variant);
                $variant->addChannelPricing($channelPricing);
            }

            $product->addVariant($variant);
            $this->entityManager->persist($variant);
        }
    }
}
