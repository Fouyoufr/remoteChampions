# Ce dossier contient le nécessaire permettant de mettre à jour remoteChampions
- Le fichier **changelog** contient les descriptions des mises à jour (la première ligne référence la dernière version en cours)
- La variable **gitBranch** dans "config.inc" permet de gérer une branche de test sur le site.
- le fichiers **boxes.xml** des sous-répertoires de langues contiennent les informations sur les boites gérables par Remote Champions.

## Structure d'un fichier "boxes.xml":
```
<box id="X" name="Nom de la boite" type="b=base/extension, s=scénario,h=héros" own="0">
    <mechant id="X" name="Nom du méchant" vie1="Vie de la phase 1" vie2="Vie de la phase 2" vie3="Vie de la phase 3"/>
    <principale id="X" name="Nom de la manigance principale" init="Menace initiale" initX="0 ou 1 si init est multiplié par le nb de joueurs" max="Menace maximale" maxX="10 ou 1 si max est multiplié par le nb de joueurs"/>
    <heros id="X" name="Nom du héros" vie="Vie initiale">
      <scheme id="X" card="Numéro de carte de la manigance dans le deck" name="Nom de la manigance némésis" init="Menace initiale" initX="0 ou 1 si init est multiplié par le nb de joueurs" crise="0 ou 1 si icone crise" rencontre="0 ou 1 si icone rencontre" accel="0 ou 1 si icone accélératrion" ampli="0 ou 1 si icone amplification" entrave="0 ou 1 si entrave" revele="Texte à afficher lorsque la manigance est révélée" dejoue="Texte à afficher lorsque la manigance est déjouée." info="Texte à afficher concernant la manigance"/>
    </heros>
    <deck id="X" name="Nom d'un deck de la boite">
      <scheme id="X" card="Numéro de la carte dans le deck" name="Nom de la manigance annexe" init="Menace initiale" initX="0 ou 1 si init est multiplié par le nb de joueurs" crise="0 ou 1 si icone crise" rencontre="0 ou 1 si icone rencontre" accel="0 ou 1 si icone accélératrion" ampli="0 ou 1 si icone amplification" entrave="0 ou 1 si entrave" revele="Texte à afficher lorsque la manigance est révélée" dejoue="Texte à afficher lorsque la manigance est déjouée." info="Texte à afficher concernant la manigance"/>
     </deck>
  </box>
  ```

## Derniers ID utilisés:
Boite : 27
Méchant : 27
Héros : 31
Manigance : 165
Deck : 59


## Référence en ligne des cartes en VO
https://hallofheroeslcg.com/browse/

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