# Installation de votre site
## Prérequis
Pour installer mc sur un serveur vous appartenant, il vous faudra quelques éléments indispensables : un [serveur web prenant en charge PHP](https:#serveur-web-php) et une [base de donnée type mySql](https:#base-de-données).
#### Serveur Web PHP
En complément de l'installation du service PHP, celui-ci (le compte avec lequel le service fonctionne) doit avoir les **accès en écriture et suppression sur le répertoire racine de l'installation** et sur tous les fichiers/dossiers inclus. Ces autorisations sont nécessaires pour mener à bien les processus d'installation et de mise à jour.
De plus, le moteur php doit permettre les lectures de fichiers distants (http get) pour que l'installation et la mise à jour puissent se dérouler sans problème.  
Fonctionnement testé avec succès sur les configurations/versions suivantes :
 - Apache 2.4
 - PHP 7.2
#### Base de données
Il vous faut un service de base de donnée relationnelle type *mySql*.  
Fonctionnement testé avec succès sur les configuration/versions suivantes :
 - MariaDB 10
## Séquence d'installation
1. Copier tout le contenu (y compris le(s) sous-dossier(s)) du dossier **Setup** du présent repositery vers le dossier choisi sur votre serveur pour héberger le site mc.
1. Vérifier que votre installation de PHP a les droits nécessaires pour écrire et supprimer des fichiers dans le dossier choisi et ses sous-dossiers.
1. Il vous faut un identfiant pour vous connecter à votre serveur de base de données.
1. Si vous avez déja préparé une nouvelle base de données vierge sur votre serveur de base de donnée, vous pouvez passer directement à l'[étape 6](https:#setupLaunch)
1. Connectez-vous à votre interface de gestion mySQL afin de créer une nouvelle base de donnée. Vous pouvez également passer ce point en forunissant au processus d'installation les informations de connexion d'un utilisateur pouvant créer une base sur votre serveur.
1. <a name="setupLaunch"></a>Utiliser un navigateur Internet pour vous connecter à la racine de votre site web. (selon votre cas, vous taperez directement le nom de votre dossier ou vous devrez le faire suivre de "*/setup.php*".
1. Dans l'écran d'installation qui vous est présenté renseignez les informations suivantes :
   - Nom/adresse du serveur mySql : vous pouvez utiliser *localhost* si le serveur de base de données est installé sur la même machine que le serveur web.
   - Numéro de port du serveur (3007 par défaut)
   - Nom de connexion au serveur : Nom de connexion de l'utilisateur de la base créée ou pouvant créer une base sur le serveur
   - Mot de passe de connexion au serveur : Mot de passe du précédent utilisateur
   - Nom de la base existante : Cochez ce choix si vous avez déjà créé une base mySQL et indiquez ici son nom
   - Nom de la base à créer : Cochez ce choix pour que le processus d'installation crée une nouvelle base de données sur le serveur
1. Ensuite, patientez et suivez les indications de la page, le processus d'installation va réellement débuter.
A l'issue de l'installation, votre site devrait être fonctionnel. Si vous appelez de nouveau la page "*/setup.php*" celle-ci vérifiera s'il est utile de mettre à jour votre installation du site et s'occupera [de la mise à jour éventuelle](https:#Mise-à-jour-de-votre-site).  
Sinon, vous pouvez désormais consulter la rubrique [Utilisation de votre site](https:#Utilisation-de-votre-site) et vous serez prêt à joueur dans un instant !  
**Note :** Si vous utilisez le site pour jouer à distance avec des amis (but original de ce développement), il faudra bien sur que le site web soit accèssible de tous les joueurs sur Internet.
# Mise à jour de votre site
*En construction*
# Utilisation de votre site
Une fois votre site installé, vous pouvez simplement y accéder depuis votre navigateur. Vous tomberez dès lors sur l'écran de sélection de fonction suivant
![Ecran de connexion](illus1.png "Ecran de connexion")
- Si la partie qui vous intéresse a déjà été créée ([voir plus loin](https:#création-dune-nouvelle-partie)), vous pouvez simplement saisir son mot-clef dans le premier champ avant de cliquer sur "**OK**"
 - Pour créer une nouvelle partie, choisissez le *nombre de joueurs* depuis le menu avant de cliquer sur "**Créer**".
   - Vous pouvez héberger le suivi de parties pour 1 à 4 joueurs
   - Vous pouvez suivre seulement le compteur de méchant en choisissant "**Méchant seul**" (utile pour certaines parties spécifique comme le suivi de *Kang*).
 - Dans les deux cas, si vous êtes sur pc/Mac, vous serez ensuite dirigé vers l'écran principal de la partie. Si vous consultez le site depuis un smartphone, vous serez redirigé vers votre fiche de joueur.
# Création d'une nouvelle partie
*En construction*
# Comment utiliser le site
*En construction*
## Gestion de la partie
![Ecran principal](illus2.png "Ecran principal")

*En construction*
## Gestion du méchant
![Ecran principal](illus3.png "Ecran principal")

*En construction*
## Gestion du joueur
![Ecran principal](illus4.png "Ecran principal")

*En construction*
## Gestion des Manigances
![Ecran principal](illus5.png "Ecran principal")

*En construction*
