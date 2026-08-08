# API.md - API Contracts & Endpoints Sylius / Symfony

## 📡 Endpoints Sylius Native REST / API Platform

En cas d'utilisation d'un composant frontend dynamique ou d'intégration d'API, Sylius fournit nativement :

### 1. Produits & Créations (`/api/v2/shop/products`)
- `GET /api/v2/shop/products` : Liste des créations actives avec pagination, tri et filtrage par taxon.
- `GET /api/v2/shop/products/{code}` : Détail d'une création (images, attributs, extrait audio).

### 2. Taxons & Catégories (`/api/v2/shop/taxons`)
- `GET /api/v2/shop/taxons` : Arborescence des familles (Instruments, Décoration, Luminaires).

### 3. Contact & Demandes Sur-Mesure (`/api/v1/contact` / Endpoint Custom)
- `POST /api/v1/contact` : Transmission d'une demande de contact qualifiée pour une création ou un projet sur-mesure.
