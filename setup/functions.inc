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
  global $partieId,$adminPassword,$mobile,$str,$gitBranch,$rcLang,$rcLangs,$meloDice,$meloDice1,$meloList,$bodyClass;
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
    echo "<a href='https://github.com/Fouyoufr/remoteChampions/issues' alt='".$str['bugReport']."' id='bugReport' target='_blank'><img src='img/bug.png' alt='".$str['bugReport']."'/></a>";

    if ($meloDice!='' and $bodyClass<>'admin') {
      echo '
      <script type="application/javascript">
        var tag=document.createElement(\'script\');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName(\'script\')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        var ytPlayer;

        function onYouTubeIframeAPIReady() {
          ytPlayer = new YT.Player(\'ytPlayer\', {events: {\'onReady\': ytPlayerStateChange,\'onStateChange\': ytPlayerStateChange}});}
        
        function ytPlayerStateChange(event) {
          if (ytPlayer.getPlayerState() == YT.PlayerState.PLAYING) {
            document.getElementById(\'meloPlay\').style.display=\'none\';
            document.getElementById(\'meloStop\').style.display=\'block\';
            document.getElementById(\'meloNext\').style.display=\'block\';
            document.getElementById(\'meloButton\').style.display=\'block\';}
          else {
            document.getElementById(\'meloPlay\').style.display=\'block\';
            document.getElementById(\'meloStop\').style.display=\'none\';
            document.getElementById(\'meloNext\').style.display=\'none\';
            document.getElementById(\'meloButton\').style.display=\'block\';}}

      </script>
      <div id="melodice">
        <input id="meloCheck" type="checkbox">
        <div id="meloDisplay">
          <a id="meloLink" href="https://melodice.org/playlist/'.$meloList.'" target="_blank">MELODICE</a>
          <iframe id="ytPlayer" title="melodice" type="text/html" src="https://www.youtube.com/embed/'.$meloDice1.'?enablejsapi=1&rel=0&playsinline=1&autoplay=0&showinfo=0&autohide=1&controls=0&modestbranding=1&disablekb=1&origin=http';
      if ($_SERVER['HTTPS']) echo 's';
      echo '://'.$_SERVER['SERVER_NAME'].'&playlist='.$meloDice.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen"></iframe>
          <div id="meloList">('.$meloList.')</div>
        </div>
        <div id="meloCommands">
          <a href="javascript:ytPlayer.playVideo();" id="meloPlay">&#x23f5;</a>
          <a  href="javascript:ytPlayer.pauseVideo();" id="meloStop">&#x23f8;</a>
          <a  href="javascript:ytPlayer.nextVideo();" id="meloNext">&#x23Ed;</a>
          <label for="meloCheck" id="meloButton"></label>
        </div>
      </div>';}}}

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