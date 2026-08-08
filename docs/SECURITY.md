# SECURITY.md - Sécurité & Protection de l'Application

## 🛡️ Règles de Sécurité Sylius / Symfony

1. **Firewall & Authentification Back-Office** :
   - L'accès à Sylius Admin (`/admin`) est protégé par le composant `security` de Symfony avec hachage fort (Bcrypt / Argon2id).
   - Rôle requis : `ROLE_ADMINISATRATION_ACCESS`.

2. **Validation & Filtrage des Fichiers Téléversés** :
   - Les photographies envoyées depuis le back-office doivent être restreintes aux types MIME d'image autorisés (`image/jpeg`, `image/png`, `image/webp`).
   - Les enregistrements audio doivent être strictement contrôlés (`audio/mpeg`, `audio/wav`, `audio/ogg`).

3. **Protection contre les Injections & CSRF** :
   - Tous les formulaires Sylius Admin et le formulaire de contact public comportent un jeton de protection CSRF.
   - Les requêtes SQL sont intégralement paramétrées via Doctrine ORM.
