# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-09-23
**Branche Courante** : `docs/pre-production-readiness-plan`

## 🚀 Phase Actuelle
Phase 7 : Enrichissement du Catalogue (Finalisé) & Phase 9 : Pré-production et Déploiement VPS (Cadrage documenté).

## 🎯 Focus Courant
Cadrage de pré-production, intégration du plan de préparation au déploiement VPS ([`docs/PRODUCTION_READINESS.md`](file:///docs/PRODUCTION_READINESS.md)) et suivi des dépendances e-mail (Zimbra Starter OVH ➔ Brevo).

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
- [x] Simplification & harmonisation du menu Admin : masquage des sous-sections superflues (Stock, Options, Attributs, Associations, Marketing) et remplacement universel du terme technique "Taxon" par "Catalogue" sur l'ensemble de l'interface d'administration (menu, listes, formulaires, filtres, messages).
- [x] Raccordement dynamique du menu principal du haut (Navbar) pour afficher les catégories de premier niveau rattachées au canal (`sylius.channel.menuTaxon.children`).
- [x] Désactivation du hook natif d'en-tête Sylius (`sylius_shop.base.header#content`) pour éviter le doublon d'en-tête.
- [x] Adoption de la direction artistique style Apple : ajout des classes `.ztc-apple-card` et `.ztc-apple-pill` dans `assets/shop/styles/zen_too_craft.css` et build Encore.
- [x] Refonte du template de listing de catégorie `templates/bundles/SyliusShopBundle/product/index.html.twig` (bannière dépolie avec image du taxon, puces de sous-catégories et grille d'exposition).
- [x] Génération de l'image d'art HD `taxon_instruments.jpeg` et association en base de données à la ressource `TaxonImage` de la catégorie Instruments à Vent (ID: 10).
- [x] Rendu dynamique et contextuel des filtres de sous-taxons (affiche uniquement les pastilles d'enfants ayant des produits rattachés via `ztc_get_subtaxons_with_products`).
- [x] Refonte ergonomique et responsive de la barre de navigation haute : libellés de taxons épurés via `ztc_short_taxon_name`, bouton « Sur-Mesure » permanent de la DA, icône burger SVG nette, et tiroir mobile dépoli épuré sans bouton redondant.
- [x] Branche `feat/shop-dynamic-taxon-filters` intégrée et Pull Request mergée dans `main` : [PR #22](https://github.com/massDigit/craft-studio/pull/22).
- [x] Internationalisation (FR / EN) complète de la grille Bento de la page d'accueil (univers, titres et encart sur-mesure).
- [x] Intégration et harmonisation du design de la page Contact (`/fr/contact/`) au thème Obsidienne & Or avec token CSRF, alertes flash et formulaire épuré centré.
- [x] Formalisation du document d'audit et de préparation à la mise en production dans [`docs/PRODUCTION_READINESS.md`](file:///docs/PRODUCTION_READINESS.md) et mise à jour de [`docs/DEPLOYMENT.md`](file:///docs/DEPLOYMENT.md).

## 🔄 En Cours / À Reprendre au Prochain Démarrage
- [ ] Suivre le provisionnement de l'offre Zimbra Starter chez OVH pour créer `contact@zentoo-craft.fr`.
- [ ] Créer le compte Brevo avec l'adresse professionnelle, authentifier le domaine (DKIM, DMARC, fusion SPF OVH/Brevo).
- [ ] Rédiger le fichier `compose.prod.yml` (exclusion stricte du dev, persistance `/srv/sylius/public/media` et volume DB).
- [ ] Préparer le fichier de routage dynamique Traefik File Provider (`zentoocraft.yml`).
- [ ] Poursuivre la rédaction finale des contenus éditoriaux CMS (*Histoire du Savoir-Faire*, *Charte Éco-Responsable*).

## ⚠️ Points de Vigilance
- **Cohabitation Traefik sur VPS** : Utiliser le File Provider (`zentoocraft.yml`) sans modifier `--providers.docker=false` pour ne pas perturber le projet Korning existant.
- **Délivrabilité e-mail (SPF)** : Ne jamais ajouter une deuxième entrée SPF dans OVH ; fusionner les directives dans l'enregistrement unique (`include:mx.ovh.com include:spf.brevo.com -all`).
- **Persistance des données** : Toujours préserver le volume des médias `/srv/sylius/public/media` et le volume SQL lors des montées de version.
- **Cache Symfony** : Toujours purger le cache Symfony (`docker compose exec php bin/console cache:clear`) après modification de templates ou de hooks Twig.

## 🏁 Prochaine Étape / Milestone
Création de la boîte mail professionnelle dès activation OVH Zimbra, configuration Brevo et rédaction de `compose.prod.yml`.
