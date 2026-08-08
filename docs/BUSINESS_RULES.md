# BUSINESS_RULES.md - Règles Métier & Cartographie Sylius Native

## 🔗 Cartographie Métier <-> Framework Sylius

| Besoin Métier | Solution Framework-First | Détails Techniques |
| :--- | :--- | :--- |
| **Créations & Pièces** | `Sylius\Component\Core\Model\Product` | Produits Sylius activables/désactivables avec traductions sémantiques. |
| **Familles de Produit** | `Sylius\Component\Taxonomy\Model\Taxon` | Arborescence native Sylius (Instruments, Décoration, Luminaires). |
| **Visuels Haute Définition** | `Sylius\Component\Core\Model\ProductImage` + `LiipImagineBundle` | Stockage d'images produit natif + filtres de génération WebP automatiques. |
| **Extraits Audio des Instruments** | Ressource Sylius dédiée `ProductAudio` (Cardinalité ouverte) | Fichier son optionnel rattaché à une création avec upload, remplacement, suppression et validation MIME dans Sylius Admin. |
| **Pages Éditoriales & CMS** | Plugin officiel Sylius (`sylius/cms-plugin`) | Édition des pages "L'Artisan", "Savoir-Faire", "Mentions Légales" et des pavés de texte d'accueil depuis Sylius Admin. |
| **Lecteur Audio Vitrine** | HTML5 Audio + Symfony UX + CSS "Obsidienne & Or" | Intégration sur la fiche produit sans téléchargement imposé. |

---

## 📜 Règles de Validation du Média Audio
1. **Types MIME autorisés** : Strictly restricted to `audio/mpeg` (.mp3), `audio/wav` (.wav), `audio/ogg` (.ogg).
2. **Taille maximale** : 15 Mo par extrait audio.
3. **Suppression sécurisée** : Si l'artisan supprime l'audio dans le back-office, la ressource en BDD et le fichier physique sur le disque sont immédiatement effacés.
4. **Affichage Vitrine V1** : L'extrait audio principal (`is_primary = true`) est restitué sur la fiche création.
