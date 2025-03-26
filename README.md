# UltimateManager

UltimateManager est une application de gestion pour l'Ultimate Frisbee, permettant de suivre les joueurs, les matchs et les statistiques des équipes.

### Accéder au site

- URL Du Site : [UltimateManager](https://ultimatemanager.alwaysdata.net)
- Identifiant : `indi`
- Mot de passe : `blt`

### Connexion à la base de données

Pour accéder à la base de données, utilisez les informations suivantes :

- **URL de connexion** : [phpMyAdmin](https://phpmyadmin.alwaysdata.com)
- **Nom d'utilisateur MySQL** : `385401_visiteur`
- **Mot de passe** : `$iutinfo`

### Structure de la base de données

La base de données est composée de plusieurs tables principales :

- `joueur` : Informations sur chaque joueur (nom, prénom, numéro de licence, taille, poids, etc.).
- `rencontre` : Informations sur chaque rencontre (date, heure, nom de l'adversaire, lieu, résultat, etc.).
- `participer` : Relation entre les joueurs et les rencontres, indiquant la présence d'un joueur dans un match, sa note, son rôle, etc.

### Installation

Pour installer et exécuter le projet localement :

1. Clonez le dépôt :

   ```bash
   git clone https://github.com/Arcols/UltimateManager.git
    ```

### Récupérer un token JWT
Connectez vous à l'api suivante : [UltimateManager API Auth](https://immolink.alwaysdata.net/authapi.php) à l'aide de Postman
- Effectuez une requête POST avec le body suivant :
```json
{
    "username": "indi",
    "password": "blt"
}
```
- Vous obtiendrez un token JWT que vous pourrez utiliser pour accéder à l'API

### Utilisation de l'API
La documentation de l'api est disponible dans le fichier **docAPI.html** présent à la racine du projet