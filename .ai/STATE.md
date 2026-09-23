# STATE.md - Photo Instantanée du Projet

> **Ce fichier donne une image précise de l'état du projet ZEN TOO Craft.**

**Dernière mise à jour** : 2026-09-23
**Branche Courante** : `main`

## 🚀 Phase Actuelle
Phase 9 : Pré-production, Messagerie & Déploiement VPS (Déploiement VPS Validé & Opérationnel ✅).

## 🎯 Focus Courant
Finalisation de la messagerie transactionnelle (activation de Zimbra Starter OVH, création de `contact@zentoo-craft.fr`, configuration Brevo SMTP & fusion SPF).

## ✅ Réalisé
- [x] Initialisation du socle Sylius 2.2 / Symfony 7.4 et installation du plugin CMS officiel (`sylius/cms-plugin` ^1.1).
- [x] Création de la ressource Sylius `App\Entity\Product\ProductAudio` rattachée au `Product` natif avec une cardinalité ouverte (`ManyToOne`).
- [x] Déclaration de la ressource `app.product_audio` dans `config/packages/sylius_resource.yaml`.
- [x] Création du formulaire `ProductAudioType` et extension du `ProductType` dans Sylius Admin.
- [x] Implémentation du listener d'événement `ProductAudioUploadListener` pour la gestion des téléversements et de la suppression physique des fichiers audio (`public/media/audio`).
- [x] Migration de l'environnement Docker vers PHP 8.4 (`ghcr.io/sylius/sylius-php:8.4-fixuid-xdebug-alpine`) et démarrage des conteneurs (`make up`).
- [x] Intégration du formulaire WYSIWYG (Quill.js) sur les champs de description de produit et les pages CMS.
- [x] Implémentation du service IA Ollama (Qwen 2.5 local) avec les boutons `Générer la description` (FR) et `Générer la traduction` (EN) dans Sylius Admin.
- [x] Nettoyage strict des langues système en base et en cache pour restreindre l'application uniquement au Français (`fr`) et à l'Anglais (`en`).
- [x] Simplification & harmonisation du menu Admin : masquage des sous-sections superflues et remplacement du terme "Taxon" par "Catalogue".
- [x] Raccordement dynamique du menu principal du haut (Navbar) pour afficher les catégories du canal.
- [x] Refonte ergonomique et responsive style Apple (bannières dépolies, pastilles de sous-catégories, filtres contextuels).
- [x] Fiche produit avec lecteur audio HTML5 sur-mesure et formulaire de contact Obsidienne & Or.
- [x] Configuration Docker de production (`compose.prod.yml`) avec réseau interne `ztc_internal`, isolation des ports MySQL/PHP/Ollama, et raccordement Nginx au réseau externe `traefik_app-network`.
- [x] Configuration du routage dynamique Traefik File Provider (`dynamic_conf/prod/zentoocraft.yml`) dans le dépôt Traefik du VPS.
- [x] Clé SSH dédiée `id_ed25519_mrz_ovh` configurée et déployée sur le VPS avec alias `mrz-vps` et agent forwarding GitHub.
- [x] Déploiement initial automatisé sur le VPS OVH (`51.178.50.217`) dans `/home/project/craft-studio`.
- [x] Téléchargement et intégration du modèle IA Ollama `qwen2.5:1.5b` (986 Mo) dans le conteneur `ztc-ollama`.
- [x] Synchronisation des mappings Doctrine, génération des clés JWT de production, et transfert physique des médias dans le volume persistant `ztc_media_data`.
- [x] Délivrance automatique du certificat SSL Let's Encrypt pour `zentoo-craft.fr` via Traefik.
- [x] Vérification de bout en bout (HTTP/2 200 sur l'accueil, catalogues, fiches produits, contact, admin, et redirections 301/308 HTTP->HTTPS et WWW->Apex).
- [x] Zéro perturbation sur les conteneurs et services existants du projet Korning.

## 🔄 En Cours / À Reprendre au Prochain Démarrage
- [ ] Suivre le provisionnement de l'offre Zimbra Starter chez OVH pour créer `contact@zentoo-craft.fr`.
- [ ] Créer le compte Brevo avec l'adresse professionnelle, authentifier le domaine (DKIM, DMARC, fusion SPF OVH/Brevo).
- [ ] Renseigner le `MAILER_DSN` réel dans `/home/project/craft-studio/.env.local` sur le VPS.
- [ ] Poursuivre la rédaction finale des contenus éditoriaux CMS (*Histoire du Savoir-Faire*, *Charte Éco-Responsable*).

## ⚠️ Points de Vigilance
- **Permissions du volume `/srv/sylius/var`** : Le cache et les logs doivent toujours appartenir à `www-data:www-data` (UID 82).
- **Délivrabilité e-mail (SPF)** : Ne jamais ajouter une deuxième entrée SPF dans OVH ; fusionner les directives dans l'enregistrement unique (`include:mx.ovh.com include:spf.brevo.com -all`).
- **Persistance des données** : Les volumes nommés `ztc_mysql_data`, `ztc_media_data` et `ztc_var_data` préservent l'état applicatif.
- **Routage Traefik** : Traefik utilise le File Provider rechargé à chaud sans toucher à `traefik.yml`.

## 🏁 Prochaine Étape / Milestone
Activation de Zimbra Starter OVH dès réception du quota, paramétrage du compte Brevo et validation de la chaîne d'envoi d'e-mails en conditions réelles.
