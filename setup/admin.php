<?php
function restrictAccess(){
  global $str,$adminPassword;
  if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) {
    echo("<div class='pannel'><div class='titleAdmin'>".$str['restrictedTitle']."</div>".$str['restricted']."<br/><a class='adminButton' href='.'>".$str['restrictedBack']."</a></div>");
    displayBottom();
    exit('</body>');}}

function restoreXML($restoreFileName){
  global $str,$restored,$xmlBoxes,$boxFile;
  $changedBoxes=false;
  $xml=simplexml_load_file($restoreFileName);
  if (isset($xml['pUri'])) {
    if (!is_dir('ajax')) mkdir('ajax'); 
    rename($restoreFileName, 'ajax/'.$xml['pUri'].'.xml');
    $restored.="<div class='subError'>".$str['restoredGame']." : '".$xml['pUri']."'.</div>";
    #La boite contenant le Deck est-elle notée comme incluse ?
    foreach($xml->deck as $deck) if ($deck['dId']<>0) foreach ($xmlBoxes as $xmlBox) foreach ($xmlBox->deck as $ownDeck) if ($ownDeck['id']->__toString()==$deck['dId']->__toString() and $xmlBox['own']<>1) {
      $xmlBox['own']=1;
      $changedBoxes=true;
      $restored.="<div class='subError'>(".$str['boxAdd'].' \''.$xmlBox['name']."'.)</div>";}
    #La boite contenant le Héros est-elle notée comme incluse ?
    foreach($xml->joueur as $joueur) if ($joueur['jHeros']<>0) foreach ($xmlBoxes as $xmlBox) foreach ($xmlBox->heros as $ownHeros) if ($ownHeros['id']->__toString()==$joueur['jHeros']->__toString() and $xmlBox['own']<>1) {
      $xmlBox['own']=1;
      $changedBoxes=true;
      $restored.="<div class='subError'>(".$str['boxAdd'].' \''.$xmlBox['name']."'.)</div>";}
    if ($changedBoxes) xmlSave($xmlBoxes,$boxFile);}
  else {
    unlink($restoreFileName);
    $restored.="<div class='subError'>".$str['xmlError'].' : '.$restoreFileName.', '.$str['xmlError2'].'</div>';}}

$restored='';
if (isset($_FILES['zipRestore'])) {
  include_once 'functions.inc';
  include_once 'config.inc';
  session_start();
  global $str;
  restrictAccess();
  if (!file_exists('updates')) {if(!mkdir('updates',0777,true)) exit("<div class='error'>".$str['noUpdatesDir']."...</div>");}
  $target_file='updates/'.basename($_FILES['zipRestore']['name']);
  if(strtolower(pathinfo($target_file,PATHINFO_EXTENSION))!='zip' and strtolower(pathinfo($target_file,PATHINFO_EXTENSION))!='xml') $restored="<div class='error'>".$str['incorrectFile'].".<div class='subError'>".$str['noZipXML'].".<br/>".$str['readDoc']."...</div></div>";
  else {
    if(strtolower(pathinfo($target_file,PATHINFO_EXTENSION))=='zip') {
      #Restauration depuis ZIP sauvegardé
      $zip = new ZipArchive;
      if ($zip->open($_FILES['zipRestore']['tmp_name']) === TRUE) {
        if (substr($zip->getNameIndex(0),0,5)<>'ajax/') exit("<div class='error'>".$str['nozip4']."</div>");
        for($i=0;$i<$zip->numFiles;$i++) {
          $filename=$zip->getNameIndex($i);
          $fileinfo=pathinfo($filename);
          $tempFile='updates/'.$fileinfo['basename'];
          copy('zip://'.$_FILES['zipRestore']['tmp_name'].'#'.$filename,$tempFile);
          restoreXML($tempFile);}          
        $zip->close();
        $restored="<div class='redMessage'>".$str['restored']." : '".$_FILES['zipRestore']['name']."'.$restored</div>";}
      else $restored="<div class='error'>".$str['nozip2'].".<div class='subError'>".error_get_last()['message']."</div></div>";
      unlink($_FILES['zipRestore']['tmp_name']);}
    else {
      #Import d'un fichier XML unitaire
      $newGameFile='updates/'.$_FILES['zipRestore']['name'];
      if (move_uploaded_file($_FILES['zipRestore']['tmp_name'],$newGameFile)) {
        restoreXML($newGameFile);
        $restored="<div class='redMessage'>$restored</div>";}}}}

