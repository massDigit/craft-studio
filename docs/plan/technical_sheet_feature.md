# Plan d'implémentation : Fiches Techniques Dynamiques & IA

## 1. Objectif

Remplacer les accordéons codés en dur sur la page produit ("Caractéristiques", "Entretien", "Livraison") par un système de collection dynamique, multilingue et administrable depuis le back-office Sylius. Le processus de saisie sera massivement assisté par l'IA.

## 2. Modélisation des Entités (Base de données)

- **`ProductTechnicalSheetItem`** : Entité principale liée au `Product` (ManyToOne). Contiendra un champ `position` (pour ordonner les accordéons).
- **`ProductTechnicalSheetItemTranslation`** : Entité de traduction (implements `TranslationInterface`). Contiendra :
    - `title` (string) : Le titre de l'accordéon (ex: "Entretien du bambou").
    - `description` (text) : Le contenu rédactionnel.
      _(Décision architecturale : Le champ 'slug' a été intentionnellement écarté pour alléger la BDD et simplifier l'interface. L'ancre HTML sera générée à la volée par Twig côté Storefront)._

## 3. Configuration Sylius (Backend)

- Déclaration de l'entité comme **Ressource Sylius** traduisible dans `sylius_resource.yaml`.
- Création des **FormTypes** Symfony (`ProductTechnicalSheetItemType` et `ProductTechnicalSheetItemTranslationType`).
- **Extension de formulaire** (`ProductTypeExtension`) pour greffer la nouvelle collection `technicalSheetItems` dans le formulaire produit natif de Sylius.

## 4. Interface Back-Office (Administration)

- Ajout d'un nouvel onglet **"Fiche technique"** dans l'édition du produit.
- Structure par onglets de **Localités** (FR, EN...) pour gérer facilement le multilinguisme.
- Implémentation du design validé via la maquette `docs/mockup/mockup_admin_fiche_technique.html`.

## 5. Brique Intelligence Artificielle (JS & API Locale)

Ajout de contrôleurs JavaScript (Stimulus) pour interagir avec `AiApiController` :

- **✨ Génération standard** : Lecture du nom/description du produit pour générer automatiquement 3 blocs pertinents au format JSON (Caractéristiques, Entretien, Livraison).
- **🌐 Traduction globale** : Lecture de tous les items de l'onglet source (ex: FR) et traduction automatique (sans hallucinations) pour remplir les autres onglets locaux (ex: EN).
- **✨ Amélioration textuelle** : Micro-bouton sous chaque description pour reformuler le texte sélectionné avec un ton "premium, artisanal et élégant", conforme à la charte Zen Too Craft.

## 6. Interface Front-Office (Storefront)

- Modification du fichier `templates/bundles/SyliusShopBundle/product/show.html.twig`.
- Remplacement des blocs statiques par une boucle dynamique : `{% for item in product.technicalSheetItems %}`.
- Génération de l'ID d'ancre HTML à la volée via le filtre Twig : `id="{{ item.translation.title|slug }}"`.
