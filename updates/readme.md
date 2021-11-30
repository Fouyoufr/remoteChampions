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
- maRevele = Information affichée en popup lorsque manigance révélée
- maDejoue = Information affichée en popup à disparition de la manigance
- maInfo = Information affichée en popup concernant cette manigance

## Les types de boite
(valeurs pour le champ bType)
- b = boite de base et extensions
- s = paquets de scénario
- h = paquet héros

## Taille des images
- Héros = 50x50px
- Méchants = 50x50px
- Boites = Hauteur 100px

## Evolution(s) en cours
- Création d'une fonction d'information sur les informations des manigances annexes (popup auto sur "une fois révélé").

## Prochaines étapes envisagées :
- Ajouter la capacité "Entrave" de toutes les manigances annexes
- Popup auto pour afficher les "une fois déjoué" des manigances annexes.
- Indication des sbires engagés avec les héros ???