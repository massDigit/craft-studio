# ARCHITECTURE.md - Architecture Globale Sylius / Symfony Dockerisée

## 🐳 Architecture Conteneurisée Docker Compose

```mermaid
graph TD
    Client[Navigateur / Visiteur Client] --> NginxService[Conteneur Nginx (Port 80)]
    Artisan[Artisan / Administrateur] --> NginxService

    subgraph "Docker Compose Infrastructure"
        NginxService --> PHPContainer[Conteneur PHP App (Sylius / Symfony 7.4)]
        PHPContainer --> MySQLContainer[(Conteneur MySQL 8.4)]
        PHPContainer --> MailhogService[Conteneur Mailhog (Port 8025)]
        NodeService[Conteneur Node.js (Build Frontend)] --> PHPContainer
    end

    subgraph "Sylius Core & Media Architecture"
        PHPContainer --> ProductEntity[Sylius Product Entity]
        PHPContainer --> ProductImage[ProductImage Native Sylius + LiipImagine]
        PHPContainer --> ProductAudio[ProductAudio Sylius Resource]
        PHPContainer --> CMSPlugin[sylius/cms-plugin (Pages & Blocks)]
    end
```

---

## 📸 1. Architecture des Images (Framework-First)
- Utilisation intégrale du modèle natif **`Sylius\Component\Core\Model\ProductImage`** pour l'association des visuels aux créations.
- **`LiipImagineBundle`** gère la génération des filtres de redimensionnement et le format WebP léger pour mobile sans dénaturer Sylius.

---

## 🎵 2. Architecture des Médias Audio (`ProductAudio`)
- **Modélisation** : Création d'une ressource Sylius dédiée `ProductAudio` (implémentant `ResourceInterface` de Sylius) en relation flexible `ManyToOne` avec `Product`.
- **Évolutivité** : Le modèle permet d'associer un ou plusieurs morceaux audio à une création. En V1, l'extrait audio principal (`is_primary = true`) est restitué sur la fiche vitrine.
- **Back-Office Sylius Admin** :
  - Upload, remplacement, suppression avec nettoyage du fichier physique.
  - Validation des types MIME (`audio/mpeg`, `audio/wav`, `audio/ogg`).

---

## 📝 3. Architecture des Contenus Éditoriaux (`sylius/cms-plugin`)
- Intégration du **plugin CMS officiel Sylius (`sylius/cms-plugin`)** pour administrer les pages *Artisan*, *Savoir-Faire*, *Mentions Légales*, *Politique de Confidentialité* et les pavés d'accueil.
