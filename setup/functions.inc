<?php
$rcLangs=array('fr','en');

$rcLang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
if (isset($_GET['lang']) and in_array($_GET['lang'],$rcLangs)) $_SESSION['lang']=$_GET['lang'];
if (isset($_SESSION['lang'])) $rcLang=$_SESSION['lang'];
$rcLang=in_array($rcLang,$rcLangs)?$rcLang:'fr';
include_once($rcLang.'/lang.php');
$boxFile=$rcLang.'/boxes.xml';
$gitToken='ghp_';
$gitToken.='RWTKe98avc5KnbAKmenMtWXgnS9Ztd2kSf25';

function restrictAccess(){
  global $str,$adminPassword;
  if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) {
    echo("<div class='pannel'><div class='titleAdmin'>".$str['restrictedTitle']."</div>".$str['restricted']."<br/><a class='adminButton' href='.'>".$str['restrictedBack']."</a></div>");
    displayBottom();
    exit('</body>');}}

function xmlAttr($xaObj,$xaToadd) {
  foreach ($xaToadd as $xaKey=>$xaValue) {$xaObj->addAttribute($xaKey,$xaValue);}}

function xmlSave($xsObject,$xsFile) {
  $dom = dom_import_simplexml($xsObject)->ownerDocument;
  $dom->formatOutput = TRUE;
  $dom->save($xsFile);}

function gitFileDate($gitFile=null){
  global $str,$gitBranch,$gitToken;
  #Récupération des informations du repositery par les API gitHub (le $context permet de passer un userAgent à file_get_contents, requis par gitHub)
  $context = stream_context_create(array('http'=>array('method'=>'GET','header'=>"Accept-language: en\r\nCookie: foo=bar\r\nUser-Agent: Fouyoufr\r\nAuthorization: Basic ".base64_encode("Fouyoufr:$gitToken"),'protocol_version'=>1.1)));
  $gitReq='https://api.github.com/repos/Fouyoufr/remotechampions/commits?per_page=1&sha='.$gitBranch;
  if (isset($gitFile) and $gitFile!='') $gitReq.='&path='.$gitFile;
  $jsonContent=@file_get_contents($gitReq,false,$context);
  if($jsonContent === FALSE) {return array('erreur'=>error_get_last()['message']);}
  $lastCommit=json_decode($jsonContent,true)[0]['commit'];
  return array('version'=>strtok($lastCommit['message'],"\n"),'comments'=>nl2br(ltrim(strstr($lastCommit['message'],"\n"),"\n")),'date'=>new DateTime($lastCommit['committer']['date']));}

function displayBottom() {
  global $partieId,$adminPassword,$mobile,$str,$gitBranch,$rcLang,$rcLangs;
  $currentScript=basename($_SERVER['PHP_SELF']);
  if (!$mobile) {
    if ($currentScript<>'admin.php' and $currentScript<>'setup.php') {
      echo "<form action='admin.php' method='post' id='dispClef' onclick='";
      if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) echo "moDePass=prompt(\"".$str['adminPwd']."\");if(moDePass===null) return; else {getElementById(\"adminPassword\").value=moDePass;this.submit();}"; else echo "this.submit();";
      echo "'><input type='hidden' name='adminPassword' id='adminPassword'>";
    if (isset($partieId)) echo $str['gamePass']." <span>$partieId</span></form>"; else echo $str['siteAdmin'];
      echo "</form>";}
    if (isset($partieId))  echo "<a href='#' id='aideDeJeu' onclick='window.open(\"$rcLang/aide.html\", \"_blank\", \"toolbar=0,location=0,menubar=0,width=50%\");'>".$str['help']."</a>";
    echo '<div id="lang-menu"><div class="'.$rcLang.'"></div><ul>';
    if (isset($_GET['lang'])) unset ($_GET['lang']);
    if (empty($_GET)) $urlLangAdd='?lang='; else $urlLangAdd='?'.$_SERVER['QUERY_STRING'].'&lang=';
    foreach ($rcLangs as $selecLang) echo '<li><a href="'.$urlLangAdd.$selecLang.'" class="'.$selecLang.'"></a></li>';
    echo '</ul></div>';
    echo "<a href='https://github.com/Fouyoufr/remoteChampions/blob/$gitBranch/doc/$rcLang/readme.md#utilisation-de-remote-champions' alt='".$str['rcUsage']."' id='aide' target='_blank'><img src='img/aide.png' alt='".$str['rcUsage']."'/></a>";
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