# DEPLOYMENT.md - Déploiement & Git Branching Workflow

## 🌿 Protection de `main` & Workflow Git

- La branche `main` représente la version stable et déployable.
- Tout développement s'effectue sur des branches de fonctionnalités courtes (`feat/...`, `fix/...`, `docs/...`).
- Les intégrations sur `main` s'effectuent par Pull Requests validées avec description complète (Summary, Changes, Verification, Documentation, Risks).

---

## 🐳 Exécution & Déploiement Docker

### Développement Local
```bash
# Démarrage avec compose.override.yml automatique (Xdebug, MailHog, volumes dev)
docker compose up -d

# Migrations Doctrine
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction

# Vidage du cache Symfony
docker compose exec php bin/console cache:clear
```

### Production sur le VPS (51.178.50.217)
> 📋 **Document de référence complet** : voir [`docs/PRODUCTION_READINESS.md`](file:///docs/PRODUCTION_READINESS.md).

- **Reverse proxy** : Traefik v3.2.1 en **File Provider** (`/home/project/traefik/dynamic_conf/prod/zentoocraft.yml`).
- **Réseau partagé** : `traefik_app-network`.
- **Commande de démarrage** (exclusion absolue de `compose.override.yml`) :
```bash
docker compose -f compose.yml -f compose.prod.yml up -d --build
```
- **Persistance requise** : Volumes `mysql_prod_data` et `ztc_media_data` (`/srv/sylius/public/media`).

