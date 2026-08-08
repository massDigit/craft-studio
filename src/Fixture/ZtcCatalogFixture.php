<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Product\Product;
use App\Entity\Product\ProductAudio;
use App\Entity\Product\ProductTaxon;
use App\Entity\Product\ProductTranslation;
use App\Entity\Taxonomy\TaxonTranslation;
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
    }

    public function load(array $options): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy([]);

        // Root Taxon "category"
        /** @var TaxonInterface|null $rootTaxon */
        $rootTaxon = $this->taxonRepository->findOneBy(['code' => 'category']);
        if (!$rootTaxon) {
            /** @var TaxonInterface $rootTaxon */
            $rootTaxon = $this->taxonFactory->createNew();
            $rootTaxon->setCode('category');
            $this->addTaxonTranslation($rootTaxon, 'fr', 'Catégories ZEN TOO Craft', 'categories');
            $this->addTaxonTranslation($rootTaxon, 'en', 'ZEN TOO Craft Categories', 'categories');
            $this->entityManager->persist($rootTaxon);
        }

        // 1. Taxon Instruments
        $instrumentsTaxon = $this->getOrCreateTaxon(
            'instruments',
            [
                'fr' => ['name' => 'Instruments à Vent & Créations Sonores', 'slug' => 'instruments-a-vent'],
                'en' => ['name' => 'Wind Instruments & Sound Creations', 'slug' => 'wind-instruments'],
            ],
            $rootTaxon
        );

        // 2. Taxon Luminaires
        $luminairesTaxon = $this->getOrCreateTaxon(
            'luminaires',
            [
                'fr' => ['name' => 'Luminaires Artistiques Ajourés', 'slug' => 'luminaires-ajoures'],
                'en' => ['name' => 'Artistic Openwork Lighting', 'slug' => 'artistic-lighting'],
            ],
            $rootTaxon
        );

        // 3. Taxon Décoration
        $decorationsTaxon = $this->getOrCreateTaxon(
            'decorations',
            [
                'fr' => ['name' => 'Objets Décoratifs', 'slug' => 'objets-decoratifs'],
                'en' => ['name' => 'Decorative Objects', 'slug' => 'decorative-objects'],
            ],
            $rootTaxon
        );

        $this->entityManager->flush();

        // Sample Product 1: Flûte Shakuhachi
        $this->createSampleProduct(
            'flute-shakuhachi-meditative',
            [
                'fr' => [
                    'name' => 'Flûte Shakuhachi Méditative',
                    'description' => 'Flûte artisanale en tige de bambou séchée naturellement. Sonorité profonde et spirituelle accordée en La 440 Hz.',
                ],
                'en' => [
                    'name' => 'Meditative Shakuhachi Flute',
                    'description' => 'Handcrafted flute made from naturally dried bamboo stalks. Deep, spiritual acoustics tuned to A 440 Hz.',
                ],
            ],
            $instrumentsTaxon,
            $channel,
            18000,
            'demo_shakuhachi.mp3'
        );

        // Sample Product 2: Luminaire Bambou
        $this->createSampleProduct(
            'luminaire-ombre-bambou',
            [
                'fr' => [
                    'name' => 'Luminaire Ombre & Bambou',
                    'description' => 'Structure en bambou ajouré ciselée à la main. Projette des motifs d\'ombres dorées chaleureuses sur vos murs.',
                ],
                'en' => [
                    'name' => 'Obsidian & Gold Bamboo Light',
                    'description' => 'Hand-chiseled openwork bamboo lamp structure. Casts warm golden shadow patterns on your walls.',
                ],
            ],
            $luminairesTaxon,
            $channel,
            24000
        );

        // Sample Product 3: Totem Végétal
        $this->createSampleProduct(
            'totem-vegetal-equilibre',
            [
                'fr' => [
                    'name' => 'Totem Végétal Équilibre',
                    'description' => 'Création décorative épurée assemblant différentes variétés de bambou poli à la cire naturelle.',
                ],
                'en' => [
                    'name' => 'Equilibrium Botanical Totem',
                    'description' => 'Sleek decorative sculpture combining hand-polished bamboo varieties finished with natural beeswax.',
                ],
            ],
            $decorationsTaxon,
            $channel,
            15000
        );

        $this->entityManager->flush();
    }

    private function getOrCreateTaxon(string $code, array $translations, TaxonInterface $parent): TaxonInterface
    {
        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);
        if (!$taxon) {
            /** @var TaxonInterface $taxon */
            $taxon = $this->taxonFactory->createNew();
            $taxon->setCode($code);
            $taxon->setParent($parent);

            foreach ($translations as $locale => $data) {
                $this->addTaxonTranslation($taxon, $locale, $data['name'], $data['slug']);
            }

            $this->entityManager->persist($taxon);
        }

        return $taxon;
    }

    private function addTaxonTranslation(TaxonInterface $taxon, string $locale, string $name, string $slug): void
    {
        $translation = $taxon->getTranslation($locale);
        if ($translation->getLocale() !== $locale) {
            $translation = new TaxonTranslation();
            $translation->setLocale($locale);
            $taxon->addTranslation($translation);
        }
        $translation->setName($name);
        $translation->setSlug($slug);
    }

    private function createSampleProduct(
        string $code,
        array $translations,
        TaxonInterface $taxon,
        ?ChannelInterface $channel,
        int $priceInCents = 10000,
        ?string $audioFileName = null
    ): void {
        /** @var Product|null $product */
        $product = $this->productRepository->findOneBy(['code' => $code]);
        if (!$product) {
            /** @var Product $product */
            $product = $this->productFactory->createNew();
            $product->setCode($code);
            $product->setMainTaxon($taxon);

            foreach ($translations as $locale => $data) {
                $this->addProductTranslation($product, $locale, $data['name'], $data['description']);
            }

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
                $audio->setOriginalName('Extrait Sonore — ' . ($translations['fr']['name'] ?? $code) . '.mp3');
                $audio->setMimeType('audio/mpeg');
                $audio->setIsPrimary(true);
                $product->addAudio($audio);
            }

            $this->entityManager->persist($product);
        } else {
            foreach ($translations as $locale => $data) {
                $this->addProductTranslation($product, $locale, $data['name'], $data['description']);
            }
        }

        if ($product->getVariants()->isEmpty()) {
            /** @var ProductVariantInterface $variant */
            $variant = $this->productVariantFactory->createNew();
            $variant->setCode($code . '-default');
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

    private function addProductTranslation(Product $product, string $locale, string $name, string $description): void
    {
        $translation = $product->getTranslation($locale);
        if ($translation->getLocale() !== $locale) {
            $translation = new ProductTranslation();
            $translation->setLocale($locale);
            $product->addTranslation($translation);
        }
        $translation->setName($name);
        $translation->setSlug(sprintf('%s-%s', $product->getCode(), $locale));
        $translation->setDescription($description);
        $translation->setShortDescription(mb_substr($description, 0, 100) . '...');
    }
}
