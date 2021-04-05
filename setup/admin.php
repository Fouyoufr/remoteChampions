<?php
$title='Remote Champions - Admin';
$bodyClass='admin';
include 'include.php';
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
    echo "<div style='display:inline-block;border:2px solid black;padding:3px;margin:2px;'><input type='checkbox' id='boite$boiteId' onclick='ajaxPost(\"boite=$boiteId&inclus\",document.getElementById(\"boite$boiteId\").checked);'";
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
  echo "</table></div>";
}
?>
<div class="pannel">
<div class="titleAdmin">Mise à jour</div>
<button onclick="window.open('setup.php','_self');">Lancer la mise à jour</button>
</div>
<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
  document.getElementById('ajaxLoad').style.display='none';
  document.getElementById('ajaxSave').style.display='none';
</script>
</body>