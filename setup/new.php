<?php
function displaydeck($deck,$box) {
  global $imageBoite;
  if (!isset($imageBoite) or $imageBoite<>$box['id']) {
    echo "</td></tr><tr><td><img src='img/boites/".$box['id'].".png' alt='".$box['name']."'/></td><td>";
    $imageBoite=$box['id'];}
  echo "<div class='newEncadre'><input type='checkbox' name='deck".$deck['id']."' class='deckCheck'";
  if (empty($_POST) or $_POST['deck'.$deck['id']]=='on') echo ' checked';
  echo "><label for='deck".$deck['id']."'>".$deck['name']."</label></div>";}

$title='Remote Champions - New';
$bodyClass='new';
include 'include.inc';
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
$xmlBoxes=simplexml_load_file($boxFile);
if(!empty($_POST) and empty(array_diff_key($_POST,array_flip(['clef','nbJoueurs','mechantSeul'])))) $error=$str['noDeckNoGame'];
if (isset($_POST['clef']) and strlen($_POST['clef'])<>6) $error=$str['6charUri'];
if (isset($_POST['clef']) and file_exists('ajax/'.strtoupper($_POST['clef']).'.xml')) $error=$str['existentKey1']." '".strtoupper($_POST['clef'])."' ".$str['existentKey2']; elseif (isset($_POST['clef'])) $clef=strtoupper($_POST['clef']);
if ($clef=='') do {
  $clefCar = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  for ($i=0;$i<6;$i++) $clef.=$clefCar[mt_rand(0, strlen($clefCar)-1)];} while (file_exists('ajax/'.$clef.'.xml'));
if (!empty($_POST) and $error=='') {
  #Création effective de la partie /dans le cache AJAX
  $partieXML=<<<XML
		<?xml version='1.0' encoding='UTF-8'?>
		<partie>
		</partie>
		XML;
	$xml=new SimpleXMLElement($partieXML);
    if(!isset($_POST['mechantSeul'])) {
      $premier=mt_rand(1,$_POST['nbJoueurs']);
      for ($i=1;$i<=$_POST['nbJoueurs'];$i++) {
        $xmlJoueur=$xml->addChild('joueur');
        xmlAttr($xmlJoueur,array('jId'=>$i,'jNom'=>"Joueur $i",'jNumero'=>$i,'jVie'=>12,'jStatut'=>'AE','jDesoriente'=>0,'jSonne'=>0,'jTenace'=>0,'jOnline'=>0,'jHeros'=>0));}}
    else {$premier=0;}
    foreach($_POST as $post=>$postValue) if (substr($post,0,4)=='deck') {
      $xmlChild=$xml->addChild('deck');
      foreach ($xmlBoxes as $xmlBox) foreach ($xmlBox->deck as $xmlDeck) if ($xmlDeck['id']==substr($post,4)) {
        xmlAttr($xmlChild,array('dId'=>$xmlDeck['id'],'dNom'=>$xmlDeck['name']));
        foreach($xmlDeck->scheme as $manigance) {
          $xmlChild=$xmlChild->addChild('maniChoice');
          xmlAttr($xmlChild,array('maId'=>$manigance['id'],'maNom'=>$manigance['name']));}}}
    xmlAttr($xml,array('pUri'=>$clef,'pMechant'=>0,'pMechVie'=>0,'pMechPhase'=>1,'pDate'=>time(),'pPremier'=>$premier,'pManiDelete'=>0,'pManiCourant'=>0,'pManiMax'=>0,'pManiAcceleration'=>0,'pMechRiposte'=>0,'pMechPercant'=>0,'pMechDistance'=>0,'mNom'=>'Choisir Le Méchant','mpNom'=>'','nextPhaseVie'=>0));
    if (!is_dir('ajax')) {mkdir('ajax');}
    xmlSave($xml,'ajax/'.$clef.'.xml');
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
foreach ($xmlBoxes as $xmlBox) if ($xmlBox['id']==1) foreach ($xmlBox->deck as $xmlDeck) displaydeck($xmlDeck,$xmlBox);
echo "</td></tr><tr><td></td><td>";
foreach ($xmlBoxes as $xmlBox) if ($xmlBox['own']==1 and $xmlBox['type']=='b' and $xmlBox['id']<>1) foreach ($xmlBox->deck as $xmlDeck) displaydeck($xmlDeck,$xmlBox);
echo "</td></tr><tr><td></td><td>";
foreach ($xmlBoxes as $xmlBox) if ($xmlBox['own']==1 and $xmlBox['type']=='s' and $xmlBox['id']<>1) foreach ($xmlBox->deck as $xmlDeck) displaydeck($xmlDeck,$xmlBox);
echo "</td></tr></table></form>";
displayBottom();
echo "</body></html>";
?>
</body>
</html>