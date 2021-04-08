<?php
function sql_exists($query) {
  global $sqlConn;
  if (!isset($sqlConn)) sql_get('SHOW TABLES');
    $sqlQuery=mysqli_query($sqlConn,"$query LIMIT 1");
    if (mysqli_num_rows($sqlQuery)>0) return true; else return false;}

function displayBottom() {
  global $partieId,$adminPassword;
  $currentScript=basename($_SERVER['PHP_SELF']);
  if ($currentScript<>'admin.php' and $currentScript<>'setup.php') {
    echo "<form action='admin.php' method='post' id='dispClef' onclick='";
    if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) echo "moDePass=prompt(\"Mot de passe administratif\");if(moDePass===null) return; else {getElementById(\"adminPassword\").value=moDePass;this.submit();}"; else echo "this.submit();";
    echo "'><input type='hidden' name='adminPassword' id='adminPassword'>";
    if (isset($partieId)) echo "Le mot-clef de cette partie est <span>$partieId</span></form>"; else echo "Administration du site";
    echo "</form>";}
  echo "<a href='https://github.com/Fouyoufr/remoteChampions/blob/main/doc/readme.md#utilisation-de-remote-champions' alt='Utilisation de Remote Champions' id='aide' target='_blank'><img src='img/aide.png' alt='Utilisation de Remote Champions'/></a>";
  echo "<a href='https://github.com/Fouyoufr/remoteChampions/issues' alt='Rapporter un problème' id='bugReport' target='_blank'><img src='img/bug.png' alt='Rapporter un problème'/></a>";}
function updatePassword() {
  global $adminPassword;
  $configFile = file('config.inc');
  function replace_a_line($data) {
    global $adminPassword;
     if (stristr($data,'$adminPassword=')) return "\$adminPassword='$adminPassword';\n";
     return $data;}
  $configFile=array_map('replace_a_line',$configFile);
  file_put_contents('config.inc', implode('',$configFile));
  $_SESSION['adminPassword']=$adminPassword;}
if (!file_exists('./config.inc')) {header("Refresh:0; url=setup.php");}
else {include 'config.inc';}
session_start();
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {$mobile=true;} else {$mobile=false;}
if (isset($_GET['p'])) $partieId=htmlspecialchars($_GET['p']); elseif (isset($_POST['p'])) $partieId=htmlspecialchars($_POST['p']);
if (isset($_GET['j'])) $joueurId=htmlspecialchars($_GET['j']); elseif (isset($_POST['j'])) $joueurId=htmlspecialchars($_POST['j']);
if (isset($partieId)) {if (mysqli_num_rows(sql_get("SELECT `pUri` FROM `parties` WHERE `pUri`='$partieId'"))!=1) {
  $badPartie=$partieId;
  unset($partieId);}}
if (isset($joueurId)) {
	if (mysqli_num_rows(sql_get("SELECT `jId` FROM `joueurs` WHERE `jId`='$joueurId'"))!=1) {unset($joueurId);}
	else {
		$joueur=sql_get("SELECT `pUri`FROM `joueurs`,`parties` WHERE `jId`='$joueurId' AND `jPartie`=`pUri`");
		if (mysqli_num_rows($joueur)!=1) {exit ('Le joueur selectionné n\'est pas dans une partie en cours !');}
		else {$partieId=mysqli_fetch_assoc($joueur)['pUri'];}}}
if (isset($title)) {
  echo "<!doctype html>
<html lang='fr'>
<head>
  <META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>
  <META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>
  <meta charset='UTF-8'>
  <script type='text/javascript' src='mc.js'></script>
  <link rel='stylesheet' href='ecran.css'>
  <link rel='icon' href='favicon.ico'/>
  <title>$title</title>
  <script language='JavaScript'>
    var css=localStorage.getItem('mcCss');
    if (css!=null) {
      document.querySelector(\"link[href='ecran.css']\").href=css+'.css';}
  </script>
</head>
<body";
  if (isset($bodyClass)) echo " class='$bodyClass'";
  echo ">";
  if (isset($partieId)) {
    echo "<div id='ajaxLoad'";
    if (isset($bodyClass)) echo " class='$bodyClass'";
    echo "></div>
<div id='ajaxSave'";
    if (isset($bodyClass)) echo " class='$bodyClass'";
    echo "></div>
<input id='partie' type='hidden' value='$partieId'/>
";}
if (isset($joueurId)) echo "<input id='jId' type='hidden' value='$joueurId'/>
";}
?>