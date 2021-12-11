<?php
$title='Remote Champions - Admin';
$bodyClass='admin';
include 'include.php';
global $str;
if (isset($_POST['newPass']) and $_POST['newPass']<>'') {
  $adminPassword=hash('sha256',$_POST['newPass']);
  updatePassword();}
#Vérification du mot de passe d'administration.
if (isset($_POST['adminPassword']) and !empty($_POST['adminPassword'])) $_SESSION['adminPassword']=hash('sha256',$_POST['adminPassword']);
if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) {
  echo("<div class='pannel'><div class='titleAdmin'>".$str['restrictedTitle']."</div>".$str['restricted']."<br/><a class='adminButton' href='.'>".$str['restrictedBack']."</a></div>");
  displayBottom();
  exit('</body>');}

if (isset($_POST['publicMode'])) {
  #Activation/désactivation du mode public
  if ($_POST['publicMode']=='on') $publicPass=hash('sha256',$_POST['newPublic']); else $publicPass='';
  $configFile = file('config.inc');
  function replace_a_line($data) {
    global $publicPass;
     if (stristr($data,'$publicPass=')) return "\$publicPass='$publicPass';\n";
     return $data;}
  $configFile=array_map('replace_a_line',$configFile);
  file_put_contents('config.inc', implode('',$configFile));}

if (isset($_GET['del'])) {unlink('ajax/'.$_GET['del'].'.xml');}
?>

<!--
  <div class="pannel">
  <div class="titleAdmin">
    <?php
    #echo '<div class="titleAdmin">'.$str['Theme'].$str['ThemeSelect'].' '.$str['wip'].':</div><Select id="selectCSS" onchange=\'localStorage.setItem("mcCss",this.value);\'>';
  #foreach (glob("*.css") as $filename) {
    #echo '<option>'.basename($filename,'.css');
    #echo '</option>';}
?>
</select>
</div>
  -->
  
<div class="pannel">
  <?php
  echo '<div class="titleAdmin">'.$str['boxesTitle'].'</div>('.$str['globalParam'].')<br/>';
  function displayBox($boite) {
    $boiteId=$boite['bId'];
    $boiteNom=$boite['bNom'];
    echo "<div class='adminEncadre'><input type='checkbox' id='boite$boiteId' onclick='ajaxPost(\"boite=$boiteId&inclus\",document.getElementById(\"boite$boiteId\").checked);'";
    if ($boite['bInclus']=='1') {echo ' checked ';}
    if ($boite['bInclus']=='2') {echo ' checked disabled';}
    echo "><label for='boite$boiteId'><img src='img/boites/$boiteId.png'/><br/>$boiteNom</label></div>";}
    function displayBoxes($dbReq) {
    $boites=sql_get("SELECT * FROM `boites` WHERE $dbReq ORDER BY `bNom`");
    if ($boites) while ($boite=mysqli_fetch_assoc($boites)) {
      displayBox($boite);}}
  displayBox(array('bId'=>'1','bNom'=>$str['baseBox'],'bInclus'=>'2'));
  displayBoxes("bType='b' AND bId <>'1'");
  echo '<hr/>';
  displayBoxes("bType='s'");
  echo '<hr/>';
  displayBoxes("bType='h'");
  ?>
</div>
<?php
$gameList=glob('ajax/*.xml');
if ($gameList) {
  echo "<div class='pannel'><div class='titleAdmin'>".$str['gamesList']."</div><table><tr><th>".$str['gamePassAdmin']."</th><th>".$str['villain']."</th><th>".$str['players']."</th><th>".$str['gameDate']."</th><th></th></tr>";
  foreach($gameList as $game) {
    $xml=simplexml_load_file($game);
    echo '<tr><td>'.$xml['pUri'].'</td><td>';
    if ($xml['pMechant']==0) echo 'Aucun'; else echo $xml['mNom'];
    if ($xml->joueur->count()>0) echo '</td><td>'.$xml->joueur->count();  else echo '</td><td>'.$str['noPlayer'];
    echo '</td><td>le '.date('d/m/Y H:i',$xml['pDate']->__toString()).'</td><td class="adminIcones"><a href=".?p='.$xml['pUri'].'"><img src="img/link.png" alt="'.$str['adminOpenGame'].'"/></a> / <a href="?del='.$xml['pUri'].'" onclick="return confirm(\''.$str['deleteConfirm'].' '.$xml['pUri'].' ?\')"><img src="img/trash.png" alt="'.$str['adminDelete'].'"/></a></td></tr>';}
  echo "</table></div>";}

