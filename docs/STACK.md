# STACK.md - Stack Technique & Écosystème Sylius / Symfony Dockerisé

## 🐳 Environnement de Développement Docker de Référence

L'application **ZEN TOO Craft** est intégralement dockerisée et ne dépend d'aucune installation locale de PHP, MySQL ou Node.

| Service Docker | Image / Configuration | Rôle dans l'Architecture |
| :--- | :--- | :--- |
| **`php`** | `ghcr.io/sylius/sylius-php:8.3-alpine` | Conteneur applicatif principal (PHP 8.3/8.5, OPcache, extensions Sylius) |
| **`mysql`** | `mysql:8.4` (Healthcheck & Port 3306) | Base de données relationnelle du catalogue et des contenus |
| **`nginx`** | `ghcr.io/sylius/sylius-nginx:latest` (Port 80) | Serveur Web haute performance exposant l'application |
| **`nodejs`** | `node:22-alpine` | Service de compilation et d'installation des dépendances frontend |
| **`mailhog`** | `mailhog/mailhog:latest` (Port 8025) | Capture et inspection des e-mails en environnement de développement |

---

## 🛠️ Composants Applicatifs Sylius / Symfony

| Composant | Technologie / Bundle | Rôle |
| :--- | :--- | :--- |
| **Framework Core** | **Symfony 7.4 / Sylius 2.2** | Socle PHP, routing, sécurité, injection de dépendances |
| **CMS Éditorial** | **`sylius/cms-plugin`** | Plugin CMS officiel Sylius (Pages Artisan, Savoir-faire, Légales) |
| **Moteur de Rendu** | **Twig + Storefront Sylius + Symfony UX** | Server-Side Rendering (SSR) et composants interactifs légers |
| **Gestionnaire d'Images** | **`ProductImage` + LiipImagineBundle** | Images natives Sylius et filtres WebP pour mobile |
| **Gestionnaire Audio** | **Sylius Resource `ProductAudio`** | Ressource audio sur-mesure rattachée aux créations |

---

## 🎨 Charte Visuelle & Design System
- **Fond principal** : Noir Obsidienne (`#0D0F0E`)
- **Conteneurs** : Vert Sombre Organique en verre dépoli (`rgba(19, 24, 21, 0.85)` + `backdrop-filter: blur(12px)`)
- **Accents** : Or Végétal / Ambre de Bambou (`#D4AF37`, `#E5C158`)
- **Textes** : Crème Doux / Lin (`#F4EFE6`)
- **Typographies** : À valider lors de la création du Design System d'après le logo et les photos.
