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
$error='';
$clef='';
if(empty(array_diff_key($_POST,array_flip(['clef','nbJoueurs','mechantSeul'])))) $error='Vous ne pouvez créer une partie sans sélectionner aucun deck';
if (isset($_POST['clef']) and strlen($_POST['clef'])<>6) $error="La clef d'accès doit faire exactement 6 caractères";
if (isset($_POST['clef'])) if (sql_exists("SELECT `pUri` FROM `parties` WHERE `pURI`='".strtoupper($_POST['clef'])."'")) $error="La clef est '".strtoupper($_POST['clef'])."' déjà présente"; else $clef=strtoupper($_POST['clef']);
if ($clef=='') do {
  $clefCar = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  for ($i=0;$i<6;$i++) $clef.=$clefCar[mt_rand(0, strlen($clefCar)-1)];} while (sql_exists("SELECT `pUri` FROM `parties` WHERE `pURI`='$vlef'"));
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
echo "/></td></tr>";
echo "<tr><td>Clef d'accès</td><td><input type='text' name='clef' value='$clef' maxlength='6' size='6'/></td></tr>";
echo "<tr><td></td><td><input type='submit' value='Créer'></td></tr></table><div class='newOptions'>Decks disponibles pour cette partie ";
echo "(<a href='#' onclick='var fields=document.getElementsByClassName(\"deckCheck\");for (let item of fields) {if (item.name.startsWith(\"deck\")) item.checked=false;}'>Tout déselectionner</a>):";
echo "</div><table><tr><td></td><td>";
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
echo "</body>
</html>";
?>
</body>
</html>