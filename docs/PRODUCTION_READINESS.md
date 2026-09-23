# ZEN TOO Craft — État de préparation à la mise en production

**Date de référence** : 23 septembre 2026  
**Domaine cible** : `zentoo-craft.fr` (et `www.zentoo-craft.fr`)  
**VPS cible** : `51.178.50.217` (OVHcloud)  
**Reverse Proxy frontal** : Traefik v3.2.1 (Partagé avec *Korning*)  

---

## 1. Objectif

Ce document décrit l’état actuel de la préparation à la mise en production de ZEN TOO Craft, les éléments déjà configurés, les dépendances encore en attente et les contrôles à réaliser avant le déploiement du projet sur le VPS.

Le besoin fonctionnel prioritaire côté messagerie est la mise en place d’un formulaire de contact capable d’envoyer des e-mails transactionnels, avec une configuration de domaine propre et une solution d’envoi dédiée.

---

## 2. Synthèse de l'état global

| Domaine / Élément | État | Commentaire |
| :--- | :--- | :--- |
| **Nom de domaine** | ✅ Fait | `zentoo-craft.fr` créé et géré chez OVHcloud |
| **DNS vers VPS** | ✅ Fait | `@` et `www` pointent vers l’IPv4 publique `51.178.50.217` |
| **DNS e-mail OVH** | ✅ Présent | MX et SPF OVH conservés |
| **Zimbra Starter** | ⏳ En attente | Offre incluse mais pas encore exploitable dans l’interface OVH |
| **Adresse `contact@zentoo-craft.fr`** | ⏳ En attente | À créer dès que Zimbra Starter est provisionné |
| **Compte Brevo** | ⏳ En attente | À créer après disponibilité d’une adresse e-mail professionnelle |
| **Authentification Brevo / DNS** | ⏳ En attente | DKIM / DMARC / fusion SPF à ajouter dans la zone OVH |
| **Projet applicatif sur VPS** | ⏳ Non déployé | Le dépôt n’a pas encore été pullé sur le VPS |
| **Audit Docker Compose** | ⏳ À faire | Audit production complet requis avant déploiement |
| **Audit Docker Compose Override** | ⏳ À faire | S'assurer qu'aucune configuration dev ne fuit en production |
| **Configuration Traefik pour ZEN TOO** | ⏳ À faire | Routage via File Provider (`zentoocraft.yml`) |
| **HTTPS / certificat** | ⏳ À faire | À valider lors du déploiement via Let's Encrypt |
| **Sauvegardes / restauration** | ⏳ À définir | Stratégie DB + médias `/srv/sylius/public/media` |
| **Monitoring / logs** | ⏳ À définir | Rétention des logs et alerting minimal |

---

## 3. Nom de domaine et DNS

### 3.1 Domaine
- **Nom de domaine** : `zentoo-craft.fr`
- **Registrar / DNS** : OVHcloud
- **DNSSEC** : Activé

### 3.2 VPS cible
- **IPv4 publique actuelle** : `51.178.50.217`

### 3.3 Zone DNS actuellement configurée
Les entrées web ont été configurées pour pointer directement vers le VPS :
```dns
@      A      51.178.50.217
www    A      51.178.50.217
```
Les anciennes entrées de redirection/page d’attente OVH ont été retirées.

Les entrées nécessaires au service e-mail OVH ont été conservées :
```dns
@      MX     1 mx1.mail.ovh.net.
@      MX     5 mx2.mail.ovh.net.
@      MX     100 mx3.mail.ovh.net.
@      SPF    v=spf1 include:mx.ovh.com -all
```

---

## 4. Architecture Traefik existante sur le VPS (Cohabitation Korning)

Le VPS héberge déjà une infrastructure active pour le projet **Korning**.

### 4.1 Traefik
- **Version** : `traefik:v3.2.1`
- **Ports d'écoute** : `80/tcp` (HTTP) et `443/tcp` (HTTPS)
- **Réseau partagé** : `traefik_app-network` (externe)
- **Resolver ACME** : `myresolver` (TLS challenge, `/letsencrypt/acme.json`)

### 4.2 Mode de configuration actuel
La configuration utilise le **File Provider** :
```bash
--providers.file.directory=/etc/traefik/routing
--providers.file.watch=true
--providers.docker=false
```
Les routes existantes sont déclarées dans `/home/project/traefik/dynamic_conf/prod/`.

