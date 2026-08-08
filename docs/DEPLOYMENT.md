# DEPLOYMENT.md - Déploiement & Git Branching Workflow

## 🌿 Protection de `main` & Workflow Git

- La branche `main` représente la version stable et déployable.
- Tout développement s'effectue sur des branches de fonctionnalités courtes (`feat/...`, `fix/...`, `docs/...`).
- Les intégrations sur `main` s'effectuent par Pull Requests validées avec description complète (Summary, Changes, Verification, Documentation, Risks).

---

## 🐳 Déploiement & Execution Docker Compose

```bash
# Démarrage des conteneurs
docker compose up -d

# Migrations Doctrine
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction

# Vidage et réchauffement du cache Symfony
docker compose exec php bin/console cache:clear

# Installation des assets du thème Sylius
docker compose exec php bin/console sylius:install:assets
```
