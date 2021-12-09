<?php
function displaydeck($deck) {
  global $imageBoite;
  if (!isset($imageBoite) or $imageBoite<>$deck['dBoite']) {
    echo "</td></tr><tr><td><img src='img/boites/".$deck['dBoite'].".png'/></td><td>";
    $imageBoite=$deck['dBoite'];}
  echo "<div class='newEncadre'><input type='checkbox' name='deck".$deck['dId']."' class='deckCheck'";
  if (empty($_POST) or $_POST['deck'.$deck['dId']]=='on') echo ' checked';
  echo "><label for='deck".$deck['dId']."'>".$deck['dNom']."</label></div>";}
$title='Remote Champions - New';
$bodyClass='new';
include 'include.php';
global $str;
#Gestion du mode public
if (isset($_POST['publicPass']) and !empty($_POST['publicPass'])) $_SESSION['publicPass']=hash('sha256',$_POST['publicPass']);
if ($publicPass!='' and (!isset($_SESSION['publicPass']) or $_SESSION['publicPass']<>$publicPass)) {
  echo"<div class='pannel'><div class='titleAdmin'>".$str['restricted']."</div>".$str['sorryRestricted']."...<br/><a class='adminButton' href='.'>".$str['restrictedBack']."</a></div>";
  displayBottom();
  exit('</body>');}
else unset($_POST['publicPass']);
$error='';
$clef='';
if(!empty($_POST) and empty(array_diff_key($_POST,array_flip(['clef','nbJoueurs','mechantSeul'])))) $error=$str['noDeckNoGame'];
if (isset($_POST['clef']) and strlen($_POST['clef'])<>6) $error=$str['6charUri'];
if (isset($_POST['clef'])) if (sql_exists("SELECT `pUri` FROM `parties` WHERE `pURI`='".strtoupper($_POST['clef'])."'")) $error=$str['existentKey1']." '".strtoupper($_POST['clef'])."' ".$str['existentKey2']; else $clef=strtoupper($_POST['clef']);
if ($clef=='') do {
  $clefCar = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  for ($i=0;$i<6;$i++) $clef.=$clefCar[mt_rand(0, strlen($clefCar)-1)];} while (sql_exists("SELECT `pUri` FROM `parties` WHERE `pURI`='$clef'"));
if (!empty($_POST) and $error=='') {
  #Création effective de la partie dans la base mySql/dans le cache AJAX
  $xml = new DomDocument('1.0', 'UTF-8');
  $xml->preserveWhiteSpace = false;
  $xml->formatOutput = true;
  $xmlPartie = $xml->appendChild($xml->createElement('partie'));
  $xmlPrinciaple=$xmlPartie->appendChild($xml->createElement('principale'));
    if(!isset($_POST['mechantSeul'])) {
      $sqlJoueurs="INSERT INTO `joueurs` (`jPartie`,`jNom`,`jVie`,`jStatut`,`jHeros`,`jNumero`) VALUES ";
      $premier=mt_rand(1,$_POST['nbJoueurs']);
      for ($i=1;$i<=$_POST['nbJoueurs'];$i++) {
        sql_get($sqlJoueurs."('$clef','Joueur $i','10','AE','0','$i')");
        xmlDoc($xmlPartie,array('joueur'=>array('jId'=>mysqli_insert_id($sqlConn),'jNom'=>"Joueur $i",'jNumero'=>$i,'jVie'=>12,'jStatut'=>'AE','jDesoriente'=>0,'jSonne'=>0,'jTenace'=>0,'jOnline'=>0,'jHeros'=>0)));}}
    else {$premier=0;}
    $sqlDecks="INSERT INTO `deckParties` (`dpPartie`,`dpDeck`) VALUES ";
    //Création de la liste des decks et manigances
    $maniganceList=[];
    $sqlManigances=sql_get("SELECT `maId`,`maNom`,`dId` FROM `boites`,`decks`,`manigances` WHERE `dBoite`=`bId` AND `bInclus`='1' AND `maDeck`=`dId` ORDER BY `maNom`");
    while ($manigance=mysqli_fetch_assoc($sqlManigances)) {$maniganceList[]=array('maId'=>$manigance['maId'],'maNom'=>$manigance['maNom'],'dId'=>$manigance['dId']);}
    foreach($_POST as $post=>$postValue) if (substr($post,0,4)=='deck') {
      $sqlDecks.="('$clef','".substr($post,4)."'),";
      $xmlDeck=$xmlPartie->appendChild($xml->createElement('deck'));
      $xdAttr1=$xml->createAttribute('dId');
      $xdAttr1->appendChild($xml->createTextNode(substr($post,4)));
      $xdAttr2=$xml->createAttribute('dNom');
      $xdAttr2->appendChild($xml->createTextNode(deckNames()[substr($post,4)]));
      $xmlDeck->appendChild($xdAttr1);
      $xmlDeck->appendChild($xdAttr2);
      foreach ($maniganceList as $mlId=>$mlValue) if ($mlValue['dId']==substr($post,4)) {
        $xdAttr1=$xml->createAttribute('maId');
        $xdAttr1->appendChild($xml->createTextNode($mlValue['maId']));
        $xdAttr2=$xml->createAttribute('maNom');
        $xdAttr2->appendChild($xml->createTextNode($mlValue['maNom']));
        $xmlMani=$xmlDeck->appendChild($xml->createElement('maniChoice'));
        $xmlMani->appendChild($xdAttr1);
        $xmlMani->appendChild($xdAttr2);}}
    sql_get(substr($sqlDecks,0,-1));
    sql_get("INSERT INTO `parties` (`pUri`,`pPremier`) VALUES ('$clef','$premier')");
    xmlDoc($xmlPartie,array('pUri'=>$clef,'pMechant'=>0,'pMechVie'=>0,'pMechPhase'=>1,'pDate'=>time(),'pPremier'=>$premier,'pManiDelete'=>0,'pManiCourant'=>0,'pManiMax'=>0,'pManiAcceleration'=>0,'pMechRiposte'=>0,'pMechPercant'=>0,'pMechDistance'=>0,'mNom'=>'Choisir Le Méchant'));
    if (!is_dir('/ajax')) {mkdir('ajax');}
    $xml->save('ajax/'.$clef.'.xml');
    exit ("<script language='JavaScript'>window.location.href='.?p=$clef'</script>");}
