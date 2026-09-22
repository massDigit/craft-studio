# TASKS.md - Roadmap & Carnet de Tâches ZEN TOO Craft

## 🎯 Roadmap par Phases

### Phase 1 : Bootstrap Documentaire & Cadrage (TERMINÉ ✅)
- [x] Structuration de `.ai/` et `docs/` selon AGENTS.md
- [x] Enregistrement des décisions validées (Twig/Symfony UX, Lecteur Audio HTML5, V1 Vitrine) dans `.ai/DECISIONS.md`
- [x] Analyse spécifique de l'architecture des Médias Audio & Contenus Éditoriaux dans `.ai/ASSUMPTIONS.md`
- [x] Rédaction des spécifications d'ingénierie dans `docs/` (`PROJECT.md`, `FEATURES.md`, `BUSINESS_RULES.md`, `STACK.md`, `ARCHITECTURE.md`, `DATABASE.md`, `API.md`, `SECURITY.md`, `TESTING.md`, `DEPLOYMENT.md`)

### Phase 2 : Validation des Recommandations Architecture Audio & CMS (TERMINÉ ✅)
- [x] Validation de l'architecture `ProductAudio` (Sylius Resource dédiée)
- [x] Validation de l'architecture CMS (`sylius/cms-plugin` ^1.1)

### Phase 3 : Initialisation du Socle Sylius / Symfony & Ressource `ProductAudio` (TERMINÉ ✅)
- [x] Bootstrap de l'application Sylius 2.2 / Symfony 7.4
- [x] Installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1)
- [x] Implémentation du modèle `ProductAudio` (`App\Entity\Product\ProductAudio`) avec cardinalité ouverte (`ManyToOne` vers `Product`)
- [x] Enregistrement de la ressource Sylius `app.product_audio` dans `sylius_resource.yaml`
- [x] Validation empirique des mappings Doctrine (`doctrine:schema:validate`)
- [x] Validation empirique de l'injection de dépendances (`lint:container`)
- [x] Formulaire d'upload `ProductAudioType` et listener d'événements `ProductAudioUploadListener` (stockage & nettoyage `public/media/audio`)
- [x] Configuration des Taxons natifs de référence via `ztc:catalog:init` (Instruments à vent, Décoration, Luminaires ajourés)

### Phase 4 : Design System & Theme "Sombre Obsidienne & Or" (TERMINÉ ✅)
- [x] Création du Thème Sylius Storefront (Twig) & Design System CSS (`assets/shop/styles/zen_too_craft.css`)
- [x] Intégration du CSS Design System (obsidienne `#0D0F0E`, or végétal `#D4AF37`, typographies Google Fonts *Outfit* + *Inter*)
- [x] Création du composant Twig du lecteur audio HTML5 sur-mesure (`templates/shop/product/_audio_player.html.twig`)

### Phase 5 : Vitrine Publique V1 (Pages & Composants) (TERMINÉ ✅)
- [x] Page d'Accueil : Banner Hero immersif, univers du créateur, sélection de créations, teaser savoir-faire (`templates/bundles/SyliusShopBundle/homepage/index.html.twig`)
- [x] Fiche Création Détaillée & Lecteur Audio HTML5 Sur-Mesure (`templates/bundles/SyliusShopBundle/product/show.html.twig`)
- [x] Formulaire de Contact & Demandes Sur-Mesure (`templates/bundles/SyliusShopBundle/contact/request.html.twig`)

### Phase 6 : Back-Office CMS, Blog & Direction Artistique (TERMINÉ ✅)
- [x] Intégration du Journal de l'Artisan (`BlogPost` / `BlogPostTranslation`) directement dans le menu Admin **« Gestion de contenu »** aux côtés des pages.
- [x] Gestion dynamique des thématiques (*Topics*) sur les articles de blog dans Sylius Admin.
- [x] Nettoyage et assainissement du CMS : suppression de la collection obsolète et retrait des sous-sections inutilisées (*Médias*, *Blocs*, *Modèles de page*).
- [x] Intégration de l'éditeur WYSIWYG (Quill.js) et du service local d'IA Ollama (Qwen 2.5) pour la rédaction et traduction FR/EN.
- [x] Nettoyage strict des langues système en base et en cache pour restreindre l'application uniquement au Français (`fr`) et à l'Anglais (`en`).
- [x] Refonte des pages catégories / taxons : bannière d'univers en verre dépoli, cartes glassmorphism (`.ztc-apple-card`) et pastilles Apple (`.ztc-apple-pill`).
- [x] Filtres de sous-taxons dynamiques et contextuels (`ztc_get_subtaxons_with_products`, conditionnés à la présence de produits rattachés).
- [x] Optimisation de la barre de navigation haute : libellés concis via `ztc_short_taxon_name`, bouton permanent doré « Sur-Mesure » (`.ztc-btn-gold`) et rupture responsive à 1200px (`xl`).
- [x] Menu mobile épuré : icône burger SVG vectorielle, tiroir flottant en verre dépoli sans impact sur la géométrie de la barre, liste sobre sans doublon.
- [x] Internationalisation complète FR/EN de la grille Bento de la page d'accueil (univers et encart sur-mesure).

### Phase 7 : Enrichissement du Catalogue & Pages Éditoriales
- [ ] Associer des visuels d'art dédiés aux taxons restants (*Luminaires*, *Décoration*).
- [ ] Structuration de sous-taxons de démonstration avec produits associés (ex: *Flûtes Shakuhachi*, *Flûtes Traversières*).
- [ ] Rédaction et traduction des pages éditoriales CMS (*Histoire du Savoir-Faire*, *Charte Éco-Responsable*).

### Phase 8 : Recette Finale, Performance & SEO
- [ ] Optimisation des filtres LiipImagineBundle pour le rendu WebP mobile
- [ ] Recette mobile & tablette complète (streaming audio HTML5 et ergonomie tactile)
- [ ] Audit SEO sémantique (Schema.org/Product, OpenGraph, meta-tags)
