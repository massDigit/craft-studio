# FEATURES.md - Matrice des Fonctionnalités V1 vs V2 (Sylius / Symfony)

## 📌 Matrice Comparative

| Fonctionnalité | Rôle & Description | Inclus dans V1 | Évolutivité V2 (E-Commerce) |
| :--- | :--- | :--- | :--- |
| **Page d'Accueil Immensive** | Banner Hero, univers ZEN TOO Craft, créations en vedette, teaser savoir-faire | ✅ Oui | Identique |
| **Catalogue par Taxons** | Tri et filtrage par familles (Instruments, Décoration, Luminaires) via Sylius Taxons | ✅ Oui | Identique avec filtres facettés |
| **Fiche Création Flexible** | Photos HD, description, dimensions, matériaux, accordage, statut de pièce | ✅ Oui | Ajout du bouton "Ajouter au Panier" |
| **Lecteur Audio Intégré** | Écoute en streaming direct sur les fiches d'instruments à vent | ✅ Oui | Identique |
| **Section Savoir-Faire** | Étapes de travail du bambou, sélection des tiges, luminaires ajourés | ✅ Oui | Identique |
| **Page "L'Artisan"** | Récit du créateur, démarche philosophique et univers musical | ✅ Oui | Identique |
| **Galerie Visuelle** | Grille d'images grand format filtrable (pièces, atelier, matières) | ✅ Oui | Identique |
| **Formulaire de Contact** | Demande d'information, pièce sur-mesure ou contact pro avec pré-remplissage du produit | ✅ Oui | Prise de contact pour créations sur-mesure |
| **Sylius Admin Back-Office** | Administration CRUD des créations, photos, audios, catégories (Taxons) et pages éditoriales | ✅ Oui | Gestion étendue des prix, stocks, commandes, clients |
| **Briques Légales & SEO** | Pages légales administrables, balisage OpenGraph & Schema.org/Product | ✅ Oui | Identique + CGV e-commerce |
| **Gestion du Panier** | Ajout d'articles au panier et tunnel d'achat | ❌ Non (V2) | ✅ Activation du panier Sylius natif |
| **Tunnel de Commande & Paiement** | Intégration Stripe/PayPal, gestion des frais de port et adresses | ❌ Non (V2) | ✅ Activation du checkout Sylius natif |
| **Comptes Clients & Historique** | Espace client pour suivi de commande et factures | ❌ Non (V2) | ✅ Activation du Customer Management Sylius |

---

## 🎯 Focus V1 : Expérience Vitrine & Lecteur Audio
- **Visualisation sans friction** : L'utilisateur accède immédiatement à la beauté des pièces et à l'écoute des sons d'instruments.
- **Formulaire de Contact "Sur-Mesure"** : Chaque création comporte un bouton "Demander cette création" qui injecte le nom et le taxon du produit dans la demande transmise à l'artisan.
