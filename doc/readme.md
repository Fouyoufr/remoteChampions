# Installation de '*Remote Champions*'
## Prérequis
Pour installer *Remote Champions* sur un serveur vous appartenant, il vous faudra quelques éléments indispensables : un [serveur web prenant en charge PHP](https:#serveur-web-php) et une [base de donnée type mySql](https:#base-de-données).
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
1. Copier tout le contenu (y compris le(s) sous-dossier(s)) du dossier **Setup** du présent dépot vers le dossier choisi sur votre serveur pour héberger le site *Remote Champions*.
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
A l'issue de l'installation, votre site devrait être fonctionnel. Si vous appelez de nouveau la page "*/setup.php*" celle-ci vérifiera s'il est utile de mettre à jour votre installation du site et s'occupera [de la mise à jour éventuelle](https:#Mise-à-jour-de-remote-champions).  
Sinon, vous pouvez désormais consulter la rubrique [Utilisation de votre site](https:#Utilisation-de-remote-champions) et vous serez prêt à joueur dans un instant !  
**Note :** Si vous utilisez le site pour jouer à distance avec des amis (but original de ce développement), il faudra bien sur que le site web soit accèssible de tous les joueurs sur Internet.
## Mise à jour de '*Remote Champions*'
Lorsque le site est installé, vous pouvez vérifier les mises à jours récentes en utilisant le bouton dans l'écran d'administration ou en accèdant à la page "*/setup.php*".
*(En construction)*
# Utilisation de '*Remote Champions*'
**Préambule:** L'outil *Remote Champions* n'est en aucun cas prévu pour remplacer la possession des cartes par les joueurs : il  apour seul objectif de fluidifier le déroulement des parties (particulièrement des parties jouée à distance, avec audio/visio conférence) en tenant à jour un maximum des éléments nécessaires au suvi de la partie et en les présentant aux différents joueurs.  
Utilisez votre navigateur Internet, depuis votre ordinateur ou depuis votre smartphone pour accéder à votre installation de *Remote Champions*. La page d'accueil se présente comme l'illustration suivante:  
![Page de connexion](illus1.png "Page de connexion")
- Si la partie qui vous intéresse a déjà été créée, vous pouvez simplement saisir **son mot-clef** dans le premier champ![1](tag1.png) avant de cliquer sur "*OK*"![2](tag2.png),
- En cliquant sur le bouton "**Créer**"![3](tag3.png), vous basculez sur la page de [création d'une nouvelle partie](https:#Création-dune-nouvelle-partie)(a noter qu'un mot de passe peut être demandé si la fonction de serveur public a été activé dans l'écran d'administration décrit plus loin),
- En dehors des écrans d'administration et d'installation/mise à jour, en cliquant sur la barre d'administration![4](tag4.png) vous serez dirigé vers l'écran d'administration, décrit plus loin (A noter, un mot de passe sera nécessaire pour se connecter sur ces pages sensibles, le mot de passe par défaut après installation est **admin**),
- Dans tous les écrans du site, en cliquant sur le bouton d'Informations![5](tag5.png), vous serez directelent dirigé sur la présente aide d'utilisation,
- Dans tous les écrans du site, en cliquant sur le bouton de rapport de bug![6](tag6.png), vous serez directement dirigé sur *gitHub* afin de saisir une description du problème que vous rencontrez (Un compte gratuit *gitHub* sera nécessaire. Merci pour vos retours!).
## Création d'une nouvelle partie
La page de création d'une nouvelle partie ressemblera à l'illustration ci-dessous:  
![Page de création de partie](illus6.png "Page de création de partie")  
Pour créer une nouvelle partie, vous pouvez choisir le nombre de joueurs (1 à 4) depuis le menu![1](tag1.png).  
**À noter :** vous pouvez également créer une partie ayant pour seul objectif de suivre les information d'un méchant en cliquant sur la case "*Méchant seul*"![2](tag2.png). cela peut, par exemple, être utile pour créer plusieurs parties et suivre plusieurs méchants, dans une partie avec *Kang*.  
Une clef d'accès par défaut est automatiquement affectée à la partie que vous allez créer. Si vous le souhaitez, vous pouvez modifier la valeur de cette clef proposée dans le champ "*Clef d'accès*"![3](tag3.png) (A noter: les clefs d'accès doivent impérativement comporter 6 caractères et être uniques).  
Une fois ces informations essentielles saisies, vous pouvez cliquer sur "*Créer*"![4](tag4.png) pour lancer la création de la partie.
### Options
Vous pouvez (cela n'est pas une obligation, mais peut permettre de simplifier la sélection des manigances pendant la partie) également choisir les decks qui seront utilisé pendant votre partie.  
Pour ce faire, il vous suffit de cliquer sur la case à cocher![5](tag5.png) en regard d'un deck pour le sélectionner ou le déselectionner avant de cliquer sur le bouton "*Créer*".  
 - Les decks pouvant être selectionnés sont regroupés par boite de jeu/extensions/paquets![6](tag6.png) pour les retrouver plus facilement. Les boites disponibles sont celles déclarée comme telles dans l'administration du site (voir plus loin).
 - Vous pouvez (dé)sélectionner tous les decks en un click![7](tag7.png) pour plus de failité.
Après que vous avez créé votre nouvelle partie, vous serez dirigé vers la page de celle-ci (ou vers votre fiche de joueur si vous êtes sur smartphone).  

Si une erreur empèche la création de la partie, elle vous sera communiquer sur la même page, afin que vous puissiez retenter la création.
## Utilisation du site en partie
### Présentation de la page de partie
La page principale d'une partie qui vient d'être créée ressemble à l'illustration suivante:  
![Page principale nouvelle partie](illus2.png "Page principale nouvelle partie")  
Cette page est divisée en différentes zones:  
![1](tag1.png)La fiche de joueur, qui représente l'état de chaque joueur (elle est détaillée [plus loin](https:#Gestiondujoueur)).  
![2](tag2.png)La synthèse du méchant qui fournit les informations sur la situation actuelle du méchant de la partie (elle est détaillée [plus loin](https#Gestionduméchant))  
![3](tag3.png)L'indicateur du premier joueur. Le premier joueur est désigné au hasard parmi les joueurs de la partie lors de la sélection de la manigance principale. Il suffira ensuite de cliquer sur l'indicateur du premier joueur pour indiquer que c'est le tour du joueur suivant (l'indicateur se déplace).  
![4](tag4.png)Un ensemble de compteurs annexes. A vous de voir si vous avez besoin d'autres compteurs que les principaux prévus : ce pourra servir pour suivre des éléments spécifiques de certaines missions...  
![5](tag5.png)La gestion des manigances (détaillée [plus loin](https#GestiondesManigances)).  
![6](tag6.png)A tout moment, le **mot-clef d'accès** à la partie, à communiquer à tous les joueurs pour que ceux-ci puissent la rejoindre est rappelé en bas de page. (nota : En cliquant dessus, il est possible d'ouvrir l'écran d'administration décrit plus loin).  
## Paramètrage initial de la partie
![Initialisation de la partie](illus7.png "Initialisation de la partie")  
1. Il est conseillé de commencer par attribuer les places aux joueurs:
  - Mettez-vous d'accord avec tous les joueurs (y compris distants) pour trouver une disposition ayant le plus de sens pour l'ensemble des joueurs de la partie.
  - Cliquez sur le nom de chaque joueur[6](tag6.png). Cela fait appraitre la fenêtre de changement de nom de joueur : saisissez le nouveau nom/surnon du joueur et validez.
  - Cliquez sur l'image de héros de l'emplacement de joueur pour faire apparaître la fenêtre de sélection de héros.
  - Seléctionnez le héros qui sera joué par le joueur en question (la liste des héros présentés dépend des boites/packs déclarés disponibles dans l'administration du site -voir plus loin-).
  - La vie maximum du joeur est affectée automatiquement.
2. Avant de commencer réellement à jouer, choisissez le méchant qui sera combattu pendant la partie:
  - Cliquez sur l'image de méchant neutre![1](tag1.png). (Vous pourrez changer de méchant à tout moment en cliquant de nouveau sur son image).
  - La fenêtre de sélection de méchant apparait[2](tag2.png). Cliquez sur le méchant choisi pour la partie (la liste des méchants présentés dépend des boites/packs déclarés disponibles dans l'administration du site -voir plus loin-)
  - Une fois le méchant choisi, la fenêtre de sélection de la manigance principale[3](tag3.png) apparait. Sélectionnez la manigance principale dans le menu (la liste présentée dépend des boites/packs déclarés disponibles dans l'administration du site -voir plus loin-) et confirmez. (si vous annulez ou souhaitez changer la manigance principale, cliquez sur son titre[4](tag4.png)).
  - Après la sélection de la manigance principale, l'indicateur de premier joueur est attribué au hasard, vous pouvez utiliser cette fonction pour décider qui jouera en premier pour débuter la partie.
  - Si vous jouez une partie dans laquelle le méchant ne commence par à la phase I, cliquez sur l'indication de phase pour passer à la phase suivante dès le début de partie.
  - La vie du méchant est initialisée, en fonction du nombre de joueurs (Ajustez là si vous utilisez *Remote Champions* pour suivre uniquement la vie du méchant).

**Conseil :** Mettez-vous d'accord avec tous les joueurs pour affecter la responsabilité des diverses mises à jour. Sinon, comme tous les joueurs peuvent éditer la page de la partie en cours, vous risquez d'avoir des modifications multiples.  

En cours de partie, selon l'évolution de celle-ci, la page de partie ressemblera à quelque-chose comme l'illustration suivante:  
![En cours de partie](illus8.png|width=100 "Page de partie en cours")
*(En construction)*
## Gestion du méchant
![Ecran principal](illus3.png "Ecran principal")

*(En construction)*
## Gestion du joueur
![Ecran principal](illus4.png "Ecran principal")

*(En construction)*
## Gestion des Manigances
![Ecran principal](illus5.png "Ecran principal")

*(En construction)*
## Administration du serveur
### Activation des boites de jeu
*(En construction)*