echo "<form id='newPartie' method='post'><h1>".$str['newGameTitle']."</h1>";
if ($error<>'') echo "<div class='newError'>".$str['error'].": $error.</div>";
echo "<table class='newPartie'><tr><td>".$str['playerNB']."</td><td><select id='nbJoueurs' name='nbJoueurs'";
if (isset($_POST['mechantSeul']) and $_POST['mechantSeul']=='on') echo ' disabled';
  echo "><option value='1' ";
if (isset($_POST['nbJoueurs']) and $_POST['nbJoueurs']==1) echo ' selected';
echo ">".$str['1player']."</option>";
for ($nbj=2;$nbj<5;$nbj++) {
  echo "<option value='$nbj'";
  if (isset($_POST['nbJoueurs']) and $_POST['nbJoueurs']==$nbj) echo ' selected';
  echo ">$nbj ".$str['players']."</option>";}
echo "</select></td></tr><tr><td><label for ='mechantSeul'>".$str['villainOnly']."</label></td><td><input type='checkbox' name='mechantSeul' onclick='if (this.checked) document.getElementById(\"nbJoueurs\").disabled=true; else document.getElementById(\"nbJoueurs\").disabled=false;'";
if (isset($_POST['mechantSeul']) and $_POST['mechantSeul']=='on') echo " checked";
echo "/></td></tr><tr><td>".$str['gameUri']."</td><td><input type='text' name='clef' value='$clef' maxlength='6' size='6'/></td></tr><tr><td></td><td><input type='submit' value='".$str['create']."'></td></tr></table><div class='newOptions'>".$str['availableDecks']." (<a href='#' id='selecTitle' onclick='var selecTitle=document.getElementById(\"selecTitle\");var fields=document.getElementsByClassName(\"deckCheck\"); if (selecTitle.innerHTML==\"".$str['unselectAll']."\") {selecTitle.innerHTML=\"".$str['selectAll']."\";for (let item of fields) if (item.name.startsWith(\"deck\")) item.checked=false;} else {selecTitle.innerHTML=\"".$str['unselectAll']."\";for (let item of fields) if (item.name.startsWith(\"deck\")) item.checked=true;}'>".$str['unselectAll']."</a>):</div><table><tr><td></td><td>";
$decks=sql_get("SELECT `dId`,`dNom`,`dBoite` FROM `boites`,`decks` WHERE `bId`='1' AND `dBoite`=`bId` AND `bInclus`='1' ORDER BY `dNom`");
while ($deck=mysqli_fetch_assoc($decks)) displaydeck($deck);
echo "</td></tr><tr><td></td><td>";
$decks=sql_get("SELECT `dId`,`dNom`,`dBoite` FROM `boites`,`decks` WHERE `bType`='b'  AND `bId`!='1' AND `dBoite`=`bId` AND `bInclus`='1' ORDER BY `bNom`,`dNom`");
while ($deck=mysqli_fetch_assoc($decks)) displaydeck($deck);
echo "</td></tr><tr><td></td><td>";
$decks=sql_get("SELECT `dId`,`dNom`,`dBoite` FROM `boites`,`decks` WHERE `bType`='s'  AND `bId`!='1' AND `dBoite`=`bId` AND `bInclus`='1' ORDER BY `bNom`,`dNom`");
while ($deck=mysqli_fetch_assoc($decks)) displaydeck($deck);
echo "</td></tr></table></form>";
displayBottom();
echo "</body></html>";
?>
</body>
</html>