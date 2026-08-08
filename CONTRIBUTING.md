# Guide de Contribution ZEN TOO Craft

Bienvenue sur le projet **ZEN TOO Craft** !

## 🌿 1. Protection de `main` & Stratégie de Branchement

La branche `main` est protégée. **Développez uniquement sur des branches dédiées** créées à partir d'un `main` à jour.

### Nommage des Branches :
- `feat/product-audio` : Nouvelle fonctionnalité
- `feat/cms-pages` : Nouvelles pages CMS
- `fix/audio-validation` : Correctif
- `docs/update-architecture` : Documentation

---

## 🔀 2. Workflow d'une Tâche & Pull Requests

Pour chaque tâche :
1. `git checkout main && git pull origin main`
2. `git checkout -b feat/nom-de-la-branche`
3. Implémenter par incréments atomiques au format **Conventional Commits** (`feat:`, `fix:`, `docs:`, `build:`, `refactor:`, `test:`).
4. Effectuer les contrôles Docker (`docker compose exec php bin/console lint:container`, `phpunit`).
5. Préparer une Pull Request avec la structure obligatoire (Summary, Changes, Verification, Documentation, Risks).
6. Merger sur `main` une fois toutes les vérifications au vert.
7. Revenir sur `main`, synchroniser, et supprimer la branche locale.

---

## 🛑 3. Contrôle Avant Commit & Merge
- Inspecter `git diff`.
- Garantir qu'aucun secret, fichier temporaire ou code cassé n'est inclus.
- Exécuter la suite de tests et de validation dans l'environnement Docker.
