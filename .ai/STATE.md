# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-09-22
**Branche Courante** : `main`
**Dernier commit** : `6e13c1c` — Merge PR #21 `feat/project-requests`

## 🚀 Phase Actuelle
Phase 6 : Pages Éditoriales CMS & Finition Vitrine (PROCHAINE ÉTAPE).

## 🎯 Focus Courant
Le socle technique est complet. L'artisan dispose d'un back-office adapté à son activité artisanale. La prochaine priorité est le contenu éditorial (pages CMS) et la structure catalogue (sous-taxons, visuels).

## ✅ Réalisé

### Socle & Architecture
- [x] Bootstrap Sylius 2.2 / Symfony 7.4 + plugin CMS (`sylius/cms-plugin` ^1.1)
- [x] Migration Docker PHP 8.4 (`ghcr.io/sylius/sylius-php:8.4-fixuid-xdebug-alpine`)
- [x] Design System CSS "Sombre Obsidienne & Or" (`assets/shop/styles/zen_too_craft.css`)
- [x] Taxons natifs initialisés via `ztc:catalog:init` (Instruments à Vent, Décoration, Luminaires)

### Vitrine Publique (Shop)
- [x] Page d'accueil immersive (hero, univers créateur, sélection créations, teaser savoir-faire)
- [x] Page catalogue produit (`product/index.html.twig`) — direction artistique Apple, cartes glassmorphism
- [x] **Lecteur audio interactif sur cartes** : clic sur overlay = play/pause piste primaire, barre de progression, exclusion mutuelle
- [x] Fiche produit détaillée + lecteur audio HTML5 sur-mesure (`_audio_player.html.twig`)
- [x] **Formulaire de demande de projet** sur fiche produit avec message de confirmation artisanal
- [x] Pied de page custom, navbar dynamique (catégories du canal)
- [x] Page CMS Sylius (template `shop/page/show.html.twig` surclassé avec le thème)
- [x] Page FAQ globale (`/faq`) avec système de questions/réponses par taxon
- [x] Page Blog (`/blog` + `/blog/{slug}`)
- [x] Page Contact (`contact/request.html.twig`)

### Back-office Admin
- [x] **Studio d'enregistrement audio** dans le formulaire produit (API MediaRecorder + upload fichier)
- [x] Entité `ProductAudio` avec champ `label` obligatoire, upload listener, suppression physique
- [x] Migration `Version20260918143705` — colonne `label VARCHAR(255)` sur `ztc_product_audio`
- [x] **Formulaire produit épuré** — onglets Taxes, Expédition, Inventaire, Attributs, Associations, Mollie, ESD masqués via `sylius_twig_hooks.yaml` + `ProductVariantCleanExtension`
- [x] Fiche technique produit générée par IA (Ollama / Qwen 2.5 local) — `ProductTechnicalSheetItem`
- [x] **Tableau de bord demandes de projet** remplaçant les statistiques de vente
- [x] CRUD admin `ProjectRequest` (liste, visualisation, statut)
- [x] Menu admin simplifié (lien "Demandes de Projets", "Catégories")
- [x] Formulaire CMS pages surclassé (WYSIWYG Quill.js, meta SEO)
- [x] FAQ admin par taxon (`FaqItem` + `FaqItemTranslation`)

### Infrastructure
- [x] Limite upload Nginx + PHP portée à 50Mo (`nginx-custom.conf`, `uploads.ini`, `compose.override.yml`)
- [x] Intégration Ollama (LLM local) pour la génération de contenus

## 🔄 À Faire

### Contenu & Catalogue
- [ ] Sous-taxons de démonstration (ex: Flûtes Shakuhachi, Flûtes Traversières) pour les puces de filtres
- [ ] Visuels associés aux taxons *Luminaires Artistiques* et *Objets Décoratifs*
- [ ] Rendre dynamique l'encart "Savoir-Faire & Philosophie" de la homepage (actuellement en dur)

### Pages CMS Éditoriales
- [ ] Page "Savoir-Faire & Matière Brute"
- [ ] Page "L'Artisan"

### Phase 7 – Recette & Production
- [ ] Optimisation images WebP/mobile (LiipImagineBundle)
- [ ] Recette mobile & tablette (ergonomie tactile, streaming audio iOS)
- [ ] Audit SEO (Schema.org/Product, OpenGraph, meta-tags)

## ⚠️ Points de Vigilance
- Purger le cache après toute modif config : `docker compose exec php bin/console cache:clear`
- Recompiler les assets si modif CSS : `docker compose run --rm nodejs yarn build`
- Les fichiers audio (`public/media/audio/`) ne sont pas versionnés — prévoir un volume persistant en production.

## 🏁 Prochaine Étape / Milestone
Créer les pages CMS éditoriales et les sous-taxons pour dynamiser la navigation catalogue.


