# Ce dossier contient le nécessaire permettant de mettre à jour remoteChampions
- Le fichier **changelog** contient les descriptions des mises à jour (la première ligne référence la dernière version en cours)
- Le fichier **sqlTables** est utilisé pour la déclaration de la structure de la base SQL de remoteChampions
- Les fichiers **boites,decks,manigances,ManigancesPrincipales,heros et mechants** contiennent le contenu prédéfini des tables SQL de meme nom.

## Structure de la table "manigancesPrincipales":
- mpId = identifiant de l'entrée,
- mpNom = libellé de la manigance,
- mpMax = Valeur de menace maximale (en haut à gauche sur la carte),
- mpMaxMultiplie = Si est à 1, *mpMax* est multiplié par le nombre de joueurs,
- mpInit = Valeur de menace lors de la mise en jeu,
- mpMultiplie = Si est à 1, *mpInit* est multiplié par le nombre de joueurs,
- mpBoite = référence de la boite de jeu contenant cette manigance.

## Structure de la table "manigances":
- maId = identifiant de l'entrée,
- maDeck = référence du deck contenant cette manigance (ou 0 pour les manigances de Héros),
- maNumero = numéro de la carte dans le deck (ou référence du héros),
- maNom = libellé de la manigance,
- maInit = Valeur de menace lors de la mise en jeu,
- maMultiplie = Si est à 1, *maInit* est multiplié par le nombre de joueurs,
- maCrise = Si est à 1, cette manigance comporte un symbole *crise*,
- maRencontre = Si est à 1, cette manigance comporte un symbole *rencontre*,
- maAcceleration = Si est à 1, cette manigance comporte un symbole *accélération*,
- maAmplification = Si est à 1, cette manigance comporte un symbole *amplification*.
- maEntrave = Nombre de menace par joueur ajoutée lors de la mise en jeu
- maRevele = Information affichée en popup lorsque manigance révélée ([pp]affiche le symbole "par joueur") (info dans maniganceInfo.php)
- maDejoue = Information affichée en popup à disparition de la manigance ([pp]affiche le symbole "par joueur") (info dans maniganceInfo.php)
- maInfo = Information affichée en popup concernant cette manigance ([pp]affiche le symbole "par joueur") (info dans maniganceInfo.php)

## Contenu de maniganceInfo.php
Un tableau de chaines de caractères dont l'index est utilisé dans les champs maRevele, maDejoue et maInfo

## Contenu de deckNames.php
Un tableau des decks :
 - l'index du tableau correspond au 'dId',
 - Le champ 'dNom' comtient le no du deck,
 - le champ 'dBoite' contient la référence de la boite depuis la base sql 'boites'

## Les types de boite
(valeurs pour le champ bType)
- b = boite de base et extensions
- s = paquets de scénario
- h = paquet héros

## Taille des images
- Héros = 50x50px
- Méchants = 50x50px
- Boites = Hauteur 100px

## Création de la nouvelle image Docker
- Installer Docker for Windows
- Reboot/installer WSL2 si nécessaire
- Dans l'invite de commande docker, se placer dans le dossier Git contenant le fichier DockerFile puis lancer :
    ```docker build -t fouyou/remotechampions . --no-cache```
- Se connecter à Docker Hub :
    ```docker login```
- Envoyer la nouvelle image sur le hub :
    ```docker push fouyou/remotechampions```

## Prochaines étapes envisagées :
- Indication des sbires engagés avec les héros ???
- Bypass de l'économiseur d'écran sur smartphone ?
- Application HTML installable ?
- Journal d'installation/mise à jour ?

A chercher : 'sql', 'deckNames' et 'maniganceInfo' (et 'maniTxt') ==> tester la mise à jour depuis 1.5 et depuis < 1.5