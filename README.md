# BileMo-API

## Installation
   1. Récupérer une copie du projet
      - Cliquer sur le bouton contextuel **Code** dans la barre de navigation de fichier
      - Dans la liste déroulante, sélectionner **Download ZIP**
      - Sauvegarder le fichier dans l'emplacement de votre choix
      - Faire un clic droit sur l'archive et choisissez **Extraire vers...**
      - Choisir l'emplacement qui vous convient
      
  2. Installer la base de données
      - Ouvrir la console en ligne de commande de votre choix et déplacez-vous jusque dans le dossier racine du projet *(commande `cd <chemin>` sur windows)* 
      - Créer la base de données en entrant la commande `bin/console doctrine:database:create`
      - Mettre à jour la structure de la base de données en entrant la commande `bin/console doctrine:migrations:migrate`
      - Charger le jeu de fausses données dans la base de données en entrant la commande `bin/console doctrine:fixtures:load`

  3. Configurer les variables d'environnement
      - Dans le fichier .env, ajouter/modifier les variables suivantes:
           - DATABASE_URL : les identifiants de la base de données: `mysql://user:password@127.0.0.1:3306/db_name?serverVersion=5.7` où user corresponds au nom du compte ayant accès à la base de données, password corresponds au mot de passe et db_name au nom de la base de données
           - APP_ENV : mettre la valeur prod

## Informations complémentaires

Des comptes enregistrés existent parmi les fixtures. Si vous désirez tester l'api sans attendre, vous pouvez utiliser le corps de requête suivant sur l'endpoint `POST /api/login_check`:
```
{
  "name":"Phonogonie",
  "password":"multipass"
}
```

Sinon, vous pouvez créer un nouveau compte en allant sur l'endpoint `POST /api/stores`. Le corps de requête doit avoir le format suivant :
```
{
  "name": "string",
  "email": "string",
  "password": "string"
}
```

La documentation complète de l'API peut être consultée à l'adresse `GET /api/doc` par défaut et à l'adresse ` GET /api/doc.json` au format json.

Les diagrammes relatifs au projet peuvent être visualisés dans le dossiers `/diagrams`
