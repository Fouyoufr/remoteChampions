<?php
$title='Marvel Champions - Admin';
$bodyClass='admin';
include 'include.php';
?>
<div class="pannel">
  <div class="titleAdmin">Mot-clef de la partie</div>
<?php
echo $partieId;
?>
</div>

<div class="pannel">
  <div class="titleAdmin">Mise en page</div>
  Choisir l'apparence de l'écran de synthèse du jeu: 
  <Select id="selectCSS" onchange='localStorage.setItem("mcCss",this.value);'>
<?php
  foreach (glob("*.css") as $filename) {
    echo '<option>'.basename($filename,'.css');
    echo '</option>';
}
?>
</select>
</div>

<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
  ajaxCall(ajaxAdminSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)
  setInterval("ajaxCall(ajaxAdminSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)",2000); 
</script>
</body>