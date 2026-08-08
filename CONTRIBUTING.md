# Guide de Contribution ZEN TOO Craft

Bienvenue sur le projet **ZEN TOO Craft** !

## 📜 Règles du Git Workflow & Commits Structurés

### 1. Commits Atomiques & Conventional Commits
Chaque commit doit représenter un incrément petit, cohérent et testable.

Les messages doivent suivre la norme Conventional Commits :
- `feat:` Nouvelle fonctionnalité
- `fix:` Correction de bug
- `refactor:` Amélioration de la structure du code
- `test:` Ajout ou modification de tests
- `docs:` Documentation
- `chore:` Configuration et maintenance
- `build:` Docker, Composer, dépendances

**Exemples** :
- `build: initialize dockerized Sylius environment`
- `chore: configure Sylius CMS plugin`
- `feat: add ProductAudio domain model`

### 2. Contrôle Avant Commit
Avant tout commit :
1. Inspecter les fichiers modifiés (`git status` & `git diff`).
2. Valider le conteneur (`docker compose exec php bin/console lint:container`).
3. Ne jamais committer de clés secrètes, d'identifiants sensibles ou de code défectueux.