echo "<form class='pannel' id='newPassForm' method='post' action=''><div class='titleAdmin'>".$str['adminPwd'].'</div>';
if (isset($_POST['newPass'])) echo "<div class='redMessage'>".$str['adminPassChanged']."</div>";
echo '<span class="redUnderline">'.$str['warning'].':</span>'.$str['passwordNotStored'];
echo '<hr/><div class="publicStatus">'.$str['newAdminPass'].'<br/>'.$str['checkAdminPass'].'<br/> </div><div><input type=\'password\' name=\'newPass\' id=\'newPass\'><br/><input type=\'password\' name=\'newPass2\' id=\'newPass2\'><input type=\'submit\' onclick="if (document.getElementById(\'newPass\').value == document.getElementById(\'newPass2\').value) return true; else {alert(\''.$str['adminPassNotMatch'].'\');return false;}"></div></form>
<form class="pannel" id=\'publicModeForm\' method=\'post\' action=\'\'>
<div class="titleAdmin">Mode public</div>';
if (isset($_POST['publicMode'])) echo "<div class='redMessage'>".$dtr['adminSaved']."</div>";
echo '<span class="publicWarning">'.$str['warning'].':</span>'.$str['adminPublicHelp'].'<hr/><div class="publicStatus">'.$str['adminPublicStatus'].'<br/>'.$str['newAdminPass'].'<br/>'.$str['checkAdminPass'].'</div><div>';
echo "<input type='radio' name='publicMode' value='off' id='publicModeOff' onclick='document.getElementById(\"newPublic\").disabled=true;document.getElementById(\"newPublic2\").disabled=true;document.getElementById(\"newPublic\").value=\"\";document.getElementById(\"newPublic2\").value=\"\";'";
if (!isset($publicPass) or $publicPass=='') echo ' checked';
echo ">".$str['disabled']." / <input type='radio' name='publicMode' value='on' onclick='document.getElementById(\"newPublic\").disabled=false;document.getElementById(\"newPublic2\").disabled=false;'";
if ($publicPass!='') echo ' checked';
echo ">".$str['enabled']."<br/><input type='password' name='newPublic' id='newPublic'";
if (!isset($publicPass) or $publicPass=='') echo ' disabled';
echo "><br/><input type='password' name='newPublic2' id='newPublic2'";
if (!isset($publicPass) or $publicPass=='') echo ' disabled';
echo '><input type=\'submit\' onclick="if (document.getElementById(\'publicModeOff\').checked) return true; else if(document.getElementById(\'newPublic\').value.length<6) {alert(\''.$str['adminPublicPass6char'].'\');return false;} else if (document.getElementById(\'newPublic\').value == document.getElementById(\'newPublic2\').value) return true; else {alert(\''.$str['adminPassNotMatch'].'\');return false;}"></div></form>';
echo '<form class="pannel" class="miseAJour" action="setup.php" method="post" enctype="multipart/form-data"><div class="titleAdmin">'.$str['adminUpdate'].'</div>';

$gitCommit=gitFileDate();
if (isset($gitCommit['erreur'])) echo "<div class='error'>".$str['gitHubError']."....<div class='subError'>".$gitCommit['erreur']."</div></div>"; else echo "<a class='adminEncadre' href='https://github.com/Fouyoufr/remoteChampions/blob/main/README.md#historique-des-changements' target='_blank'>".$str['gitHubVersion']." ".$gitCommit['version'].", ".$str['adminAgo']." ".date_diff($gitCommit['date'],new DateTime())->format('%m '.$str['months'].',%a '.$str['jours'].', %h '.$str['hours'].' et %i '.$str['minutes']).":<br/>".$gitCommit['comments']."</a><br/><br/>";

echo "<div>".$str['adminUpdate']." : ".$str['onlineUpdate']." <input type='radio' name='autoUpdate' value='oui' checked onclick='if (this.checked) document.getElementById(\"zipUpdate\").disabled=true;'>/ ".$str['zipUpdate'].": <input type='radio' name='autoUpdate' value='non' onclick='if (this.checked) document.getElementById(\"zipUpdate\").disabled=false;'> <input type='file' name='zipUpdate' id='zipUpdate'  accept='.zip' disabled></div>\n<input type='submit' class='adminButton' value='".$str['updateLaunch']."'>\n</form>";
displayBottom();
?>
<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
</script>
</body>;