<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Blog\BlogPost;
use App\Entity\Product\Product;
use App\Entity\Product\ProductAudio;
use App\Entity\Product\ProductImage;
use App\Entity\Product\ProductTaxon;
use App\Entity\Product\ProductTranslation;
use App\Entity\Taxonomy\TaxonTranslation;
use App\Entity\User\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\CmsPlugin\Entity\Collection;
use Sylius\CmsPlugin\Entity\Page;
use Sylius\CmsPlugin\Entity\PageTranslation;
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
        #[Target('sylius_cms.factory.page')]
        private FactoryInterface $pageFactory,
        #[Target('sylius_cms.repository.page')]
        private RepositoryInterface $pageRepository,
        #[Target('sylius_cms.factory.collection')]
        private FactoryInterface $collectionFactory,
        #[Target('sylius_cms.repository.collection')]
        private RepositoryInterface $collectionRepository,
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
            $this->entityManager->flush();
        }

        if (!$channel->getMenuTaxon()) {
            $channel->setMenuTaxon($rootTaxon);
            $this->entityManager->persist($channel);
            $this->entityManager->flush();
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

        // 4. Pages CMS Éditoriales (Blog)
        $pageArtisan = $this->createCmsPage(
            'artisan-zen-too-craft',
            [
                'fr' => [
                    'title' => 'L\'Artisan ZEN TOO Craft',
                    'slug' => 'l-artisan-zen-too-craft',
                    'content' => '<h2>L\'Art du Bambou & du Son</h2><p>L\'atelier ZEN TOO Craft façonne des pièces uniques sculptées à la main dans le respect de la matière brute et de la nature.</p>',
                ],
                'en' => [
                    'title' => 'The ZEN TOO Craft Artisan',
                    'slug' => 'the-zen-too-craft-artisan',
                    'content' => '<h2>The Art of Bamboo & Sound</h2><p>The ZEN TOO Craft workshop creates unique hand-carved pieces respecting raw materials and nature.</p>',
                ],
            ],
            $channel,
        );

        $pageSavoirFaire = $this->createCmsPage(
            'savoir-faire-bambou',
            [
                'fr' => [
                    'title' => 'Savoir-Faire & Charte Éco-Responsable',
                    'slug' => 'savoir-faire-bambou',
                    'content' => '<h2>Artisanat Éco-Responsable</h2><p>Sélection naturelle des tiges de bambou, séchage au soleil, polissage à la cire bio d\'abeille et accordage acoustique de précision (La 440 Hz / 432 Hz).</p>',
                ],
                'en' => [
                    'title' => 'Craftsmanship & Eco-Responsible Charter',
                    'slug' => 'craftsmanship-bamboo',
                    'content' => '<h2>Eco-Friendly Craftsmanship</h2><p>Natural selection of bamboo stalks, sun drying, organic beeswax polishing, and precision acoustic tuning (A 440 Hz / 432 Hz).</p>',
                ],
            ],
            $channel,
        );

        // 5. Pages Institutionnelles & Légales (Footer)
        $pageMentions = $this->createCmsPage(
            'mentions-legales',
            [
                'fr' => [
                    'title' => 'Mentions Légales',
                    'slug' => 'mentions-legales',
                    'content' => '<h2>Mentions Légales & Crédits</h2><p>Éditeur du site : Studio ZEN TOO Craft. Tous droits réservés.</p>',
                ],
                'en' => [
                    'title' => 'Legal Notice',
                    'slug' => 'legal-notice',
                    'content' => '<h2>Legal Notice & Credits</h2><p>Publisher: ZEN TOO Craft Studio. All rights reserved.</p>',
                ],
            ],
            $channel,
        );

        $pageCgv = $this->createCmsPage(
            'conditions-generales-de-vente',
            [
                'fr' => [
                    'title' => 'Conditions Générales de Vente (CGV)',
                    'slug' => 'cgv',
                    'content' => '<h2>Conditions Générales de Vente</h2><p>Les présentes conditions régissent l\'achat d\'objets artisanaux sur l\'atelier en ligne ZEN TOO Craft.</p>',
                ],
                'en' => [
                    'title' => 'Terms & Conditions',
                    'slug' => 'terms-conditions',
                    'content' => '<h2>Terms & Conditions</h2><p>These terms govern the purchase of handcrafted creations on ZEN TOO Craft online studio.</p>',
                ],
            ],
            $channel,
        );

        // 6. Articles du Journal (Blog Dédié)
        $this->createBlogPost(
            'artisan-zen-too-craft',
            'L\'Artisan ZEN TOO Craft',
            'l-artisan-zen-too-craft',
            'L\'atelier ZEN TOO Craft façonne des pièces uniques sculptées à la main dans le respect de la matière brute et de la nature.',
            '<h2>L\'Art du Bambou & du Son</h2><p>L\'atelier ZEN TOO Craft façonne des pièces uniques sculptées à la main dans le respect de la matière brute et de la nature.</p>',
            'flute_shakuhachi.jpeg',
        );

        $this->createBlogPost(
            'savoir-faire-bambou',
            'Savoir-Faire & Charte Éco-Responsable',
            'savoir-faire-bambou',
            'Sélection naturelle des tiges de bambou, séchage au soleil, polissage à la cire bio d\'abeille et accordage acoustique de précision (La 440 Hz / 432 Hz).',
            '<h2>Artisanat Éco-Responsable</h2><p>Sélection naturelle des tiges de bambou, séchage au soleil, polissage à la cire bio d\'abeille et accordage acoustique de précision (La 440 Hz / 432 Hz).</p>',
            'luminaire_ombre.jpeg',
        );

        // 7. Collections CMS (Footer)
        $this->getOrCreateCmsCollection('footer_menu', 'Footer — Informations Légales', [$pageMentions, $pageCgv]);

        $this->entityManager->flush();

        $output->writeln('<info>Catalogue, visuels, pages CMS et articles du journal initialisés avec succès !</info>');

        return Command::SUCCESS;
    }

    private function createBlogPost(
        string $code,
        string $title,
        string $slug,
        string $excerpt,
        string $content,
        ?string $coverImage = null,
    ): void {
        $blogPost = $this->entityManager->getRepository(BlogPost::class)->findOneBy(['code' => $code]);
        if (!$blogPost) {
            $blogPost = new BlogPost();
            $blogPost->setCode($code);
            $blogPost->setTitle($title);
            $blogPost->setSlug($slug);
            $blogPost->setExcerpt($excerpt);
            $blogPost->setContent($content);
            $blogPost->setCoverImage($coverImage);
            $blogPost->setAuthor('ZEN TOO Craft');
            $blogPost->setPublished(true);
            $blogPost->setPublishedAt(new \DateTimeImmutable());
            $this->entityManager->persist($blogPost);
        }
    }

    /**
     * @param array<Page|null> $pages
     */
    private function getOrCreateCmsCollection(string $code, string $name, array $pages): void
    {
        /** @var Collection|null $collection */
        $collection = $this->collectionRepository->findOneBy(['code' => $code]);
        if (!$collection) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->createNew();
            $collection->setCode($code);
            $collection->setName($name);
            $this->entityManager->persist($collection);
        } else {
            $collection->setName($name);
            $collection->getPages()?->clear();
        }

        foreach ($pages as $page) {
            if ($page && !$collection->hasPage($page)) {
                $collection->addPage($page);
            }
        }
    }

    /**
     * @param array<string, array{title: string, slug: string, content: string}> $translations
     */
    private function createCmsPage(string $code, array $translations, ?ChannelInterface $channel): Page
    {
        /** @var Page|null $page */
        $page = $this->pageRepository->findOneBy(['code' => $code]);
        if (!$page) {
            /** @var Page $page */
            $page = $this->pageFactory->createNew();
            $page->setCode($code);
            $page->setName($translations['fr']['title'] ?? $code);
            $page->setEnabled(true);

            if ($channel) {
                $page->addChannel($channel);
            }

            foreach ($translations as $locale => $data) {
                $translation = new PageTranslation();
                $translation->setLocale($locale);
                $translation->setTitle($data['title']);
                $translation->setSlug($data['slug']);
                $translation->setMetaDescription(strip_tags($data['content']));
                $page->addTranslation($translation);
            }

            $this->entityManager->persist($page);
        } else {
            $page->setEnabled(true);
            if ($channel && !$page->hasChannel($channel)) {
                $page->addChannel($channel);
            }
        }

        return $page;
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
            $product->setEnabled(true);
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
            $product->setEnabled(true);
            $product->setMainTaxon($taxon);

            if ($channel && !$product->hasChannel($channel)) {
                $product->addChannel($channel);
            }

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
            $product->addVariant($variant);
            $this->entityManager->persist($variant);
        }

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $variant) {
            if ($channel && !$variant->hasChannelPricingForChannel($channel)) {
                /** @var ChannelPricingInterface $channelPricing */
                $channelPricing = $this->channelPricingFactory->createNew();
                $channelPricing->setChannelCode($channel->getCode());
                $channelPricing->setPrice($priceInCents);
                $channelPricing->setProductVariant($variant);
                $variant->addChannelPricing($channelPricing);
                $this->entityManager->persist($channelPricing);
            }
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
