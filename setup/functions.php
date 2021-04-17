<?php
function gitFileDate($gitFile=null){
  #Récupération des informations du repositery par les API gitHub (le $context permet de passer un userAgent à file_get_contents, requis par gitHub)
  $context = stream_context_create(array('http'=>array('method'=>"GET",'header'=>"Accept-language: en\r\n"."Cookie: foo=bar\r\n"."User-Agent: Fouyoufr")));
  $gitReq='https://api.github.com/repos/fouyoufr/remotechampions/commits?per_page=1';
  if (isset($gitFile) and $gitFile!='') $gitReq.='&path='.$gitFile;
  $jsonContent=@file_get_contents($gitReq,false,$context);
  if($jsonContent === FALSE) { return array('erreur'=>error_get_last()['message']);}
  $lastCommit=json_decode($jsonContent,true)[0]['commit'];
  return array('version'=>strtok($lastCommit['message'],"\n"),'comments'=>nl2br(ltrim(strstr($lastCommit['message'],"\n"),"\n")),'date'=>new DateTime($lastCommit['committer']['date']));}

function sql_exists($query) {
  global $sqlConn;
  if (!isset($sqlConn)) sql_get('SHOW TABLES');
    $sqlQuery=mysqli_query($sqlConn,"$query LIMIT 1");
    if (mysqli_num_rows($sqlQuery)>0) return true; else return false;}

function displayBottom() {
  global $partieId,$adminPassword,$mobile;
  $currentScript=basename($_SERVER['PHP_SELF']);
  if (!$mobile) {
    if ($currentScript<>'admin.php' and $currentScript<>'setup.php') {
      echo "<form action='admin.php' method='post' id='dispClef' onclick='";
      if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) echo "moDePass=prompt(\"Mot de passe administratif\");if(moDePass===null) return; else {getElementById(\"adminPassword\").value=moDePass;this.submit();}"; else echo "this.submit();";
      echo "'><input type='hidden' name='adminPassword' id='adminPassword'>";
    if (isset($partieId)) echo "Le mot-clef de cette partie est <span>$partieId</span></form>"; else echo "Administration du site";
      echo "</form>";}
    echo "<a href='aide.html' target='_blank' id='aideDeJeu'>Aide</a>";
    echo "<a href='https://github.com/Fouyoufr/remoteChampions/blob/main/doc/readme.md#utilisation-de-remote-champions' alt='Utilisation de Remote Champions' id='aide' target='_blank'><img src='img/aide.png' alt='Utilisation de Remote Champions'/></a>";
    echo "<a href='https://github.com/Fouyoufr/remoteChampions/issues' alt='Rapporter un problème' id='bugReport' target='_blank'><img src='img/bug.png' alt='Rapporter un problème'/></a>";}}

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
?>