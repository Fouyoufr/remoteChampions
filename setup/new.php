<?php
function displaydeck($deck) {
  global $imageBoite;
  if (!isset($imageBoite) or $imageBoite<>$deck['dBoite']) {
    echo "</td></tr><tr><td><img src='img/boites/".$deck['dBoite'].".png'/></td><td>";
    $imageBoite=$deck['dBoite'];}
  echo "<div class='newEncadre'><input type='checkbox' name='deck".$deck['dId']."' class='deckCheck'";
  if (empty($_POST) or $_POST['deck'.$deck['dId']]=='on') echo ' checked';
  echo "><label for='deck".$deck['dId']."'style='display:inline-block;'>".$deck['dNom']."</label></div>";}
$title='Remote Champions - Création de partie';
$bodyClass='new';
include 'include.php';

#Gestion du mode public
if (isset($_POST['publicPass']) and !empty($_POST['publicPass'])) $_SESSION['publicPass']=hash('sha256',$_POST['publicPass']);
if ($publicPass!='' and (!isset($_SESSION['publicPass']) or $_SESSION['publicPass']<>$publicPass)) {
  echo"<div class='pannel'><div class='titleAdmin'>Accès restreint</div>Désolé, le serveur est en mode public et la création de nouvelles parties est protégée par un mot de passe...<br/><a class='adminButton' href='.'>Retour au site</a></div>";
  displayBottom();
  exit('</body>');}
else unset($_POST['publicPass']);

$error='';
$clef='';
if(!empty($_POST) and empty(array_diff_key($_POST,array_flip(['clef','nbJoueurs','mechantSeul'])))) $error='Vous ne pouvez créer une partie sans sélectionner aucun deck';
if (isset($_POST['clef']) and strlen($_POST['clef'])<>6) $error="La clef d'accès doit faire exactement 6 caractères";
if (isset($_POST['clef'])) if (sql_exists("SELECT `pUri` FROM `parties` WHERE `pURI`='".strtoupper($_POST['clef'])."'")) $error="La clef '".strtoupper($_POST['clef'])."' est déjà présente"; else $clef=strtoupper($_POST['clef']);
if ($clef=='') do {
  $clefCar = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  for ($i=0;$i<6;$i++) $clef.=$clefCar[mt_rand(0, strlen($clefCar)-1)];} while (sql_exists("SELECT `pUri` FROM `parties` WHERE `pURI`='$clef'"));
if (!empty($_POST) and $error=='') {
  #Création effective de la partie dans la base mySql
    if(!isset($_POST['mechantSeul'])) {
      $sqlJoueurs="INSERT INTO `joueurs` (`jPartie`,`jNom`,`jVie`,`jStatut`,`jHeros`,`jNumero`) VALUES ";
      for ($i=1;$i<=$_POST['nbJoueurs'];$i++) $sqlJoueurs.="('$clef','Joueur $i','10','AE','0','$i'),";
      sql_get(substr($sqlJoueurs,0,-1));
      $premier=mt_rand(1,$_POST['nbJoueurs']);}
    else {$premier=0;}
    $sqlDecks="INSERT INTO `deckParties` (`dpPartie`,`dpDeck`) VALUES ";
    foreach($_POST as $post=>$postValue) if (substr($post,0,4)=='deck') $sqlDecks.="('$clef','".substr($post,4)."'),";
    sql_get(substr($sqlDecks,0,-1));
    sql_get("INSERT INTO `parties` (`pUri`,`pPremier`) VALUES ('$clef','$premier')");
    exit ("<script language='JavaScript'>window.location.href='.?p=$clef'</script>");}
echo "<form id='newPartie' method='post'><h1>Création d'une nouvelle partie</h1>";
if ($error<>'') echo "<div class='newError'>Erreur: $error.</div>";
echo "<table class='newPartie'><tr><td>Nombre de joueurs</td><td><select id='nbJoueurs' name='nbJoueurs'";
if (isset($_POST['mechantSeul']) and $_POST['mechantSeul']=='on') echo ' disabled';
  echo "><option value='1' ";
if (isset($_POST['nbJoueurs']) and $_POST['nbJoueurs']==1) echo ' selected';
echo ">1 joueur</option>";
for ($nbj=2;$nbj<5;$nbj++) {
  echo "<option value='$nbj'";
  if (isset($_POST['nbJoueurs']) and $_POST['nbJoueurs']==$nbj) echo ' selected';
  echo ">$nbj joueurs</option>";}
echo "</select></td></tr><tr><td><label for ='mechantSeul'>Méchant seul</label></td><td><input type='checkbox' name='mechantSeul' onclick='if (this.checked) document.getElementById(\"nbJoueurs\").disabled=true; else document.getElementById(\"nbJoueurs\").disabled=false;'";
if (isset($_POST['mechantSeul']) and $_POST['mechantSeul']=='on') echo " checked";
echo "/></td></tr><tr><td>Clef d'accès</td><td><input type='text' name='clef' value='$clef' maxlength='6' size='6'/></td></tr><tr><td></td><td><input type='submit' value='Créer'></td></tr></table><div class='newOptions'>Decks disponibles pour cette partie (<a href='#' id='selecTitle' onclick='var selecTitle=document.getElementById(\"selecTitle\");var fields=document.getElementsByClassName(\"deckCheck\"); if (selecTitle.innerHTML==\"Tout déselectionner\") {selecTitle.innerHTML=\"Tout sélectionner\";for (let item of fields) if (item.name.startsWith(\"deck\")) item.checked=false;} else {selecTitle.innerHTML=\"Tout déselectionner\";for (let item of fields) if (item.name.startsWith(\"deck\")) item.checked=true;}'>Tout déselectionner</a>):</div><table><tr><td></td><td>";
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