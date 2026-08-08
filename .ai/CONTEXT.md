# CONTEXT.md - Mémoire Stable & Identité du Projet

> **Ce fichier définit ce QU'EST le projet ZEN TOO Craft. Il reste stable dans le temps.**

## 🎯 Vision du Produit
ZEN TOO Craft est la vitrine numérique officielle et le catalogue administrable de l'artisan créateur spécialisé dans le bambou et les matériaux naturels (instruments de musique à vent, flûtes artisanales, luminaires artistiques à motifs ajourés, objets de décoration).

Bâti sur le socle **Sylius / Symfony** avec un rendu frontend **Twig + Symfony UX** et le plugin CMS officiel Sylius (`sylius/cms-plugin`), le site combine la puissance d'un back-office e-commerce natif avec une expérience vitrine "Zen, Sombre Obsidienne & Or Végétal" haut de gamme, fluide, réactive et optimisée pour le SEO.

## 💡 Le Problème Résolu
- **Pour le public / visiteurs** : Découvrir l'univers artisanal ZEN TOO Craft, apprécier les détails du travail manuel, écouter les enregistrements sonores des instruments directement sur le site via un lecteur HTML5 sur-mesure et contacter l'artisan pour des acquisitions ou projets personnalisés.
- **Pour l'artisan** : Disposer d'un back-office robuste et éprouvé (Sylius Admin) pour administrer en toute autonomie ses créations, photos (`ProductImage`), fichiers audio (`ProductAudio`), catégories (Taxons) et contenus éditoriaux (pages Artisan, Savoir-Faire, Mentions légales), tout en bénéficiant d'une architecture nativement prête pour une ouverture e-commerce V2 (boutique en ligne).

## 👥 Personas & Utilisateurs Cibles
- **Amateurs de musique & musiciens** : Écoute d'extraits sonores d'instruments en bambou, fiches d'accordage et sonorités naturelles.
- **Amateurs d'artisanat & matière brute** : Découverte d'objets uniques faits main, du geste artisanal et des essences naturelles.
- **Amateurs de décoration & design** : Luminaires ajourés, jeux de lumière/ombre et objets de caractère contemporain.
- **Professionnels (boutiques, musiciens, événements, galeristes)** : Prise de contact simple et qualifiée.
- **L'Artisan Administrateur** : Autonomie totale via Sylius Admin sur les créations, visuels, audios, catégories (Taxons) et pages éditoriales (`sylius/cms-plugin`).

## 🏛️ Principes Fondateurs
- **Framework-First (Sylius / Symfony)** : Exploiter prioritairement les briques natives de Sylius (`Product`, `ProductImage`, `ProductVariant`, `Taxon`, `Attribute`, Admin Grids, Storefront Twig, `sylius/cms-plugin`) avant d'envisager tout développement sur-mesure.
- **Rendu Performant & Sémantique** : Server-Side Rendering (SSR) Twig + Symfony UX pour une réactivité optimale et un référencement naturel d'excellence.
- **Identité Visuelle "Zen & Sombre Gold"** : Esthétique sombre obsidienne, accents ambre/or végétal, bambou, transparence glassmorphism et sobriété contemporaine.
- **Évolutivité E-Commerce (V2 Ready)** : Préserver la compatibilité native Sylius e-commerce tout en restreignant le périmètre V1 au site vitrine et à la prise de contact.
- **Photographie & Audio de Qualité** : Visuels haute définition optimisés via `ProductImage` + `LiipImagineBundle` et lecteur audio HTML5 sur-mesure s'appuyant sur la ressource `ProductAudio`.
