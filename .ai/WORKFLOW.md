# WORKFLOW.md - Procédures de Dev Loop, Makefile & Docker

## 🐳 1. Environnement Docker Compose & Raccourcis Makefile

L'ensemble des opérations s'exécute à l'intérieur de l'infrastructure Docker de référence via `make` ou `docker compose`.

### Raccourcis Makefile Disponibles :
- **`make up`** : Démarrage des conteneurs en arrière-plan (`docker compose up -d`)
- **`make down`** : Arrêt des conteneurs (`docker compose down`)
- **`make install`** : Exécution de `sylius:install` dans le conteneur PHP
- **`make php-shell`** : Shell interactif dans le conteneur PHP (`docker compose exec php sh`)
- **`make node-shell`** : Shell interactif dans le conteneur Node.js
- **`make clean`** : Nettoyage complet des conteneurs et volumes Docker

---

## 🔁 2. Boucle de Développement & Validation (Dev Loop)

Chaque incrément suit la séquence :
1. Modifier un périmètre limité (incrément atomique) sur une branche dédiée (`feat/...`) ;
2. Exécuter les contrôles ci-dessous dans le conteneur (via `make` ou `docker compose exec`) ;
3. Corriger les éventuelles erreurs ;
4. Mettre à jour la documentation si nécessaire ;
5. Créer un commit Conventional Commit atomique (`feat:`, `fix:`, `docs:`, `build:`) ;
6. Préparer et valider la Pull Request.

```bash
# 1. Démarrage de l'environnement Docker
make up

# 2. Validation du conteneur & du Kernel Symfony
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

## 🌿 3. Politique de Branchement (Branching Policy)

### Protection de `main`
La branche `main` est strictement **protégée**. Aucun code ne doit être écrit ou committé directement sur `main`.

### Workflow par Tâche :
1. Partir de `main` à jour (`git checkout main && git pull origin main`) ;
2. Créer une branche dédiée au périmètre cible (ex: `feat/product-audio`, `feat/cms-pages`, `fix/audio-validation`) ;
3. Travailler exclusivement sur cette branche ;
4. Exécuter les contrôles de la Dev Loop Docker ci-dessus ;
5. Effectuer des commits atomiques au format Conventional Commits ;
6. Pousser la branche et préparer/vérifier la Pull Request ;
7. Merger sur `main` une fois toutes les vérifications validées ;
8. Revenir sur `main`, tirer les derniers changements, supprimer la branche locale et mettre à jour `.ai/STATE.md` et `.ai/TASKS.md`.

---

## 🔀 4. Format des Pull Requests & Description Obligatoire

### Format du Titre :
Le titre de la PR doit suivre Conventional Commits (ex: `feat: add ProductAudio management`).

### Modèle de Description PR :
```markdown
## Summary
Description concise des modifications.

## Changes
Principaux changements apportés par composant.

## Verification
Tests et commandes d'ingénierie exécutés dans Docker (`docker compose exec php ...`).

## Documentation
Fichiers `.ai/` et `docs/` mis à jour.

## Risks
Risques ou limitations identifiés (le cas échéant).
```

---

## 🤖 5. Procédure GitHub CLI (`gh`) & Validation CI Obligatoire

Pour automatiser la gestion des Pull Requests et garantir l'intégration continue :

1. **Authentification GitHub CLI (`gh`) avec Jetons du projet** :
   Les commandes `gh` doivent systématiquement consommer le jeton présent dans l'environnement de développement (`.env.dev` / `.env.local`) :
   ```bash
   # Création de la Pull Request avec la CLI GitHub
   GH_TOKEN=$GITHUB_TOKEN gh pr create --title "feat: my feature" --body-file description.md

   # Vérification du statut des checks automatisés (CI)
   GH_TOKEN=$GITHUB_TOKEN gh pr checks
   ```

2. **Séquence Obligatoire d'Intégration Distante** :
   - **Étape 1** : Pousser la branche de fonctionnalité (`git push -u origin feat/...`).
   - **Étape 2** : Créer la Pull Request via `gh` CLI avec `GH_TOKEN=$GITHUB_TOKEN`.
   - **Étape 3** : Attendre que le pipeline de la CI distante passe au **VERT** (`GH_TOKEN=$GITHUB_TOKEN gh pr checks`).
   - **Étape 4** : Ne merger la Pull Request qu'une fois la CI 100% verte.
   - **INTERDICTION STRICTE** de fusionner localement la branche sur `main` pour contourner la PR ou la CI.

