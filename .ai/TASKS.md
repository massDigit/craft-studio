# TASKS.md - Roadmap & Carnet de Tâches ZEN TOO Craft

> Dernière mise à jour : 2026-09-22

## 🎯 Roadmap par Phases

### Phase 1 : Bootstrap Documentaire & Cadrage (TERMINÉ ✅)
- [x] Structuration de `.ai/` et `docs/` selon AGENTS.md
- [x] Enregistrement des décisions validées dans `.ai/DECISIONS.md`
- [x] Analyse de l'architecture Audio & CMS dans `.ai/ASSUMPTIONS.md`
- [x] Rédaction des specs dans `docs/` (`PROJECT.md`, `FEATURES.md`, `BUSINESS_RULES.md`, `STACK.md`, `ARCHITECTURE.md`, `DATABASE.md`, `API.md`, `SECURITY.md`, `TESTING.md`, `DEPLOYMENT.md`)

### Phase 2 : Validation Architecture Audio & CMS (TERMINÉ ✅)
- [x] Validation architecture `ProductAudio` (Sylius Resource dédiée)
- [x] Validation architecture CMS (`sylius/cms-plugin` ^1.1)

### Phase 3 : Socle Sylius / Symfony & Ressource `ProductAudio` (TERMINÉ ✅)
- [x] Bootstrap Sylius 2.2 / Symfony 7.4 + plugin CMS
- [x] Entité `ProductAudio` (`ManyToOne` vers `Product`), ressource Sylius, formulaire, listener d'upload
- [x] Migration Docker PHP 8.4
- [x] Taxons natifs via `ztc:catalog:init`
- [x] Validation Doctrine & injection de dépendances

### Phase 4 : Design System & Thème "Sombre Obsidienne & Or" (TERMINÉ ✅)
- [x] Design System CSS (`assets/shop/styles/zen_too_craft.css`)
- [x] Composant lecteur audio HTML5 sur-mesure (`templates/shop/product/_audio_player.html.twig`)

### Phase 5 : Vitrine Publique V1 (TERMINÉ ✅)
- [x] Page d'accueil immersive (hero, univers, créations, savoir-faire)
- [x] Page catalogue produits — direction artistique Apple, cartes glassmorphism, puces de filtres
- [x] Fiche produit détaillée + lecteur audio HTML5
- [x] Page Contact / Demandes sur-mesure
- [x] Navbar dynamique (catégories du canal), footer custom
- [x] Page CMS (`shop/page/show.html.twig`)
- [x] Page FAQ globale + FAQ par taxon (admin)
- [x] Page Blog

### Phase 5-bis : Back-office Admin Artisanal (TERMINÉ ✅)
- [x] Studio d'enregistrement audio dans le formulaire produit (API MediaRecorder + upload)
- [x] Champ `label` obligatoire sur piste audio + migration `Version20260918143705`
- [x] Formulaire produit épuré (onglets inutiles désactivés via hooks + `ProductVariantCleanExtension`)
- [x] Fiches techniques générées par IA (Ollama / Qwen 2.5 local)
- [x] WYSIWYG Quill.js sur descriptions produit et pages CMS
- [x] Tableau de bord demandes de projet (widget admin)
- [x] CRUD admin `ProjectRequest` + lien menu latéral
- [x] Formulaire demande de projet sur fiche produit vitrine + message de confirmation artisanal

### Phase 5-ter : Lecteur Audio Interactif Vitrine (TERMINÉ ✅)
- [x] Overlay de carte produit cliquable → play/pause piste principale
- [x] Barre de progression animée en temps réel
- [x] Exclusion mutuelle (une seule piste à la fois)
- [x] Affichage du `label` de la piste sur l'overlay

### Phase 6 : Pages Éditoriales CMS & Finition Catalogue (EN COURS 🔄)
- [ ] Page "Savoir-Faire & Matière Brute" (administrable via CMS)
- [ ] Page "L'Artisan" (administrable via CMS)
- [ ] Rendre dynamique l'encart "Savoir-Faire & Philosophie" de la homepage
- [ ] Sous-taxons de démonstration (Flûtes Shakuhachi, Flûtes Traversières…)
- [ ] Visuels associés aux taxons *Luminaires Artistiques* et *Objets Décoratifs*

### Phase 7 : Recette, Mobile Polish & SEO
- [ ] Optimisation images WebP/mobile (LiipImagineBundle)
- [ ] Recette mobile & tablette (ergonomie tactile, streaming audio iOS)
- [ ] Audit SEO sémantique (Schema.org/Product, OpenGraph, meta-tags)
- [ ] Volume persistant pour `public/media/audio/` en production