### 4.3 Stratégie retenue pour ZEN TOO Craft
Pour préserver la stabilité de l'existant sans redémarrer Traefik avec `--providers.docker=true`, **ZEN TOO Craft utilisera le File Provider**.
Un fichier de routage dynamique dédié (`/home/project/traefik/dynamic_conf/prod/zentoocraft.yml`) déclarera :
- Les routers HTTPS pour `zentoo-craft.fr` et `www.zentoo-craft.fr` avec redirection canonique.
- Le service HTTP routant vers le conteneur Nginx ZEN TOO Craft connecté au réseau `traefik_app-network`.

---

## 5. E-mails — Zimbra Starter & Brevo

### 5.1 Zimbra Starter (OVH)
- L’offre souscrite donne droit à une boîte Zimbra Starter.
- Actuellement en attente de provisionnement côté OVH (quota affiché 0/0).
- **Action attendue** : dès provisionnement, créer `contact@zentoo-craft.fr`.

### 5.2 Compte Brevo & Délivrabilité
Brevo assurera l'envoi des emails transactionnels (formulaire de contact).

**Séquençage strict** :
```
Zimbra opérationnel 
  ➔ contact@zentoo-craft.fr 
  ➔ Création compte Brevo 
  ➔ Authentification zentoo-craft.fr 
  ➔ DKIM / DMARC / Fusion SPF dans DNS OVH 
  ➔ Identifiants SMTP en variables de prod 
  ➔ Recette formulaire
```

> ⚠️ **Règle DNS RFC critique** : Ne jamais ajouter une deuxième entrée SPF. Il faudra fusionner l'entrée existante OVH avec Brevo :
> `v=spf1 include:mx.ovh.com include:spf.brevo.com -all`

---

## 6. Audit Docker & Séparation des Environnements

### 6.1 Découplage strict Dev vs Prod
En production, le fichier `compose.override.yml` **ne doit jamais être chargé**. Le déploiement s'exécutera via :
```bash
docker compose -f compose.yml -f compose.prod.yml up -d
```

### 6.2 Spécifications du `compose.prod.yml`
- **Services exclus en prod** : `mailhog` désactivé, `nodejs` désactivé (assets compilés au build).
- **Réseau privé interne** : `ztc_internal` pour PHP ↔ MySQL.
- **Réseau frontal** : `traefik_app-network` connecté **uniquement** à Nginx.
- **Exposition ports** : Aucun port `3306` ni `80` mappé directement sur l'hôte.
- **Persistance garantie** :
  - Volume persistant pour MySQL : `mysql_prod_data`
  - Volume persistant pour les médias : `ztc_media_data` monté sur `/srv/sylius/public/media` (photos produits, extraits audio, visuels CMS).
- **Configuration applicative** :
  - `APP_ENV=prod`, `APP_DEBUG=0`
  - `DATABASE_URL` avec mot de passe fort
  - `MAILER_DSN=smtp://...` pointant vers le relais SMTP Brevo
  - Clés JWT et secrets Symfony non versionnés

---

## 7. Sécurité & Protection du Formulaire

- **CSRF** : Validation active (`form_widget(form._token)`).
- **Anti-Spam** : Champ honeypot masqué en CSS pour piéger les bots sans gêner les utilisateurs.
- **Rate Limiting** : Limitation de débit (Symfony RateLimiter) pour éviter l'épuisement du quota journalier Brevo.
- **Destinataire fixe** : L'adresse de destination est fixée côté serveur (`$channel->getContactEmail()`) et ne peut être altérée depuis le navigateur.

---

## 8. Checklist d'Exécution Pré-Production

- [x] Domaine `zentoo-craft.fr` acheté chez OVHcloud
- [x] DNS racine `@` et `www` pointent vers le VPS (`51.178.50.217`)
- [x] MX et SPF OVH conservés
- [ ] Zimbra Starter provisionné et actif chez OVH
- [ ] Boîte e-mail `contact@zentoo-craft.fr` créée et testée en émission/réception
- [ ] Compte Brevo créé avec `contact@zentoo-craft.fr`
- [ ] Domaine authentifié dans Brevo (DKIM et DMARC)
- [ ] Fusion SPF effectuée dans la zone DNS OVH
- [ ] Fichier `compose.prod.yml` rédigé et validé
- [ ] Fichier de routage Traefik `zentoocraft.yml` préparé
- [ ] Dépôt cloné sur le VPS
- [ ] Variables d'environnement de production sécurisées (`.env.local` / secrets)
- [ ] Migrations Doctrine exécutées
- [ ] Certificat SSL Let's Encrypt émis et testé via Traefik
- [ ] Redirection canonique HTTP ➔ HTTPS et `www` active
- [ ] Formulaire de contact testé de bout en bout
- [ ] Procédure de sauvegarde et de rollback documentée
