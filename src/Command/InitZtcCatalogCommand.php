<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product\Product;
use App\Entity\Product\ProductAudio;
use App\Entity\Product\ProductImage;
use App\Entity\Product\ProductTaxon;
use App\Entity\Product\ProductTranslation;
use App\Entity\Taxonomy\TaxonTranslation;
use App\Entity\User\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'ztc:catalog:init',
    description: 'Initialise les taxons, images et créations ZEN TOO Craft',
)]
class InitZtcCatalogCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RepositoryInterface $taxonRepository,
        private RepositoryInterface $productRepository,
        private RepositoryInterface $channelRepository,
        private FactoryInterface $taxonFactory,
        private FactoryInterface $productFactory,
        private FactoryInterface $productVariantFactory,
        private FactoryInterface $channelPricingFactory,
        private FactoryInterface $channelFactory,
        private RepositoryInterface $localeRepository,
        private RepositoryInterface $currencyRepository,
        private FactoryInterface $localeFactory,
        private FactoryInterface $currencyFactory,
        #[Target('sylius.factory.admin_user')]
        private FactoryInterface $adminUserFactory,
        #[Target('sylius.repository.admin_user')]
        private RepositoryInterface $adminUserRepository,
        private UserPasswordHasherInterface $passwordHasher,
        /** @phpstan-ignore property.onlyWritten */
        private string $projectDir = '',
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Initialisation du catalogue et des visuels ZEN TOO Craft...</info>');

        /** @var \Sylius\Component\Locale\Model\LocaleInterface|null $frLocale */
        $frLocale = $this->localeRepository->findOneBy(['code' => 'fr']);
        if (!$frLocale) {
            /** @var \Sylius\Component\Locale\Model\LocaleInterface $frLocale */
            $frLocale = $this->localeFactory->createNew();
            $frLocale->setCode('fr');
            $this->entityManager->persist($frLocale);
        }

        /** @var \Sylius\Component\Locale\Model\LocaleInterface|null $enLocale */
        $enLocale = $this->localeRepository->findOneBy(['code' => 'en']);
        if (!$enLocale) {
            /** @var \Sylius\Component\Locale\Model\LocaleInterface $enLocale */
            $enLocale = $this->localeFactory->createNew();
            $enLocale->setCode('en');
            $this->entityManager->persist($enLocale);
        }

        /** @var \Sylius\Component\Currency\Model\CurrencyInterface|null $eurCurrency */
        $eurCurrency = $this->currencyRepository->findOneBy(['code' => 'EUR']);
        if (!$eurCurrency) {
            /** @var \Sylius\Component\Currency\Model\CurrencyInterface $eurCurrency */
            $eurCurrency = $this->currencyFactory->createNew();
            $eurCurrency->setCode('EUR');
            $this->entityManager->persist($eurCurrency);
        }

        $this->entityManager->flush();

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy([]);
        if (!$channel) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelFactory->createNew();
            $channel->setCode('ZTC_STORE');
            $channel->setName('ZEN TOO Craft');
            $channel->setHostname(null);
            $channel->setEnabled(true);
            $channel->addLocale($frLocale);
            $channel->addLocale($enLocale);
            $channel->setDefaultLocale($frLocale);
            $channel->addCurrency($eurCurrency);
            $channel->setBaseCurrency($eurCurrency);
            $this->entityManager->persist($channel);
            $this->entityManager->flush();
        }

        /** @var AdminUser|null $adminUser */
        $adminUser = $this->adminUserRepository->findOneBy(['email' => 'sylius@example.com']);
        if (!$adminUser) {
            /** @var AdminUser $adminUser */
            $adminUser = $this->adminUserFactory->createNew();
            $adminUser->setEmail('sylius@example.com');
            $adminUser->setUsername('sylius');
            $adminUser->setPlainPassword('sylius');
            $adminUser->setEnabled(true);
            $adminUser->setLocaleCode('fr');
            $adminUser->setFirstName('ZEN');
            $adminUser->setLastName('Artisan');

            $hashedPassword = $this->passwordHasher->hashPassword($adminUser, 'sylius');
            $adminUser->setPassword($hashedPassword);

            $this->entityManager->persist($adminUser);
            $this->entityManager->flush();
            $output->writeln('<comment>Compte Administrateur Back-Office (sylius@example.com / sylius) créé avec succès !</comment>');
        }

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
            $rootTaxon,
        );

        // 2. Taxon Luminaires
        $luminairesTaxon = $this->getOrCreateTaxon(
            'luminaires',
            [
                'fr' => ['name' => 'Luminaires Artistiques Ajourés', 'slug' => 'luminaires-ajoures'],
                'en' => ['name' => 'Artistic Openwork Lighting', 'slug' => 'artistic-lighting'],
            ],
            $rootTaxon,
        );

        // 3. Taxon Décoration
        $decorationsTaxon = $this->getOrCreateTaxon(
            'decorations',
            [
                'fr' => ['name' => 'Objets Décoratifs', 'slug' => 'objets-decoratifs'],
                'en' => ['name' => 'Decorative Objects', 'slug' => 'decorative-objects'],
            ],
            $rootTaxon,
        );

        $this->entityManager->flush();

        $output->writeln('<comment>Taxons bilingues (fr/en) créés avec succès !</comment>');

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
            'flute_shakuhachi.jpeg',
            'demo_shakuhachi.mp3',
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
            24000,
            'luminaire_ombre.jpeg',
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
            15000,
        );

        $this->entityManager->flush();

        $output->writeln('<info>Catalogue et visuels produits ZEN TOO Craft initialisés avec succès !</info>');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, array{name: string, slug: string}> $translations
     */
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

    /**
     * @param array<string, array{name: string, description: string}> $translations
     */
    private function createSampleProduct(
        string $code,
        array $translations,
        TaxonInterface $taxon,
        ?ChannelInterface $channel,
        int $priceInCents = 10000,
        ?string $imageFileName = null,
        ?string $audioFileName = null,
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

            if ($imageFileName) {
                $image = new ProductImage();
                $image->setPath($imageFileName);
                $image->setType('main');
                $product->addImage($image);
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

            if ($imageFileName && $product->getImages()->isEmpty()) {
                $image = new ProductImage();
                $image->setPath($imageFileName);
                $image->setType('main');
                $product->addImage($image);
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