if (isset($_GET['save'])) {
  include 'include.inc';
  global $str;
  restrictAccess();
    $zip=new ZipArchive();
    if ($zip->open('ajax/save.zip',ziparchive::CREATE)!==TRUE) {
      echo("<div class='pannel'><div class='titleAdmin'>".$str['error']."</div>".$str['zipError'].".<br/><a class='adminButton' href='.'>".$str['restrictedBack']."</a></div>");
      displayBottom();
      exit('</body>');}
    $gameList=glob('ajax/*.xml');
    foreach($gameList as $game)  $zip->addFile($game);
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-Length: '.filesize('ajax/save.zip'));
    header('Content-Disposition: attachment;filename="remoteChampion-'.date('Y-m-d').'.zip"');
    readfile('ajax/save.zip');
 exit();}

$title='Remote Champions - Admin';
$bodyClass='admin';
include_once 'include.inc';
$xmlBoxes=simplexml_load_file($boxFile);
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
  
<?php
  echo "<form class='pannel' id='newPassForm' method='post' action=''><div class='titleAdmin'>".$str['adminPwd'].'</div>';
  if (isset($_POST['newPass'])) echo "<div class='redMessage'>".$str['adminPassChanged']."</div>";
  if ($_SESSION['adminPassword']=='8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918') echo "<div class='redMessage'>".$str['defaultAdminPass']."</div>";
  echo '<span class="redUnderline">'.$str['warning'].':</span>'.$str['passwordNotStored'].'<hr/><div class="publicStatus">'.$str['newAdminPass'].'<br/>'.$str['checkAdminPass'].'<br/> </div><div><input type=\'password\' name=\'newPass\' id=\'newPass\'><br/><input type=\'password\' name=\'newPass2\' id=\'newPass2\'><input type=\'submit\' onclick="if (document.getElementById(\'newPass\').value == document.getElementById(\'newPass2\').value) return true; else {alert(\''.$str['adminPassNotMatch'].'\');return false;}"></div></form>';

  echo '<div class="pannel"><div class="titleAdmin">'.$str['boxesTitle'].'</div>('.$str['globalParam'].')<br/>';
  function displayBoxes($boxType) {
    global $xmlBoxes;
    foreach ($xmlBoxes as $xmlBox) if ($xmlBox['id']<>1 and $xmlBox['type']==$boxType) {
      echo "<div class='adminEncadre'><input type='checkbox' id='boite".$xmlBox['id']."' onclick='ajaxPost(\"boite=".$xmlBox['id']."&inclus\",document.getElementById(\"boite".$xmlBox['id']."\").checked);'";
      if ($xmlBox['own']==1) echo ' checked ';
      echo "><label for='boite".$xmlBox['id']."'><img src='img/boites/".$xmlBox['id'].".png'/><br/>".$xmlBox['name']."</label></div>";}}
  echo "<div class='adminEncadre'><input type='checkbox' id='boite1' checked disabled><label for='boite1'><img src='img/boites/1.png'/><br/>Base</label></div>";
  displayBoxes('b');
  echo '<hr/>';
  displayBoxes('s');
  echo '<hr/>';
  displayBoxes('h');
  ?>
