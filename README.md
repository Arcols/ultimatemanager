# UltimateManager

UltimateManager est une application de gestion pour l'Ultimate Frisbee, permettant de suivre les joueurs, les matchs et les statistiques des équipes.

## Prise en main

### Connexion à la base de données

Pour accéder à la base de données, utilisez les informations suivantes :

- **URL de connexion** : [phpMyAdmin](https://phpmyadmin.alwaysdata.com)
- **Nom d'utilisateur MySQL** : `385401_visiteur`
- **Mot de passe** : `$iutinfo`

> **Remarque** : Assurez-vous de bien protéger ces informations d'identification et de ne pas les partager publiquement.

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
