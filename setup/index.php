<?php
$title='Remote Champions';
$bodyClass='index';
include 'include.php';
if (!isset($partieId)) {
  if (isset($_POST['nbJoueurs'])) {
  	$nbJoueurs=htmlspecialchars($_POST['nbJoueurs']);
    $caracteres = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $partieId = '';
    for ($i=0;$i<6;$i++) {$partieId.=$caracteres[mt_rand(0, strlen($caracteres)-1)];}
    $insertDate=date('Y-m-d H:i:s');
    if ($nbJoueurs!=0) {
      for ($i=1;$i<=$nbJoueurs;$i++) {sql_get("INSERT INTO `joueurs` (`jPartie`,`jNom`,`jVie`,`jStatut`,`jHeros`,`jNumero`) VALUES ('$partieId', 'Joueur $i', '10', 'AE','1','$i')");}
      $premier=mt_rand(1,$nbJoueurs);}
    else {$premier=0;}
    sql_get("INSERT INTO `parties` (`pUri`,`pPremier`) VALUES ('$partieId','$premier')");
    exit ("<script language='JavaScript'>
    window.location.href = window.location.href+'?p=$partieId'
    </script>");}
else {
  echo '<form id="selectPartie" method="post">';
  if (isset($badPartie)) {echo '<div id="keyError">&#9888; Le mot-clef saisi n\'est pas valide... &#9888;</div>';}
  exit ('<h1>Choisir le mot-clef d\'une partie pour l\'afficher:<br/>
    <input type="text" name="p" id="selectPartieName">
    <input type="submit" value="OK" onclick="document.getElementById(\'nbJoueurs\').value=\'0\';" class="bouton"><br/>
    <h1>Ou créer une nouvelle partie pour 
    <select id="nbJoueurs" name="nbJoueurs"><option value="1">1 joueur</option><option value="2">2 joueur</option><option value="3">3 joueur</option><option value="4">4 joueur</option><option value="0">Méchant seul</option></select>.<br/>
    <input type="submit" value="Créer" onclick="document.getElementById(\'selectPartieName\').value=\'\';" class="bouton">
  </form>
</body>
</html>');}}

if ($mobile) {
  if (!isset($joueurId)) {
  	exit ('<div id="selectPartie">
  	  <h1>Choisir votre joueur:<br/>
   <input type="button" class="selectJoueur" id="selecJ1">
   <input type="button" class="selectJoueur" id="selecJ2">
   <input type="button" class="selectJoueur" id="selecJ3">
   <input type="button" class="selectJoueur" id="selecJ4">
 </div>
 <script language="JavaScript">
  ajaxCall(ajaxSelecSet,\'pGet=\'+encodeURIComponent(document.getElementById(\'partie\').value),true)
  setInterval("ajaxCall(ajaxSelecSet,\'pGet=\'+encodeURIComponent(document.getElementById(\'partie\').value),true)",2000); 
 </script>');}}

$sqlDecks=sql_get("SELECT `dId`,`dNom` FROM `decks`,`boites` WHERE `bId`=`dBoite` AND `bInclus`='1' ORDER By `dNom` ASC");
while ($deck=mysqli_fetch_assoc($sqlDecks)) $decks[$deck['dId']]=$deck['dNom'];
$sqlHeros=sql_get("SELECT `hId`,`hNom` FROM `heros`,`boites` WHERE `bId`=`hBoite` AND `bInclus`='1' ORDER By `hNom` ASC");
while ($hero=mysqli_fetch_assoc($sqlHeros)) $heros[$hero['hId']]=$hero['hNom'];
$sqlPrincipales=sql_get("SELECT * FROM `ManigancesPrincipales`,`boites` WHERE `mpId`!=0 AND `bInclus`='1' AND `bId`=`mpBoite` ORDER By `mpNom` ASC");
while ($principale=mysqli_fetch_assoc($sqlPrincipales))
$principales[$principale['mpId']]=$principale['mpNom'];
?>

<div id="mechantDisp">
  <span id="mechant"></span>
  <img id="mechantPic" onclick="document.getElementById('changeMechant').style.display='block';"></img>
  <div id="phaseMechant" onclick="ajaxCall(ajaxPhase,'p='+encodeURIComponent(document.getElementById('partie').value)+'&phase')"></div>
  <div>
    <input id='vieMechantMoins' type='button' value='<' onclick='document.getElementById("mechantLife").innerText-=1;ajaxPost("vieMechant",document.getElementById("mechantLife").innerText);'>
    <div id="mechantLife"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("mechantLife").innerText=parseInt(document.getElementById("mechantLife").innerText)+1;ajaxPost("vieMechant",document.getElementById("mechantLife").innerText);'>
  </div>
  <input id="mechantDesoriente" type="button" value="Désor."  onclick='ajaxPost("switch","mechantDesoriente");'>
  <input id="mechantSonne" type="button" value="Sonné" onclick='ajaxPost("switch","mechantSonne");'>
  <input id="mechantTenace" type="button" value="Tenace" onclick='ajaxPost("switch","mechantTenace");'>
  <input id="mechantRiposte" type="button" value="Riposte" onclick='ajaxPost("switch","mechantRiposte");'>
  <input id="mechantPercant" type="button" value="Perçant" onclick='ajaxPost("switch","mechantPercant");'>
  <input id="mechantDistance" type="button" value="A distance" onclick='ajaxPost("switch","mechantDistance");'>
  <div class='smartphoneIcone' onclick='window.open("mechant.php?p="+document.getElementById("partie").value,"","titlebar=no,toolbar=no,status=no,menubar=no,scrollbars=no,height=170px,width=400px");'></div>
</div>
<div id="compteurs">
  Autres Compteurs.<br/>
  <a  onclick='ajaxPost("addCompteur","");' id="NewCompteurBtn">+</a>
  <div id="compteursList">
  </div>
</div>

<?php
for ($i = 1; $i <= 4; $i++) {
echo "
<div id='joueur".$i."Disp' class='joueurDisp'>
  <img class='picJoueur' id='picJoueur$i' onclick=\"document.getElementById('herosAChanger').value=$i;document.getElementById('changeHeros').style.display='block';\"></img>
  <div><span id='joueur$i'></span></div>
  <div id='vieDisp$i' class='vieDisp'></div>
  <input id='desoriente$i' type='button' value='Désorienté'>
  <input id='sonne$i' type='button' value='Sonné'>
  <input id='tenace$i' type='button' value='Tenace'>
  <div id='joueur".$i."Etat' class='joueurEtat'></div>
  <input id='joueur".$i."Numero' type='hidden' />
  <div id='online".$i."' class='pointVert'></div>
  <div class='smartphoneIcone' onclick='window.open(\"joueur.php?j=\"+document.getElementById(\"joueur".$i."Numero\").value,\"\",\"titlebar=no,toolbar=no,status=no,menubar=no,scrollbars=no,height=170px,width=400px\");'></div>
</div>";}
echo "<form action='admin.php' method='post' id='dispClef' onclick='prompt(\"Mot de passe administratif\");this.submit();'>
<input type='hidden' name='adminPassword' id='adminPassword'>
Le mot-clef de cette partie est <span>$partieId</span></form>";
#onclick="document.getElementById('form-id').submit();"
#person = prompt("Please enter your name", "Harry Potter");
?>
<img id="indexFirst" src='img/first.png'/>
<div id="manigance">
<a  onclick='document.getElementById("NewPrincipale").style.display="block";' id="NewPrincipaleButton"></a>
<div>
  <div style="float:left;">
    <input id='maCrise' type='button' value='<' onclick='document.getElementById("manigancePri").innerText-=1;ajaxPost("manigance",document.getElementById("manigancePri").innerText);'>
    <div id="manigancePri"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("manigancePri").innerText=parseInt(document.getElementById("manigancePri").innerText)+1;ajaxPost("manigance",document.getElementById("manigancePri").innerText);'>
  </div>
  <div style="background-color:rgba(255,255,255,0.3);display:inline-block;padding:2px;">
    <img src="img/Menace+.png" style="display:inline-block;height:100%;vertical-align:middle;"/><div style="display:inline-block;height:100%;vertical-align:middle;">
    <input class='vieBtn' type='button' value='<' onclick='document.getElementById("maniganceAcc").innerText-=1;ajaxPost("maniganceAcc",document.getElementById("maniganceAcc").innerText);'>
    <div id="maniganceAcc"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("maniganceAcc").innerText=parseInt(document.getElementById("maniganceAcc").innerText)+1;ajaxPost("maniganceAcc",document.getElementById("maniganceAcc").innerText);'> 
  </div></div>
  <div style="float:right;">
    Max
    <input class='vieBtn' type='button' value='<' onclick='document.getElementById("manigancePriMax").innerText-=1;ajaxPost("maniganceMax",document.getElementById("manigancePriMax").innerText);'>
    <div id="manigancePriMax"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("manigancePriMax").innerText=parseInt(document.getElementById("manigancePriMax").innerText)+1;ajaxPost("maniganceMax",document.getElementById("manigancePriMax").innerText);'> 
  </div></div>
  <div id="manigancesAnnexes"></div>
  <a  onclick='document.getElementById("NewManigance").style.display="block";' id="PlusManigance">+</a>
</div>

<div id="changePhase">
<div class="titlePopup">Changement de phase</div>
  Confirmez-vous le changement de phase de jeu ?<br/>
  Pour le passage à la phase <span id="changePhaseNext"></span>, <span id="changePhaseMechant"></span> va passer à <span id="changePhaseVie"></span> points de vie ?<br/><br/>
  <div class="boutonsPopup">
    <input type ="button" value="Confirmer" onclick='ajaxPost("phase",document.getElementById("changePhaseNext").innerText);' class='bouton'>
    <input type="button" value="Annuler" onclick='document.getElementById("changePhase").style.display="none";' class='bouton'>
  </div>
</div>

<div id="changeMechant">
  <div class="titlePopup">Choisir/Changer le méchant de la partie</div>
  <div style="text-align:center;">&#9888; Si vous changez de méchant, le nouveau sera réinitialisé à la phase 1 &#9888;<br/>Et le jeton premier joueur sera réattribué aléatoirement.</div><br/>
  <div id="mechantSelect">
<?php
$mechants=sql_get("SELECT * FROM `mechants`,`boites` WHERE `mID`>0 AND `mBoite`=`bID` AND `bInclus`='1' ORDER BY `mNom` ASC");
while ($mechant=mysqli_fetch_assoc($mechants)) {echo "<div class='changeMechant' onclick='ajaxPost(\"mechant\",".$mechant['mId'].");'><img src='img/mechants/".$mechant['mId'].".png' style='background:white;'/>".$mechant['mNom'].'</div>';}
?>
  </div>
  <br/>
  <div class="boutonsPopup">
    <input type="button" value="Annuler" onclick='document.getElementById("changeMechant").style.display="none";' class='bouton'>
  </div>
</div>

<div id="changeHeros">
  <div class="titlePopup">Choisir/Changer de personnage</div>
  <input type="hidden" id="herosAChanger" value="0">
  <div style="text-align:center;">&#9888; Si vous changez de personnage, la vie de ce joueur sera réinitialisée.</div><br/>
  <div id="herosSelect">
<?php
$heros=sql_get("SELECT * FROM `heros`,`boites` WHERE `hId`>0 AND `hBoite`=`bID` AND `bInclus`='1' ORDER BY `hNom` ASC");
while ($hero=mysqli_fetch_assoc($heros)) {echo "<div class='changeHeros' onclick='ajaxPost(\"joueur=\"+document.getElementById(\"herosAChanger\").value+\"&heros\",".$hero['hId'].");'><img src='img/heros/".$hero['hId'].".png' style='background:white;'/>".$hero['hNom'].'</div>';}
?>
  </div>
  <br/>
  <div class="boutonsPopup">
    <input type="button" value="Annuler" onclick='document.getElementById("changeHeros").style.display="none";' class='bouton'>
  </div>
</div>

<div id="NewPrincipale">
  <div class="titlePopup">Changer la manigance principale</div>
  <div style="text-align:center;">&#9888; Les valeurs actuelles de menace/maximum seront remplaçées. &#9888;</div><br/>
  <select name="NewPrincipaleId" id="NewPrincipaleId">
  <option value="">--Manigance Principale--</option>
<?php
    foreach ($principales as $mpId => $mpNom) {echo"<option value='$mpId'>$mpNom</option>";}
?>
  </select>
  <div class="boutonsPopup">
    <input type ="button" value="Confirmer" onclick='ajaxPost("NewPrincipale",document.getElementById("NewPrincipaleId").value);document.getElementById("NewPrincipale").style.display="none";' class='bouton'>
    <input type="button" value="Annuler" onclick='document.getElementById("NewPrincipale").style.display="none";' class='bouton'>
  </div>
</div>

<div id="NewManigance">
  <div class="titlePopup">Ajouter une manigance annexe à la partie</div>

    <select name="deck" id="deck" onchange="if (this.value=='0') {getElementById('newManiganceId').style.display='none';} else {ajaxCall(ajaxManigancesMenu,'p='+document.getElementById('partie').value+'&mGet='+(this.value));}">
      <option value="0">--Choisissez le deck--</option>
<?php
  foreach ($decks as $dId => $dNom) {echo"<option value='$dId'>$dNom</option>";}
  echo '<optGroup>';
  foreach ($heros as $hId => $hNom) echo"<option value='h$hId'>$hNom</option>";
?>
      </optGroup>
    </select>
    <select name="manigance" id="newManiganceId" style="display:none;"></select><br/>
  <div class="boutonsPopup">
    <input type ="button" value="Confirmer" onclick='ajaxPost("newManigance",document.getElementById("newManiganceId").value);document.getElementById("NewManigance").style.display="none";document.getElementById("deck").value="0";' class='bouton'>
    <input type="button" value="Annuler" onclick='document.getElementById("NewManigance").style.display="none";' class='bouton'>
  </div>
</div>

<div id="changeNameIndex">
    <div class="titlePopup">Changement de Nom Joueur</div>
    Entrez le nouveau nom à affecter à <span id="changeNameOld"></span> :<br/>
    <input type='text' id='playerName' minlength="4" maxlength="50" style="width:90%;margin-top:50px;margin-bottom:50px;">
    <input type='hidden' id='changeNameId'>
    <div style="text-align:center;">
      <input type ="button" value="Confirmer" onclick='ajaxPost("j="+document.getElementById("changeNameId").value+"&changeName",document.getElementById("playerName").value);document.getElementById("changeNameIndex").style.display="none";'>
      <input type="button" value="Annuler" onclick='document.getElementById("changeNameIndex").style.display="none";'>
    </div>
  </div> 

<script language="JavaScript">
  ajaxCall(ajaxMainSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)
  setInterval("ajaxCall(ajaxMainSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)",2000); 
</script>
</body>
</html>