# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-08-08
**Branche Courante** : `main`

## 🚀 Phase Actuelle
Phase 3 : Configuration des Taxons & Fixtures Métier (EN COURS).

## 🎯 Focus Courant
Création des Taxons natifs de référence (Instruments à vent, Décoration, Luminaires ajourés) et des Fixtures de démonstration.

## ✅ Réalisé
- [x] Initialisation du socle Sylius 2.2 / Symfony 7.4 et installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1).
- [x] Création de la ressource Sylius `App\Entity\Product\ProductAudio` rattachée au `Product` natif avec une cardinalité ouverte (`ManyToOne`).
- [x] Déclaration de la ressource `app.product_audio` dans `config/packages/sylius_resource.yaml`.
- [x] Création du formulaire `ProductAudioType` et extension du `ProductType` dans Sylius Admin.
- [x] Implémentation du listener d'événement `ProductAudioUploadListener` pour la gestion des téléversements et de la suppression physique des fichiers audio (`public/media/audio`).
- [x] Validation empirique des mappings Doctrine (`[OK] The mapping files are correct.`).
- [x] Validation empirique du conteneur d'injection de dépendances (`[OK] The container was linted successfully.`).
- [x] Validation empirique des métadonnées de ressources Sylius (`sylius:debug:resource app.product_audio`).

## 🔄 En Cours
- [ ] Configuration des Taxons natifs Sylius pour l'arborescence du catalogue.
- [ ] Intégration du Design System CSS "Sombre Obsidienne & Or Végétal" sur le Storefront.

## ⚠️ Points de Vigilance
- Exécuter la suite des tests et des commandes de validation via Docker Compose (`docker compose exec php ...`).

## 🏁 Prochaine Étape / Milestone
Créer l'arborescence des Taxons natifs Sylius et développer le composant du lecteur audio HTML5 sur la fiche vitrine.


