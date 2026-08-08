# TESTING.md - Stratégie de Test & Assurance Qualité

## 🧪 Strategie de Tests Sylius / Symfony

1. **Tests d'Intégration & Unitaires (PHPUnit / Symfony TestPack)** :
   - Validation des entités et des extensions Sylius (`Product` avec extension audio).
   - Validation des règles métier de masquage audio et de contact pré-rempli.

2. **Tests Comportementaux (Behat / Sylius Native Test Suite)** :
   - Validation des scénarios d'administration (Ajout d'une création par l'artisan, attribution d'un taxon, masquage).

3. **Tests Ergonomiques & Responsive (Mobile & Audio)** :
   - Recette sur smartphone et tablette (restitution du lecteur audio, fluidité du carousel de photos HD, réactivité des formulaires).
