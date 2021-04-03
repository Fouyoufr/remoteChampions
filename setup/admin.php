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
  $boites=sql_get("SELECT * FROM `boites` ORDER BY `bNom`");
  while ($boite=mysqli_fetch_assoc($boites)) {
    if ($boite['bId']<>0) {
      $boiteId=$boite['bId'];
      $boiteNom=$boite['bNom'];
      echo "<input type='checkbox' id='boite$boiteId' onclick='ajaxPost(\"boite=$boiteId&inclus\",document.getElementById(\"boite$boiteId\").checked);'";
      if ($boite['bInclus']=='1') {echo ' checked ';}
      echo "><label for='boite$boiteId'>$boiteNom</label><br/>";}}
  ?>
</div>
<div class="pannel">
<div class="titleAdmin">Mise à jour</div>
<?php
if (isset($partieId)) { echo "(Cette action quittera la partie en cours)<br/>";}
?>
<button onclick="if(window.opener){window.opener.open('/setup.php');} else {window.open('/setup.php','_self');}">Lancer la mise à jour</button>
</div>
<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
  ajaxCall(ajaxAdminSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)
  setInterval("ajaxCall(ajaxAdminSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)",2000); 
</script>
</body>