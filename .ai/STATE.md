# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-09-22
**Branche Courante** : `feat/shop-dynamic-taxon-filters`

## 🚀 Phase Actuelle
Phase 7 : Enrichissement du Catalogue & Pages Éditoriales (EN COURS).

## 🎯 Focus Courant
Enrichissement des visuels de taxons restants et structuration des sous-taxons avec produits rattachés.

## ✅ Réalisé
- [x] Initialisation du socle Sylius 2.2 / Symfony 7.4 et installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1).
- [x] Création de la ressource Sylius `App\Entity\Product\ProductAudio` rattachée au `Product` natif avec une cardinalité ouverte (`ManyToOne`).
- [x] Déclaration de la ressource `app.product_audio` dans `config/packages/sylius_resource.yaml`.
- [x] Création du formulaire `ProductAudioType` et extension du `ProductType` dans Sylius Admin.
- [x] Implémentation du listener d'événement `ProductAudioUploadListener` pour la gestion des téléversements et de la suppression physique des fichiers audio (`public/media/audio`).
- [x] Migration de l'environnement Docker vers PHP 8.4 (`ghcr.io/sylius/sylius-php:8.4-fixuid-xdebug-alpine`) et démarrage des conteneurs (`make up`).
- [x] Intégration du formulaire WYSIWYG (Quill.js) sur les champs de description de produit et les pages CMS.
- [x] Implémentation du service IA Ollama (Qwen 2.5 local) avec les boutons `Générer la description` (FR) et `Générer la traduction` (EN) dans Sylius Admin.
- [x] Nettoyage strict des langues système en base et en cache pour restreindre l'application uniquement au Français (`fr`) et à l'Anglais (`en`).
- [x] Simplification du menu Admin : sous-section du catalogue renommée en "Catégories".
- [x] Raccordement dynamique du menu principal du haut (Navbar) pour afficher les catégories de premier niveau rattachées au canal (`sylius.channel.menuTaxon.children`).
- [x] Désactivation du hook natif d'en-tête Sylius (`sylius_shop.base.header#content`) pour éviter le doublon d'en-tête.
- [x] Adoption de la direction artistique style Apple : ajout des classes `.ztc-apple-card` et `.ztc-apple-pill` dans `assets/shop/styles/zen_too_craft.css` et build Encore.
- [x] Refonte du template de listing de catégorie `templates/bundles/SyliusShopBundle/product/index.html.twig` (bannière dépolie avec image du taxon, puces de sous-catégories et grille d'exposition).
- [x] Génération de l'image d'art HD `taxon_instruments.jpeg` et association en base de données à la ressource `TaxonImage` de la catégorie Instruments à Vent (ID: 10).
- [x] Rendu dynamique et contextuel des filtres de sous-taxons (affiche uniquement les pastilles d'enfants ayant des produits rattachés via `ztc_get_subtaxons_with_products`).
- [x] Refonte ergonomique et responsive de la barre de navigation haute : libellés de taxons épurés via `ztc_short_taxon_name`, bouton « Sur-Mesure » permanent de la DA, icône burger SVG nette, et tiroir mobile dépoli épuré sans bouton redondant.
- [x] Branche `feat/shop-dynamic-taxon-filters` poussée et Pull Request ouverte : [PR #22](https://github.com/massDigit/craft-studio/pull/22).
- [x] Internationalisation (FR / EN) complète de la grille Bento de la page d'accueil (univers, titres et encart sur-mesure).

## 🔄 En Cours / À Reprendre au Prochain Démarrage
- [ ] Associer des visuels dédiés aux autres taxons principaux (Luminaires Artistiques, Objets Décoratifs) dans la base de données.
- [ ] Créer des sous-taxons de démonstration (ex: Flûtes Shakuhachi, Flûtes Traversières sous Instruments à Vent) pour alimenter dynamiquement les puces de filtres Apple.
- [ ] Poursuivre la rédaction et la traduction des pages éditoriales CMS (*Histoire du Savoir-Faire*, *Charte Éco-Responsable*).

## ⚠️ Points de Vigilance
- Toujours purger le cache Symfony (`docker compose exec php bin/console cache:clear`) après toute modification de configuration Twig ou de hooks.
- Recompiler les assets avec `npm run build` si de nouvelles classes CSS sont ajoutées à `assets/shop/styles/zen_too_craft.css`.

## 🏁 Prochaine Étape / Milestone
Finaliser la structure hiérarchique des sous-catégories et associer les visuels de taxons restants.
