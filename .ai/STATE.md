# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-08-08
**Branche Courante** : `main`

## 🚀 Phase Actuelle
Phase 4 : Theme Storefront "Sombre Obsidienne & Or Végétal" & UI Lecteur Audio (EN COURS).

## 🎯 Focus Courant
Création du thème Storefront Twig pour la vitrine publique ZEN TOO Craft et intégration du composant lecteur audio HTML5 sur-mesure.

## ✅ Réalisé
- [x] Initialisation du socle Sylius 2.2 / Symfony 7.4 et installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1).
- [x] Création de la ressource Sylius `App\Entity\Product\ProductAudio` rattachée au `Product` natif avec une cardinalité ouverte (`ManyToOne`).
- [x] Déclaration de la ressource `app.product_audio` dans `config/packages/sylius_resource.yaml`.
- [x] Création du formulaire `ProductAudioType` et extension du `ProductType` dans Sylius Admin.
- [x] Implémentation du listener d'événement `ProductAudioUploadListener` pour la gestion des téléversements et de la suppression physique des fichiers audio (`public/media/audio`).
- [x] Migration de l'environnement Docker vers PHP 8.4 (`ghcr.io/sylius/sylius-php:8.4-fixuid-xdebug-alpine`) et démarrage des conteneurs (`make up`).
- [x] Création de la base de données `sylius_dev`, chargement des tables et des fixtures par défaut Sylius (`sylius:fixtures:load`).
- [x] Implémentation de la commande `ztc:catalog:init` et création des Taxons natifs de référence (Instruments à vent, Décoration, Luminaires ajourés).
- [x] Validation empirique de la vitrine publique et de l'administration Sylius Admin sur `http://localhost/admin/login` (`HTTP 200 OK`).

## 🔄 En Cours
- [ ] Création du Thème Storefront Twig ZEN TOO Craft (Obsidienne & Or).
- [ ] Intégration du composant lecteur audio HTML5 sur la fiche produit vitrine.

## ⚠️ Points de Vigilance
- Exécuter la suite des tests et des commandes de validation via Docker Compose (`docker compose exec php ...`).

## 🏁 Prochaine Étape / Milestone
Implémenter le thème Storefront Twig ZEN TOO Craft et le lecteur audio HTML5 sur la fiche vitrine.



