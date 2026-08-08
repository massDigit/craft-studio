# DATABASE.md - Schéma de Base de Données Sylius, `ProductAudio` & CMS Plugin

## 🗄️ Entités Natives & Extensions

### 1. `sylius_product` & `sylius_product_image` (Natif Sylius)
- `sylius_product` : Identifiant, code, statut d'activation, traductions (nom, description, slug).
- `sylius_product_image` : Visuels de création rattachés via le modèle natif Sylius.

### 2. `ztc_product_audio` (Ressource Sylius `ProductAudio`)
- Relation `ManyToOne` avec `sylius_product` (cardinalité ouverte permettant plusieurs pistes sonores par création, avec flag `is_primary`).
- Champs :
  - `id` (INT AUTO_INCREMENT)
  - `product_id` (INT, FK vers `sylius_product.id`)
  - `path` (VARCHAR 255)
  - `original_name` (VARCHAR 255)
  - `mime_type` (VARCHAR 100)
  - `size` (INT)
  - `is_primary` (BOOLEAN DEFAULT TRUE)
  - `created_at` / `updated_at` (DATETIME)

### 3. Tables `sylius_cms_*` (Plugin CMS Officiel Sylius)
- Tables du plugin `sylius/cms-plugin` pour la gestion des pages et des blocs éditoriaux administrables dans Sylius Admin.
