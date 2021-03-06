# Remote Champions

## *fan-made* engine, to ease remote based games of Marve Champions
You'll need a web/PHP server or a Docker engine to run the website.  
Simply copy the entire content of the "*setup*" folder to the root of your web server and access it...  
[See project documentation](https://github.com/Fouyoufr/remotechampions/blob/main/doc/en/readme.md)  
[Test a sample implementation](http://rchampions.tk)  

## Moteur *fan-made*, pour faciliter les parties distantes de Marvel champions
Vous aurez besoin d'un serveur web/PHP.
Copiez simplement l'intégralité du contenu du dossier "*setup*" à la racine de votre serveur web accèdez-y... 
[Voir la documentation du projet](https://github.com/Fouyoufr/remoteChampions/blob/main/doc/fr/readme.md)  
[Implémentation test](http://rchampions.tk)

# Historique des changements
## Version 6.3
Correction du passage en rouge du bouton de diminution de vie d'un joueur tenace sur l'écran principal.  
## Version 6.2
Remote Champions devient une application web "installable" pour en faciliter l'accès.
## Version 6.1
Bouton de Full Screen sur écrans mobiles
Invite de bascule sur écrans mobiles
## Version 6.0
Adaptation du moteur Ajax en mode "long pooling"  
FInalisation du mini-lecteur Melodice
Correction  transition des Phases de méchant
## Version 5.7
Intégration de lecture de playlist Melodice pendant une partie.
## Version 5.6
Ajout de la boite de héros Vision
## Version 5.5
Corrections de quelques cartes
## Version 5.4c
Indication des boites possédées dans les 2 langues...
## Version 5.4b
Correction info des manigances de Convoitise Galactique
## Version 5.4a
Refonte complète du moteur, sans base de donnée.
(relancer la mise à jour si nécessaire)
Correction DockerFile (fin des tests)
## Version 5.4
Refonte complète du moteur, sans base de donnée.
(relancer la mise à jour si nécessaire)
## Version 5.3
Version sans Base de Données
## Version 5.2
Optimisation de la prise en charge des decks sans SQL
## Version 5.1
Activation automatique des boites lors de la restauration de partie
## Version 5
Refonte complète du moteur, tout en mode direct sur les fichiers XML, en faisant un cache AJAX, de fait nettement plus efficace.
Suppressions des bases SQL devenues inutiles par le setup.
Mise en place du moteur d'affichage (avec automatisme) des informations sur les manigances annexes.
Mise en place d'un système de sauvegarde/restauration des parties du serveur.
## Version 4.2
Informations sur les manigances annexes à date.
## Version 4.1c
Travail sur externalisation des chaines de caractères
## Version 4.1b
Complément d'informations sur les manigances annexes de tous les héros.
## version 4.1a
Nettoyage des styles inclus dans les fichiers pour complèter le css
Début déplacement des textes dans fichiers à part
## Version 4.1
Forcer la mise à jour des fichiers .php depuis le fichier Zip si celui-ci est sélectionné.
Gestion des informations principales sur les manigances, avec automatisation de l'affichage des "une fois révélée" et "une fois déjouée".
(Informations non encore saisies dans la base...)
## Version 4.0
Préparation de la fonction "Une fois révélée" sur les manigances annexes.
## Version 3.15
Ajout de Valkyrie
## Version 3.14
Entrave des manigances des héros
## Version 3.13b
Essai mise en oeuvre entrave
## Version 3.13
Prévision de Valkyrie (images), Entrave OK pour les 4 nouvelles boites
## Version 3.12
Ajout des extensions Nebula, War Machine et The Hood
## Versio 3.11
Préparation de la gestion de la capacité "Entrave" des manigances.
## Version 3.10
Ajout de l'Ombre du Titan Fou
Ajout du package "Fan Print" 1
## Version 3.9.1
Prévision de la boite "Titan Fou" (images des héros)
## Version 3.9
Ajout de Venom, prévision de Nebula (images)
## Version 3.8
Ajout de StarLord, Drax et Gamora
## Version 3.7
Aides de jeu
Gestion du conteneur en mode local
## Version 3.6
Gestion de la mise à jour indépendante (locale)  
Aide de jeu  
## Version 3.5
Première version complète de la documentation  
Création automatique de conteneur Docker
## Version 3.4
Mise en place du mode public (pour serveur disponible à plus de monde : exigence d'un mot de passe pour créer une partie)
## Version 3.3
Aménagement de la fiche joueur sur smartphone selon dernières fonctionnalités ajoutées  
Travail sur la documentation
## Version 3.2
Travail sur la documentation.
## Version 3.1
Ajout de l'option de sélection des decks à inclure dans une partie lors de sa création.
## Version 3.0a
Ajout des liens d'aide et bug report vers gitHub
## Version 3.0
Gestion des manigances annexes avec plusieurs icônes.
## Version 2.9
- Affichage de la dernière version gitHub dans l'écran d'administration,
- Correction d'un bug sur l'affichage des decks uniquement de boites sélectionnées lors de l'ajout de manigance annexe,
- Gestion des manigances principales sans multiplicateur (pour KANG par exemple).
## Version 2.8
Affichage dynamique des decks pour sélection d'une manigance secondaire.
## Version 2.7
Sélection des decks de manigance secondaire en fonction des héros présents.
## Version 2.6
Protection de l'administrtation du site par mot de passe.
## Version 2.5
- Script d'installation/Mise à jour nettoyé.
- Toutes les images de héros officiellement sortis à ce jour.
- Sélection du héros par les joueurs.
## Version 2.1
Début d'intégration des héros pour les joueurs (fonctionnel avec la boite de base).
## Version 2.0
La gestion des boites de jeu se trouve dans l'écran d'administration, permettant de simplifier les interfaces de sélection selon les éléments de jeu possédés.
Refonte de la page d'administration avec liste des parties stockées et possibilité de faire le ménage.
## Version 1.5
Préparation de la sélection des boites de jeu dans l'écran d'administration.
## Version 1.4
Gestion des mises à jour des images depuis gitHub
## Version 1.3
Gestion des mises à jour du code PHP depuis gitHub
## Version 1.2
Préparation de l'arrivée de la prise en charge des "boites de jeu"
1.1
## Version 1.1
Ajout des supressions dans les tables fixes depuis le script *setup.php*
## Version 1.01
Mecanisme de mise à jour automatique du script de mise à jour !
## Version 1.0
Premiére mise en ligne