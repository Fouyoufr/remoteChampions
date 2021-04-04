<?php
$title='Marvel Champions - Admin';
$bodyClass='admin';
include 'include.php';
if (isset($partieId)) { echo "<div class='pannel'><div class='titleAdmin'>Mot-clef de la partie</div>$partieId</div>";}
?>


<div class="pannel">
  <div class="titleAdmin">Mise en page</div>
  Choisir l'apparence de l'écran de synthèse du jeu: 
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
    echo "><label for='boite$boiteId'style='display:inline-block;'><img src='/img/boites/$boiteId.png'/><br/>$boiteNom</label></div>";}
  
  
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
<div class="pannel">
<div class="titleAdmin">Mise à jour</div>
<button onclick="window.open('/setup.php','_self');">Lancer la mise à jour</button>
</div>
<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
  document.getElementById('ajaxLoad').style.display='none';
  document.getElementById('ajaxSave').style.display='none';
</script>
</body>