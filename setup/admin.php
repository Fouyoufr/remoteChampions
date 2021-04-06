<?php
$title='Remote Champions - Admin';
$bodyClass='admin';
include 'include.php';
#Vérification du mot de passe d'administration.
if (isset($_POST['newPass'])) {
  $adminPassword=hash('sha256',$_POST['newPass']);
  updatePassword();}
if (isset($_POST['adminPassword'])) $_SESSION['adminPassword']=hash('sha256',$_POST['adminPassword']);
if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) exit("<div class='pannel'><div class='titleAdmin'>Accès restreint</div>Désolé, l'accès à cette partie du site est protégé par un mot de passe...<br/><a class='adminButton' href='.'>Retour au site</a></div>");
if (isset($_GET['del'])) {
  $partie=sql_get("SELECT `pUri` FROM `parties` WHERE `pUri`='".$_GET['del']."'");
  if (mysqli_num_rows($partie)<>0) {
    sql_get("DELETE FROM `compteurs` WHERE `cPartie`='".$_GET['del']."'");
    sql_get("DELETE FROM `joueurs` WHERE `jPartie`='".$_GET['del']."'");
    sql_get("DELETE FROM `maniAnnexes` WHERE `mnPartie`='".$_GET['del']."'");
    sql_get("DELETE FROM `parties` WHERE `puri`='".$_GET['del']."'");}}
?>


<div class="pannel">
  <div class="titleAdmin">Mise en page</div>
  Choisir l'apparence de l'écran de synthèse du jeu (en travaux): 
  <Select id="selectCSS" onchange='localStorage.setItem("mcCss",this.value);'>
<?php
  foreach (glob("*.css") as $filename) {
    echo '<option>'.basename($filename,'.css');
    echo '</option>';}
?>
</select>
</div>
<div class="pannel">
  <div class="titleAdmin">Activation des boites de jeu</div>
  (paramètre global du serveur)<br/>
  <?php
  function displayBox($boite) {
    $boiteId=$boite['bId'];
    $boiteNom=$boite['bNom'];
    echo "<div class='adminEncadre'><input type='checkbox' id='boite$boiteId' onclick='ajaxPost(\"boite=$boiteId&inclus\",document.getElementById(\"boite$boiteId\").checked);'";
    if ($boite['bInclus']=='1') {echo ' checked ';}
    if ($boite['bInclus']=='2') {echo ' checked disabled';}
    echo "><label for='boite$boiteId'style='display:inline-block;'><img src='img/boites/$boiteId.png'/><br/>$boiteNom</label></div>";}
    function displayBoxes($dbReq) {
    $boites=sql_get("SELECT * FROM `boites` WHERE $dbReq ORDER BY `bNom`");
    while ($boite=mysqli_fetch_assoc($boites)) {
      displayBox($boite);}}
  displayBox(array('bId'=>'1','bNom'=>'Boite de base','bInclus'=>'2'));
  displayBoxes("bType='b' AND bId <>'1'");
  echo '<hr/>';
  displayBoxes("bType='s'");
  echo '<hr/>';
  displayBoxes("bType='h'");
  ?>
</div>
<?php
$partiesMechant=sql_get("SELECT `pUri`,`pDate`,`mNom`,`mId` FROM `parties`,`mechants` WHERE `mId`=`pMechant` ORDER BY `pDate`DESC");
if (mysqli_num_rows($partiesMechant)<>0) {
  echo "<div class='pannel'><div class='titleAdmin'>Liste des parties sur le serveur</div><table style='text-align:left'><tr><th>Clef d'accès</th><th>Méchant</th><th>Joueurs</th><th>Créée le</th><th></th></tr>";
  $partiesJoueur=array();
  $partiesSQL=sql_get("SELECT `pUri`,`pDate`,COUNT(`jId`),`mNom`,`mId` FROM `parties`,`mechants`,`joueurs` WHERE `jPartie`=`pUri` AND `mId`=`pMechant` GROUP BY `jPartie` ORDER BY `pDate` DESC");
  while ($partie=mysqli_fetch_assoc($partiesSQL)) {$partiesJoueur[$partie['pUri']]=$partie;}
  while ($partie=mysqli_fetch_assoc($partiesMechant)) {
    echo '<tr><td>'.$partie['pUri'].'</td><td>';
    if ($partie['mId']<>0) {echo $partie['mNom'];} else {echo 'aucun';}
    echo '</td><td>';
    if (isset($partiesJoueur[$partie['pUri']])) {echo $partiesJoueur[$partie['pUri']]['COUNT(`jId`)']; }
    else {echo 'aucun';}
    echo '</td><td>le '.date('d/m/Y à H:i',strtotime($partie['pDate'])).'</td><td><a href=".?p='.$partie['pUri'].'">Ouvrir</a> / <a href="?del='.$partie['pUri'].'" onclick="return confirm(\'Cette action est irréversible.\nEtes-vous certain(e) de souhaiter détruire les informations de la partie '.$partie['pUri'].'?\')">Supprimer</a></td></tr>';
  }
  echo "</table></div>";}
?>
<form class="pannel" style="text-align:left;" id='newPassForm' method='post' action=''>
<div class="titleAdmin">Mot de passe administratif</div>
<?php
if (isset($_POST['newPass'])) echo "<div style='width:100%;font-size:2em;text-align:center;background-color:red;color:white;'>Le mot de passe administratif a été modifié.</div>"
?>
<span style="color:red;text-decoration:underline">Attention:</span> Le mot de passe modifié ici n'est pas stocké en clair sur le serveur.<br/>
(Veillez à le conserver en lieu sur et à ne pas le perdre...)<br/>
<div style="float:left;margin-right:10px;">Nouveau mot de passe<br/>Vérification<br/> </div>
<div>
<input type='password' name='newPass' id='newPass'><br/>
<input type='password' name='newPass2' id='newPass2'>
<input type='submit' onclick="if (document.getElementById('newPass').value == document.getElementById('newPass2').value) return true; else {alert('Le mot de passe saisi et la vérification ne correspondent pas!');return false;}">
</div>
</form>
<div class="pannel">
<div class="titleAdmin">Mise à jour</div>
<?php

#Récupération des informations du repositery par les API gitHub (le $context permet de psser un userAgent à file_get_contents, requis par gitHub)
$context = stream_context_create(array('http'=>array('method'=>"GET",'header'=>"Accept-language: en\r\n"."Cookie: foo=bar\r\n"."User-Agent: Fouyoufr")));
$lastCommit=json_decode(file_get_contents('https://api.github.com/repos/Fouyoufr/remoteChampions/commits?per_page=1',false,$context),true)[0]['commit'];
$version=strtok($lastCommit['message'],"\n");
$comments=nl2br(ltrim(strstr($lastCommit['message'],"\n"),"\n"));
echo "<a class='adminEncadre' href='https://github.com/Fouyoufr/remoteChampions/blob/main/README.md#historique-des-changements' target='_blank'>Dernière mise à jour gitHub : version $version, il y a ".date_diff(new DateTime($lastCommit['committer']['date']),new DateTime())->format('%m mois,%a jours, %h heures et %i minutes').":<br/>$comments</a><br/>";

?>
<a href="setup.php" class="adminButton">Lancer la mise à jour</a>
</div>
<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
</script>
</body>