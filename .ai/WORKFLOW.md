# WORKFLOW.md - Procédures de Dev Loop, Branching & Pull Requests

## 🌿 1. Politique de Branchement (Branching Policy)

### Protection de `main`
La branche `main` est strictement **protégée**. Aucun code ne doit être écrit ou committé directement sur `main`.

### Workflow par Tâche :
1. Partir de `main` à jour (`git checkout main && git pull origin main`) ;
2. Créer une branche dédiée au périmètre cible (ex: `feat/product-audio`, `feat/cms-pages`, `fix/audio-validation`) ;
3. Travailler exclusivement sur cette branche ;
4. Exécuter les contrôles de la Dev Loop Docker ci-dessous ;
5. Effectuer des commits atomiques au format Conventional Commits ;
6. Pousser la branche et préparer/vérifier la Pull Request ;
7. Merger sur `main` une fois toutes les vérifications validées ;
8. Revenir sur `main`, tirer les derniers changements, supprimer la branche locale et mettre à jour `.ai/STATE.md` et `.ai/TASKS.md`.

---

## 🐳 2. Dev Loop Docker (Commandes de Validation)

```bash
# 1. Démarrage de l'infrastructure Docker
docker compose up -d

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

## 🔀 3. Format des Pull Requests & Description Obligatoire

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

## 🛑 4. Règle de Merge & Autonomie
- Avant merge, inspecter le diff complet pour garantir l'absence de secrets, de code mort ou de fichiers temporaires non souhaités.
- Privilégier un historique propre (squash merge ou rebase si nécessaire).
- Ne jamais forcer un push sur `main` (`git push --force`).
