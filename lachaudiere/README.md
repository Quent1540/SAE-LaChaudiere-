# LaChaudièreAgenda.core

**LaChaudièreAgenda.core** est une application backend développée en **PHP** dans le cadre de la SAE "Architecture – développement serveur" du Semestre 4 du BUT Informatique (IUT Nancy-Charlemagne).

Ce projet comprend :
- une **API REST** pour exposer les événements culturels de La Chaudière,
- une **interface d’administration** pour gérer ces événements,
- une **base de données** relationnelle interrogeable via ORM.

---

## Objectifs pédagogiques

Ce backend a été réalisé dans le cadre de la **SAE S4-DWM.01** :
> Conception et développement d’une application web côté serveur.

- **Compétence ciblée** : Réaliser une application web complète, modulaire, sécurisée et maintenable.
- **Public visé** : développeurs backend, équipes front/mobile, administrateurs de La Chaudière.

---

## Technologies utilisées

- **Langage** : PHP 8.x
- **Framework** : [Slim 4](https://www.slimframework.com/)
- **ORM** : Eloquent (issu de Laravel)
- **Template Engine** : Twig
- **Base de données** : MySQL (MariaDB compatible)
- **Authentification** : Sessions PHP + Authentification par formulaire
- **Sécurité** : filtrage, échappement, protection CSRF, validation côté serveur

---

## Lancement local avec Docker

### 1. Prérequis

- Docker Desktop installé : [https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)

### 2. Commandes de lancement

Dans le répertoire `lachaudiere/` :

bash :
```
docker compose up --build
```
--- 

# Accès

### Admin Web : http://localhost:12345

### Base de données Adminer : http://localhost:12346

### Connexion Adminer :

- Système : MariaDB

- Serveur : sql

- Utilisateur : root

- Mot de passe : root

- Base de données : lachaudiere

---

# Comptes utilisateurs disponibles
### Email /	Mot de passe /	Rôle
- superadmin@lachaudiere.fr	/ superadmin_secret	/ Superadmin
- admin.concerts@lachaudiere.fr	/ admin123 / Admin

---
## Fonctionnalités réalisées

L’application implémente toutes les **14 fonctionnalités** attendues :

### Interface d’administration

1. ✅ Affichage de la liste des événements
2. ✅ Détail complet d’un événement
3. ✅ Création d’un nouvel événement
4. ✅ Suppression d’un événement
5. ✅ Édition des événements (modification)
6. ✅ Gestion des catégories
7. ✅ Gestion des utilisateurs (authentification sécurisée)
8. ✅ Ajout/modification/suppression d’une image associée à un événement

### API RESTful (accès public ou authentifié selon les routes)

9. ✅ Liste des événements (format JSON)
10. ✅ Détail d’un événement
11. ✅ Liste des catégories
12. ✅ Événements d’une catégorie
13. ✅ Serveur d’images
14. ✅ Routage propre + erreurs JSON (404, 401, etc.)

---

## Arborescence simplifiée

```
lachaudiere.core/
├── public/
│   └── index.php               # Point d'entrée (front controller)
├── src/
│   ├── api/                    # Contrôleurs API REST
│   ├── application_core/       # Logique métier (gestion des événements, utilisateurs, etc.)
│   ├── conf/                   # Configuration : connexion BDD, dépendances, etc.
│   ├── infrastructure/         # Modèles, ORM (Eloquent), adaptateurs techniques
│   └── webui/                  # Contrôleurs UI (administration avec Twig)
├── .env                        # Variables d'environnement (MySQL, app)
├── composer.json               # Dépendances PHP (Slim, Twig, Eloquent)
├── README.md                   # Ce fichier
``` 

