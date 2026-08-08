# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-08-08

## 🚀 Phase Actuelle
Phase 3 : Implémentation du Modèle `ProductAudio` & Configuration Médias / CMS (EN COURS).

## 🎯 Focus Courant
Création du formulaire d'upload `ProductAudioType` dans Sylius Admin et intégration de l'écoute des instruments.

## ✅ Réalisé
- [x] Initialisation du socle Sylius 2.2 / Symfony 7.4 et installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1).
- [x] Création de la ressource Sylius `App\Entity\Product\ProductAudio` rattachée au `Product` natif avec une cardinalité ouverte (`ManyToOne`).
- [x] Déclaration de la ressource `app.product_audio` dans `config/packages/sylius_resource.yaml`.
- [x] Validation empirique des mappings Doctrine (`[OK] The mapping files are correct.`).
- [x] Validation empirique du conteneur d'injection de dépendances (`[OK] The container was linted successfully.`).
- [x] Validation empirique des métadonnées de ressources Sylius (`sylius:debug:resource app.product_audio`).

## 🔄 En Cours
- [ ] Création du formulaire `ProductAudioType` et extension de la saisie produit Sylius Admin.
- [ ] Intégration du Design System CSS "Sombre Obsidienne & Or Végétal" sur le Storefront.

## ⚠️ Points de Vigilance
- Exécuter la suite des tests et des commandes de validation via Docker Compose (`docker compose exec php ...`).

## 🏁 Prochaine Étape / Milestone
Créer le formulaire de gestion audio back-office et intégrer les visuels/audios de démonstration pour les instruments à vent.

