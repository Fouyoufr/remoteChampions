<?php
include_once('lang-fr.php');

function xmlDoc($xdObj,$xdAdd) {
  global $xml;
  foreach ($xdAdd as $xdKey=>$xdValue) {
    if (is_array($xdValue)) {
      $xdSubObj=$xdObj->appendChild($xml->createElement($xdKey));
      xmlDoc($xdSubObj,$xdValue);}
    else {
      $xdAttr=$xml->createAttribute($xdKey);
      $xdAttr->appendChild($xml->createTextNode($xdValue));
      $xdObj->appendChild($xdAttr);}}
    return $xdObj;}

function deckNames() {
  $sqlDeckNames=sql_get("SELECT `dId`,`dNom` FROM `boites`,`decks` WHERE `dBoite`=`bId` AND `bInclus`='1' ORDER BY `dNom`");
  $deckNames=[];
  while ($sqlDeckName=mysqli_fetch_assoc($sqlDeckNames)) {$deckNames[$sqlDeckName['dId']]=$sqlDeckName['dNom'];}
  return $deckNames;}

function gitFileDate($gitFile=null){
  global $str;
  #Récupération des informations du repositery par les API gitHub (le $context permet de passer un userAgent à file_get_contents, requis par gitHub)
  $context = stream_context_create(array('http'=>array('method'=>"GET",'header'=>"Accept-language: en\r\n"."Cookie: foo=bar\r\n"."User-Agent: Fouyoufr")));
  $gitReq='https://api.github.com/repos/fouyoufr/remotechampions/commits?per_page=1';
  if (isset($gitFile) and $gitFile!='') $gitReq.='&path='.$gitFile;
  $jsonContent=@file_get_contents($gitReq,false,$context);
  if($jsonContent === FALSE) { return array('erreur'=>error_get_last()['message']);}
  $lastCommit=json_decode($jsonContent,true)[0]['commit'];
  return array('version'=>strtok($lastCommit['message'],"\n"),'comments'=>nl2br(ltrim(strstr($lastCommit['message'],"\n"),"\n")),'date'=>new DateTime($lastCommit['committer']['date']));}

function sql_exists($query) {
  global $sqlConn,$str;
  if (!isset($sqlConn)) sql_get('SHOW TABLES');
    $sqlQuery=mysqli_query($sqlConn,"$query LIMIT 1");
    if (mysqli_num_rows($sqlQuery)>0) return true; else return false;}

function displayBottom() {
  global $partieId,$adminPassword,$mobile,$str;
  $currentScript=basename($_SERVER['PHP_SELF']);
  if (!$mobile) {
    if ($currentScript<>'admin.php' and $currentScript<>'setup.php') {
      echo "<form action='admin.php' method='post' id='dispClef' onclick='";
      if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) echo "moDePass=prompt(\"".$str['adminPwd']."\");if(moDePass===null) return; else {getElementById(\"adminPassword\").value=moDePass;this.submit();}"; else echo "this.submit();";
      echo "'><input type='hidden' name='adminPassword' id='adminPassword'>";
    if (isset($partieId)) echo $str['gamePass']." <span>$partieId</span></form>"; else echo $str['siteAdmin'];
      echo "</form>";}
    if (isset($partieId))  echo "<a href='#' id='aideDeJeu' onclick='window.open(\"aide.html\", \"_blank\", \"toolbar=0,location=0,menubar=0,width=50%\");'>".$str['help']."</a>";
    echo "<a href='https://github.com/Fouyoufr/remoteChampions/blob/main/doc/readme.md#utilisation-de-remote-champions' alt='".$str['rcUsage']."' id='aide' target='_blank'><img src='img/aide.png' alt='".$str['rcUsage']."'/></a>";
    echo "<a href='https://github.com/Fouyoufr/remoteChampions/issues' alt='".$str['bugReport']."' id='bugReport' target='_blank'><img src='img/bug.png' alt='".$str['bugReport']."'/></a>";}}

function updatePassword() {
  global $adminPassword,$str;
  $configFile = file('config.inc');
  function replace_a_line($data) {
    global $adminPassword;
     if (stristr($data,'$adminPassword=')) return "\$adminPassword='$adminPassword';\n";
     return $data;}
  $configFile=array_map('replace_a_line',$configFile);
  file_put_contents('config.inc', implode('',$configFile));
  $_SESSION['adminPassword']=$adminPassword;}
?>