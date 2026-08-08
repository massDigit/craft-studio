<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product\Product;
use App\Entity\Product\ProductAudio;
use App\Entity\Product\ProductTaxon;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ztc:catalog:init',
    description: 'Initialise les taxons et créations de démonstration ZEN TOO Craft'
)]
class InitZtcCatalogCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RepositoryInterface $taxonRepository,
        private RepositoryInterface $productRepository,
        private RepositoryInterface $channelRepository,
        private FactoryInterface $taxonFactory,
        private FactoryInterface $productFactory
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Initialisation du catalogue ZEN TOO Craft...</info>');

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

        $output->writeln('<comment>Taxons ZEN TOO Craft créés avec succès !</comment>');

        // Sample Product 1: Flûte Shakuhachi
        $this->createSampleProduct(
            'flute-shakuhachi-meditative',
            'Flûte Shakuhachi Méditative',
            'Flûte artisanale en tige de bambou séchée naturellement. Sonorité profonde et spirituelle accordée en La 440 Hz.',
            $instrumentsTaxon,
            $channel,
            $locale,
            'demo_shakuhachi.mp3'
        );

        // Sample Product 2: Luminaire Bambou
        $this->createSampleProduct(
            'luminaire-ombre-bambou',
            'Luminaire Ombre & Bambou',
            'Structure en bambou ajouré ciselée à la main. Projette des motifs d\'ombres dorées chaleureuses sur vos murs.',
            $luminairesTaxon,
            $channel,
            $locale
        );

        // Sample Product 3: Totem Végétal
        $this->createSampleProduct(
            'totem-vegetal-equilibre',
            'Totem Végétal Équilibre',
            'Création décorative épurée assemblant différentes variétés de bambou poli à la cire naturelle.',
            $decorationsTaxon,
            $channel,
            $locale
        );

        $this->entityManager->flush();

        $output->writeln('<info>Catalogue ZEN TOO Craft initialisé avec succès !</info>');

        return Command::SUCCESS;
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
        ?string $audioFileName = null
    ): void {
        /** @var Product|null $existing */
        $existing = $this->productRepository->findOneBy(['code' => $code]);
        if ($existing) {
            return;
        }

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
}
