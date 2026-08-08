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
   - Les messages de commit respectent le standard Conventional Commits (`feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `chore:`, `build:`, `style:`).
   - Toujours inspecter `git diff` et exécuter `.ai/WORKFLOW.md` avant de committer. Ne jamais committer de secrets ou de code cassé.