</div>
<?php
$gameList=glob('ajax/*.xml');
if ($gameList) {
  echo "<div class='pannel'><div class='titleAdmin'>".$str['gamesList']."</div><table><tr><th>".$str['gamePassAdmin']."</th><th>".$str['villain']."</th><th>".$str['players']."</th><th>".$str['gameDate']."</th><th></th></tr>";
  $gamesList=array();
  foreach($gameList as $game) {
    $xml=simplexml_load_file($game);
    $gameLine='<tr><td>'.$xml['pUri'].'</td><td>';
    if ($xml['pMechant']==0) $gameLine.='Aucun'; else $gameLine.=$xml['mNom'];
    if ($xml->joueur->count()>0) $gameLine.='</td><td>'.$xml->joueur->count();  else $gameLine.='</td><td>'.$str['noPlayer'];
    $gameLine.='</td><td>le '.date('d/m/Y H:i',$xml['pDate']->__toString()).'</td><td class="adminIcones"><a href=".?p='.$xml['pUri'].'"><img src="img/link.png" alt="'.$str['adminOpenGame'].'"/></a> / <a href="?del='.$xml['pUri'].'" onclick="return confirm(\''.$str['deleteConfirm'].' '.$xml['pUri'].' ?\')"><img src="img/trash.png" alt="'.$str['adminDelete'].'"/></a> / <a href="ajax/'.$xml['pUri'].'.xml" download><img src="img/saveB.png" alt="'.$str['save'].'"/></a></td></tr>';
    $gamesList[]=array('date'=>$xml['pDate']->__toString(),'gameLine'=>$gameLine);}
  usort($gamesList,create_function('$a,$b','return $b[\'date\']-$a[\'date\'];'));
  foreach ($gamesList as $gameLine) echo $gameLine['gameLine'];
  echo "</table></div>";}

echo '<form class=\'pannel\' id=\'adminSaveDiv\' action=\'?\' method=\'post\' enctype=\'multipart/form-data\'><div class=\'titleAdmin\'>'.$str['saveRestore'].'</div>'.$restored.$str['saveText'].' <a href="?save" download>'.$str['saveText2'].'</a><br/>('.$str['saveText3'].' <img src=\'img/saveB.png\' alt=\''.$str['save'].'\'/>'.$str['saveText4'].'.)<hr/>'.$str['zipRestore'].' : <input type=\'file\' name=\'zipRestore\' id=\'zipRestore\' accept=\'.zip,.xml\'><input type=\'image\' src=\'img/load.png\'></form>';

echo '<form class="pannel" id=\'publicModeForm\' method=\'post\' action=\'\'>
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

echo '<form class="pannel" class="miseAJour" action="setup.php" method="post" enctype="multipart/form-data" id="setupForm"><div class="titleAdmin">'.$str['adminUpdate'].'</div>';
$gitCommit=gitFileDate();
if (isset($gitCommit['erreur'])) echo "<div class='error'>".$str['gitHubError']."....<div class='subError'>".$gitCommit['erreur']."</div></div>"; else {
  echo "Version locale : $version<br/><a class='adminEncadre' href='https://github.com/Fouyoufr/remoteChampions/blob/main/README.md#historique-des-changements' target='_blank'>".$str['gitHubVersion']." ".$gitCommit['version'].", ".$str['adminAgo']." ";
$gitAgo=date_diff($gitCommit['date'],new DateTime());  
if ($gitAgo->m>0) echo $gitAgo->m.' '.$str['months'].', ';
if (explode(',',$gitAgo->format('%m,%d'))[1]>0) echo explode(',',$gitAgo->format('%m,%d'))[1].' '.$str['days'].', ';
if (explode(',',$gitAgo->format('%m,%d,%h'))[2]>0) echo explode(',',$gitAgo->format('%m,%d,%h'))[2].' '.$str['hours'].', ';
echo explode(',',$gitAgo->format('%m,%d,%h,%i'))[3].' '.$str['minutes'].' :<br/>'.$gitCommit['comments']."</a><br/><br/>";}
echo "<div>".$str['adminUpdate']." : ".$str['onlineUpdate']." <input type='radio' name='autoUpdate' value='oui' checked onclick='if (this.checked) document.getElementById(\"zipUpdate\").disabled=true;'>/ ".$str['zipUpdate'].": <input type='radio' name='autoUpdate' value='non' onclick='if (this.checked) document.getElementById(\"zipUpdate\").disabled=false;'> <input type='file' name='zipUpdate' id='zipUpdate'  accept='.zip' disabled></div>\n<input type='submit' form='setupForm' class='adminButton' value='".$str['updateLaunch']."'>\n</form>";
displayBottom();
?>
<script language="JavaScript">
  var css=localStorage.getItem('mcCss');
  if (css!=null) {document.getElementById('selectCSS').value=css;}
</script>
</body>;