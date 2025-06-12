# LaChaudièreAgenda.web

Ce projet est une application web de type **Single-Page Application (SPA)** développée en JavaScript pur. Elle permet de consulter l'agenda des événements culturels de "La Chaudière" en consommant les données fournies par l'API REST `LaChaudièreAgenda.core`.

L'application offre des fonctionnalités de navigation, de filtrage, de tri et de mise en favoris des événements, avec une interface réactive et dynamique.

## Contexte du Projet

Ce projet a été réalisé dans le cadre de la **SAE (Situation d'Apprentissage et d'Évaluation) "Atelier de développement d'application web"** du Semestre 4 du BUT Informatique de l'IUT Nancy-Charlemagne.

- **Module** : RA-DWM (Réalisation d'applications : conception, développement, validation)
- **Sujet** : Javascript – développement web client

## Démonstration en Ligne

L'application est déployée et accessible à l'adresse suivante :

**[URL de l'application](urldocketu)**

## Fonctionnalités

Le projet implémente l'ensemble des fonctionnalités demandées dans le cahier des charges, y compris les fonctions étendues.

### Fonctionnalités de base
-  **1. Affichage des événements du mois courant** : La page d'accueil liste les événements du mois en cours, affichant pour chacun le titre, l'artiste, la date et la catégorie.
-  **2. Filtrage par catégorie** : La liste des événements peut être filtrée pour n'afficher que ceux appartenant à une catégorie spécifique.
-  **3. Navigation par catégories** : Une liste des catégories disponibles est affichée. Un clic sur une catégorie met à jour la liste des événements.
-  **4. Affichage détaillé d'un événement** : Un clic sur le bouton "Détail" d'un événement affiche une vue complète avec toutes ses informations (dates, tarif, description, etc.). La description, initialement en Markdown, est convertie en HTML pour un affichage propre.
-  **5. Extension de l'affichage (passé/futur)** : Des filtres permettent de naviguer entre les événements passés, actuels et futurs.

### Fonctionnalités étendues
-  **6. Tri des listes** : Les listes d'événements peuvent être triées par date (ascendante ou descendante) et par titre (alphabétique).
-  **7. Affichage des images** : Si un événement possède une image associée, celle-ci est affichée dans la vue détaillée.
-  **8. Gestion des favoris** : L'utilisateur peut marquer des événements comme "favoris". Ces favoris sont sauvegardés dans le `localStorage` du navigateur et peuvent être consultés via un bouton dédié.

## Technologies et Outils

- **Langages** : HTML5, CSS3, JavaScript (ES6+ avec modules)
- **Librairies** :
  - **Handlebars.js** : Moteur de templates pour générer le HTML dynamiquement et séparer la logique de l'affichage.
  - **Marked.js** : Pour convertir les descriptions d'événements du format Markdown vers HTML.
- **Architecture** :
  - **Single-Page Application (SPA)** : L'application fonctionne sur une seule page, avec des mises à jour de contenu dynamiques via AJAX.
  - **Appels Asynchrones** : Utilisation de l'API `fetch` avec la syntaxe `async/await` pour communiquer avec le backend.
- **Outillage (Tooling)** :
  - **Node.js / npm** : Gestion des dépendances du projet.
  - **esbuild** : Bundler ultra-rapide utilisé pour assembler les modules JavaScript en un seul fichier et le transpiler pour une meilleure compatibilité.

## Installation et Lancement

Pour lancer ce projet en local, vous aurez besoin de [Node.js](https://nodejs.org/) et de npm. L'API backend `LaChaudièreAgenda.core` doit également être en cours d'exécution.

1.  **Cloner le dépôt Git**
    ```bash
    git clone https://github.com/Quent1540/SAE-LaChaudiere-.git
    cd SAE-LaChaudiere-/lachaudiere.web
    ```

2.  **Installer les dépendances**
    ```bash
    npm install
    ```

3.  **Compiler le JavaScript**
    Ce projet utilise `esbuild` pour assembler les modules. Lancez la compilation une première fois :
    ```bash
    npm run build
    ```
    *Optionnel : Pour développer, vous pouvez lancer `npm run dev` qui recompilera le code à chaque modification.*

4.  **Lancer le projet avec un serveur web local**
    Pour éviter les erreurs CORS, vous ne devez pas ouvrir le fichier `index.html` directement. Utilisez un serveur local.

    **Méthode recommandée :**
    Exécutez la commande suivante dans le terminal, à la racine du projet `lachaudiere.web` :
    ```bash
    npx serve
    ```
    Ouvrez ensuite l'adresse fournie (généralement `http://localhost:3000`) dans votre navigateur. L'application devrait fonctionner correctement.

5.  **Prérequis**
    Assurez-vous que :
    - L'API backend `LaChaudièreAgenda.core` est démarrée et accessible sur l'URL configurée dans `lib/config.js` (par défaut `http://localhost:8000`).

## Structure du Projet

```
lachaudiere.web/
├── css/
│   └── index.css           # Fichier de styles principal
├── js/
│   └── out.js              # Fichier JS bundlé (généré par esbuild)
├── lib/
│   ├── config.js           # Configuration (URL de l'API)
│   └── ui.js               # Logique principale de l'application (appels API, manipulation du DOM)
├── index.html              # Point d'entrée de l'application, structure HTML et templates Handlebars
├── index.js                # Point d'entrée JavaScript, initialise l'application
├── package.json            # Dépendances et scripts npm
└── README.md               # Ce fichier
```

## Points d'API Consommés

L'application communique avec les points d'API suivants :

- `GET /api/categories` : Récupère la liste de toutes les catégories.
- `GET /api/categories/{id}/evenements` : Récupère les événements pour une catégorie donnée.
- `GET /api/evenements` : Récupère la liste de tous les événements.
- `GET /api/evenements/{id}` : Récupère les informations détaillées d'un événement spécifique.
- `GET /api/images/{nom_fichier}` : Récupère une image
