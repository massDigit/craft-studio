# TASKS.md - Roadmap & Carnet de Tâches ZEN TOO Craft

## 🎯 Roadmap par Phases

### Phase 1 : Bootstrap Documentaire & Cadrage (TERMINÉ ✅)
- [x] Structuration de `.ai/` et `docs/` selon AGENTS.md
- [x] Enregistrement des décisions validées (Twig/Symfony UX, Lecteur Audio HTML5, V1 Vitrine) dans `.ai/DECISIONS.md`
- [x] Analyse spécifique de l'architecture des Médias Audio & Contenus Éditoriaux dans `.ai/ASSUMPTIONS.md`
- [x] Rédaction des spécifications d'ingénierie dans `docs/` (`PROJECT.md`, `FEATURES.md`, `BUSINESS_RULES.md`, `STACK.md`, `ARCHITECTURE.md`, `DATABASE.md`, `API.md`, `SECURITY.md`, `TESTING.md`, `DEPLOYMENT.md`)

### Phase 2 : Validation des Recommandations Architecture Audio & CMS (EN COURS 🔄)
- [ ] Validation de l'architecture `ProductAudio` (Sylius Resource dédiée)
- [ ] Validation de l'architecture CMS (`BitBagSyliusCMSPlugin` ou entité `Page` Sylius Resource)
- [ ] Confirmation finale avant initialisation de l'application Sylius/Symfony

### Phase 3 : Initialisation du Socle Sylius / Symfony & Ressource `ProductAudio`
- [x] Bootstrap de l'application Sylius 2.2 / Symfony 7.4
- [x] Installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1)
- [x] Implémentation du modèle `ProductAudio` (`App\Entity\Product\ProductAudio`) avec cardinalité ouverte (`ManyToOne` vers `Product`)
- [x] Enregistrement de la ressource Sylius `app.product_audio` dans `sylius_resource.yaml`
- [x] Validation empirique des mappings Doctrine (`doctrine:schema:validate`)
- [x] Validation empirique de l'injection de dépendances (`lint:container`)
- [x] Formulaire d'upload `ProductAudioType` et listener d'événements `ProductAudioUploadListener` (stockage & nettoyage `public/media/audio`)
- [ ] Configuration des Taxons natifs (Familles : Instruments, Décoration, Luminaires)
- [ ] Configuration des Attributs Produit natifs (Essence de bambou, Dimensions, Accordage Hz)



### Phase 4 : Design System & Theme "Sombre Obsidienne & Or"
- [ ] Création du Thème Sylius Storefront (Twig)
- [ ] Intégration du CSS Design System (obsidienne `#0D0F0E`, or végétal `#D4AF37`, glassmorphism, choix typographique)
- [ ] Integration de Symfony UX pour les composants réactifs (modales photos, filtres interactifs, lecteur audio)
- [ ] Layout public : Header bambou, Navigation, Footer, Breakpoints mobiles

### Phase 5 : Vitrine Publique V1 (Pages & Composants)
- [ ] Page d'Accueil : Banner Hero immersif, univers du créateur, sélection de créations, teaser savoir-faire, blocs CMS
- [ ] Catalogue Produit & Filtres par Taxons (Instruments, Décoration, Luminaires)
- [ ] Fiche Création Détaillée & Lecteur Audio HTML5 Sur-Mesure
- [ ] Page "Savoir-Faire & Matière Brute" (administrable via le module CMS)
- [ ] Page "L'Artisan" (administrable via le module CMS)
- [ ] Galerie Visuelle Immensive (créations, coulisses, atelier)
- [ ] Formulaire de Contact & Demandes Sur-Mesure avec pré-remplissage du produit

### Phase 6 : Administration Sylius (Admin Grids & Forms)
- [ ] Personnalisation du formulaire Produit dans Sylius Admin (Upload visuels `ProductImage` + Upload audio `ProductAudio` + Attributs)
- [ ] Interface d'édition des pages CMS (Artisan, Savoir-faire, Mentions Légales)
- [ ] Gestion des messages de contact

### Phase 7 : Recette, Mobile Polish & SEO
- [ ] Optimisation des filtres LiipImagineBundle pour le rendu WebP mobile
- [ ] Recette mobile & tablette (ergonomie tactile et streaming audio HTML5)
- [ ] Audit SEO sémantique (Schema.org/Product, OpenGraph, meta-tags)
