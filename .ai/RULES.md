# RULES.md - Règles d'Ingénierie Stricte ZEN TOO Craft

> **Ces règles sont NON NÉGOCIABLES pour tout assistant IA ou développeur travaillant sur la codebase.**

1. **Aucun patch superficiel** : Toujours corriger la cause racine au lieu de contourner les exceptions.
2. **Découplage & Modularité** : Séparer strictement la logique métier des couches d'affichage et d'infrastructure.
3. **Contrat d'API strict** : Ne jamais altérer une signature de fonction ou de type sans mettre à jour tous ses appels.
4. **Ne jamais deviner les schemas** : Toujours lire la définition exacte des entités dans `docs/DATABASE.md` ou le code source avant consommation.
5. **Gestion propre du state** : Ne pas muter directement l'état global avec des objets partiels ou incomplets.
6. **Classification des décisions & non-usurpation d'ADR** : Ne jamais inscrire une proposition technique dans `.ai/DECISIONS.md` sans validation explicite de l'utilisateur. Toute hypothèse non confirmée doit figurer dans `.ai/ASSUMPTIONS.md`.
7. **Principe Framework-First** : Avant de créer une fonctionnalité custom, vérifier si Sylius 2.x ou Symfony 7.4 fournit déjà un mécanisme natif ou un point d'extension adapté (`Product`, `ProductImage`, `Taxon`, `sylius/cms-plugin`).
8. **Environnement de Référence Docker** : Sylius et ses services (PHP, MySQL 8.4, Nginx, Node) doivent s'exécuter dans des conteneurs Docker. Aucune dépendance locale n'est requise.
9. **Politique de Commits Atomiques & Conventional Commits** :
   - Tout développement est découpé en incréments petits, cohérents et vérifiables.
   - Les messages de commit respectent la spécification Conventional Commits (`feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `chore:`, `build:`, `style:`).
10. **Protection de `main`, Branching & PR CI Policy** :
    - La branche `main` est strictly protégée. Aucun développement ni merge direct local ne doit se faire sur `main`.
    - Chaque tâche s'exécute sur une branche dédiée (`feat/...`, `fix/...`).
    - La création de PR et le merge se font obligatoirement via `gh` CLI avec l'authentification `GH_TOKEN` du fichier `.env.dev` (`GH_TOKEN=$GITHUB_TOKEN gh pr create`).
    - **INTERDICTION STRICTE** de merger localement sur `main` sans que les vérifications automatisées (CI) de la Pull Request sur GitHub ne soient validées et vertes.
    - Après chaque merge à distance, revenir sur `main`, synchroniser (`git pull origin main`), supprimer la branche locale et mettre à jour `.ai/STATE.md` et `.ai/TASKS.md`.
11. **Autorisation Explicite pour les Commits (`git add` / `git commit`)** :
    - **INTERDICTION STRICTE** d'exécuter `git add` ou `git commit` sans demander l'autorisation préalable explicite et recevoir le feu vert de l'utilisateur.
