# WORKFLOW.md - Procédures de Validation & Dev Loop (Git Workflow Structuré)

## 🐳 Environnement de Référence Docker Compose

L'ensemble des commandes d'ingénierie s'exécute **impérativement à l'intérieur des conteneurs Docker de référence** (`compose.yml` / `compose.override.dist.yml`).

### Services Docker Utilisés :
- **`php`** : Service applicatif Sylius / Symfony
- **`mysql`** : Service de base de données MySQL 8.4
- **`nginx`** : Service serveur Web (Port 80)
- **`nodejs`** : Service de compilation frontend et assets

---

## 🔁 Boucle de Développement & Validation (Dev Loop)

Chaque incrément suit la séquence :
1. Modifier un périmètre limité (incrément atomique) ;
2. Exécuter les contrôles ci-dessous dans le conteneur ;
3. Corriger les éventuelles erreurs ;
4. Mettre à jour la documentation si nécessaire ;
5. Créer un commit Conventional Commit atomique ;
6. Passer à l'étape suivante.

```bash
# 1. Démarrage de l'environnement Docker
docker compose up -d

# 2. Validation du conteneur & de la configuration Symfony
docker compose exec php bin/console about

# 3. Validation de l'injection de dépendances
docker compose exec php bin/console lint:container

# 4. Validation de la syntaxe des templates Twig
docker compose exec php bin/console lint:twig templates/

# 5. Exécution des tests unitaires et d'intégration
docker compose exec php bin/console phpunit

# 6. Compilation des assets frontend (si modifiés)
docker compose run --rm nodejs yarn build
```

---

## 📌 Politique de Commits Structurés (Conventional Commits)

Les messages de commit doivent strictement suivre la spécification Conventional Commits :
- `feat:` Nouvelle fonctionnalité
- `fix:` Correction de bug
- `refactor:` Refactoring de code sans changement fonctionnel
- `test:` Ajout ou modification de tests
- `docs:` Documentation projet ou technique
- `chore:` Maintenance, dépendances ou configuration
- `build:` Docker, Composer, dépendances, scripts de build
- `style:` Formattage visuel ou ajustements CSS

### Contrôle Avancé Avant Commit :
1. Inspecter le diff avec `git diff --staged` ou `git diff`.
2. Vérifier qu'aucun fichier temporaire, secret ou fichier non lié n'est embarqué.
3. Garantir que le projet reste compilable et exécutable à chaque commit.
