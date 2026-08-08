# DEPLOYMENT.md - Procédures de Déploiement & Git Workflow

## 🐳 Environnement Docker Compose Reference

L'environnement de développement repose intégralement sur Docker Compose.

```bash
# Démarrage des conteneurs
docker compose up -d

# Commandes d'exploitation dans le conteneur php
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php bin/console cache:clear
docker compose exec php bin/console sylius:install:assets
```

---

## 📌 Politique de Contribution & Git Workflow
- Les modifications de code sont livrées sous forme d'incréments atomiques.
- Chaque commit doit utiliser un préfixe **Conventional Commits** (`feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `chore:`, `build:`).
- Aucun secret, fichier temporaire ou code cassé ne doit être committé sur la branche principale.
