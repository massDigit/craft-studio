# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-08-08
**Branche Courante** : `main`

## 🚀 Phase Actuelle
Phase 6 : Pages Éditoriales CMS & Finalisation de la Vitrine (EN COURS).

## 🎯 Focus Courant
Création des pages éditoriales CMS avec `sylius/cms-plugin` (Histoire du Savoir-Faire du Bambou, Charte Éco-Responsable, Mentions Légales).

## ✅ Réalisé
- [x] Initialisation du socle Sylius 2.2 / Symfony 7.4 et installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1).
- [x] Création de la ressource Sylius `App\Entity\Product\ProductAudio` rattachée au `Product` natif avec une cardinalité ouverte (`ManyToOne`).
- [x] Déclaration de la ressource `app.product_audio` dans `config/packages/sylius_resource.yaml`.
- [x] Création du formulaire `ProductAudioType` et extension du `ProductType` dans Sylius Admin.
- [x] Implémentation du listener d'événement `ProductAudioUploadListener` pour la gestion des téléversements et de la suppression physique des fichiers audio (`public/media/audio`).
- [x] Migration de l'environnement Docker vers PHP 8.4 (`ghcr.io/sylius/sylius-php:8.4-fixuid-xdebug-alpine`) et démarrage des conteneurs (`make up`).
- [x] Création de la base de données `sylius_dev`, chargement des tables et des fixtures par défaut Sylius (`sylius:fixtures:load`).
- [x] Implémentation de la commande `ztc:catalog:init` et création des Taxons natifs de référence (Instruments à vent, Décoration, Luminaires ajourés).
- [x] Configuration de la locale principale en `fr` et intégration bilingue (`fr` & `en`) des créations et taxons.
- [x] Raccordement des visuels uploadés (`flute_shakuhachi.jpeg`, `luminaire_ombre.jpeg`) aux créations et validation de la génération des vignettes LiipImagine (`HTTP 200 OK`).
- [x] Intégration du Design System CSS "Sombre Obsidienne & Or Végétal" (`assets/shop/styles/zen_too_craft.css`) et des typographies Google Fonts (*Outfit* + *Inter*).
- [x] Création du composant lecteur audio HTML5 sur-mesure (`templates/shop/product/_audio_player.html.twig`).
- [x] Surcharge de la page d'accueil vitrine (`templates/bundles/SyliusShopBundle/homepage/index.html.twig`) avec bannière héro, cartes de créations et lecteur audio embarqué.
- [x] Surcharge de la fiche création produit (`templates/bundles/SyliusShopBundle/product/show.html.twig`) avec galerie d'images et extrait sonore.
- [x] Surcharge du formulaire de contact artisan (`templates/bundles/SyliusShopBundle/contact/request.html.twig`) dans le thème Obsidienne & Or.

## 🔄 En Cours
- [ ] Création des pages éditoriales CMS (`sylius/cms-plugin`) pour l'histoire de l'artisanat du bambou et la charte éco-responsable.

## ⚠️ Points de Vigilance
- Exécuter la suite des tests et des commandes de validation via Docker Compose (`docker compose exec php ...`).

## 🏁 Prochaine Étape / Milestone
Mettre en place les blocs et pages CMS éditoriales via `sylius/cms-plugin`.
