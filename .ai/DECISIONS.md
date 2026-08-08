# DECISIONS.md - Architecture Decision Records (ADR)

> **Registre chronologique des choix d'architecture et exigences utilisateurs EXPLICITEMENT VALIDÉES.**

---

### ADR-001 : Initialisation du socle IA-Native
- **Date** : 2026-08-08
- **Statut** : Accepté
- **Contexte** : Nécessité de structurer le projet pour une collaboration homme-IA optimale.
- **Décision** : Adoption de l'architecture standardisée avec routage master `AGENTS.md`, `.ai/` et `.agents/skills/`.

---

### ADR-002 : Socle Sylius / Symfony & Back-Office d'Administration Natif
- **Date** : 2026-08-08
- **Statut** : Accepté (Explicitement validé par le client)
- **Contexte** : Besoin d'un site vitrine haut de gamme administrable avec une capacité avérée d'évolution vers le e-commerce (V2).
- **Décisions** :
  1. **Framework Core** : Utilisation de **Sylius / Symfony** comme socle d'application et de gestion de contenu.
  2. **Back-Office d'Administration** : Le back-office natif de **Sylius** (Admin Grid, Forms, Taxons, Products) sert d'interface d'administration principale pour l'artisan.
  3. **Préparation E-Commerce V2** : Conservation du modèle de données natif Sylius (`Product`, `ProductVariant`, `Taxon`, `Channel`) pour permettre l'activation future des commandes, paniers, stocks et paiements sans refonte d'architecture.
  4. **Direction Artistique** : Identité visuelle orientée **Sombre Obsidienne**, **Or / Ambre Végétal**, **Bambou**, **Naturelle**, **Zen** et **Contemporaine**.

---

### ADR-003 : Frontend Vitrine V1 (Twig + Storefront Sylius + Symfony UX)
- **Date** : 2026-08-08
- **Statut** : Accepté (Validé par l'utilisateur)
- **Contexte** : Choix du moteur de rendu pour le site vitrine.
- **Décision** : Adoption du rendu côté serveur via **Twig / Storefront Sylius** complété par **Symfony UX** pour les interactions frontend. Pas de SPA React séparée en V1.

---

### ADR-004 : Lecteur Audio HTML5 Custom & Ressource `ProductAudio` Flexibly Linked
- **Date** : 2026-08-08
- **Statut** : Accepté (Validé par l'utilisateur)
- **Contexte** : Intégration de l'écoute des instruments à vent sur les fiches produit.
- **Décision** :
  - Lecteur basé sur l'API HTML5 `<audio>` avec une interface sur-mesure respectant la direction artistique ZEN TOO Craft. Écoute directe depuis la fiche création sans téléchargement.
  - Modélisation via une ressource Sylius dédiée `ProductAudio` rattachée au `Product` natif avec une cardinalité flexible (`ManyToOne` / relation ouverte) permettant de stocker plusieurs morceaux tout en affichant l'extrait audio principal en V1.

---

### ADR-005 : Module CMS Officiel Sylius (`sylius/cms-plugin`) pour les Contenus Éditoriaux
- **Date** : 2026-08-08
- **Statut** : Accepté (Correction utilisateur appliquée)
- **Contexte** : Administration des pages institutionnelles (Artisan, Savoir-Faire, Mentions Légales, Politique de Confidentialité, Blocs d'Accueil).
- **Décision** : Utilisation du plugin CMS officiel maintenu par Sylius (`sylius/cms-plugin` compatible Sylius 2.x). Respect du principe Framework-First en exploitant les Pages, Blocks et Media natifs du plugin officiel.

---

### ADR-006 : Périmètre Fonctionnel V1 (Vitrine Administrable Stricte) & Visuels Natifs
- **Date** : 2026-08-08
- **Statut** : Accepté (Validé par l'utilisateur)
- **Contexte** : Délimitation du périmètre V1 et gestion des visuels.
- **Décisions** :
  - **Images** : Utilisation intégrale du modèle natif Sylius `ProductImage` pour les visuels des créations. `LiipImagineBundle` est utilisé pour l'optimisation et la génération des filtres/WebP sans dénaturer Sylius.
  - **Périmètre V1** : Vitrine administrable + lecteur audio + formulaire de contact. Pas de panier/checkout/paiement en V1.

---

### ADR-007 : Environnement de Développement Docker de Référence
- **Date** : 2026-08-08
- **Statut** : Accepté (Validé par l'utilisateur)
- **Contexte** : Isolation des dépendances et reproductibilité de l'environnement de développement.
- **Décision** : Adoption de l'environnement officiel **Docker & Docker Compose** de Sylius comme environnement de référence absolu. L'application ne dépend pas d'installations locales de PHP, MySQL ou Node.
- **Services Docker Officiels** :
  - `php` : Conteneur applicatif Sylius / Symfony (`ghcr.io/sylius/sylius-php:8.3-alpine` / `fixuid-xdebug`).
  - `mysql` : Base de données relationnelle (`mysql:8.4`).
  - `nginx` : Serveur web (`ghcr.io/sylius/sylius-nginx:latest`).
  - `nodejs` : Build et compilation frontend (`node:22-alpine`).
  - `mailhog` : Capture d'emails en développement.
- **Toutes les commandes d'ingénierie (console, composer, tests) doivent impérativement s'exécuter via `docker compose exec php <commande>`**.
