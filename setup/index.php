<?php
$title='Remote Champions';
$bodyClass='index';
include 'include.php';
global $str;
$xml=simplexml_load_file('boxes.xml');
if (!isset($partieId)) {
  echo '<div id="selectPartie">';
  if (isset($badPartie)) {echo "<div id='keyError'>&#9888; ".$str['badKey1']." '$badPartie' ".$str['badKey2']."... &#9888;</div>";}
  echo "<h1>".$str['welcome']."</h1><table><tr><td>".$str['typeKey']."</td><td><form method='post'><input type='text' name='p' id='selectPartieName' maxlength='6' size='6'><input type='submit' value='OK'></form></td></tr>";
  if (!$mobile) {
    echo "<tr><td>".$str['orCreate']."</td><td><form action='new.php' method='post' class='creer' onclick='";
    if ($publicPass!='') echo "moDePass=prompt(\"".$str['publicPass']."\");if(moDePass===null) return; else {getElementById(\"publicPass\").value=moDePass;this.submit();}"; else echo "this.submit();";
    echo "'><input type='hidden' name='publicPass' id='publicPass'>".$str['create']."</form></td></tr>";}
  echo "</table></div></body></html>";
displayBottom();
exit();}

if ($mobile) {
  if (!isset($joueurId) and !isset($_GET['mechant']) and !isset($_GET['desktop'])) {
  	exit ('<div id="selectPartieMobile"><h1>'.$str['mobileSelect'].'<br/>
   <input type="button" id="selecJ1"><input type="button" id="selecJ2"><input type="button" id="selecJ3"><input type="button" id="selecJ4">
   <input type="button" class="selectMechant" id="mechant" value="'.$str['villain'].'" onclick="window.location.href=\'mechant.php?p='.$partieId.'\'"></div>
 <script language="JavaScript">
 ajaxCallCache(ajaxSelecSet,\'ajax/\'+encodeURIComponent(document.getElementById(\'partie\').value+\'.xml\'),true);
 setInterval("ajaxCallCache(ajaxSelecSet,\'ajax/\'+encodeURIComponent(document.getElementById(\'partie\').value+\'.xml\'),true)",2000); 
 </script>');}}
foreach ($xml->box as $xmlBox) if ($xmlBox['own']==1) foreach ($xmlBox->principale as $principale) $principales[$principale['id']->__tostring()]=$principale['name'];
?>

<div id="mechantDisp">
  <span id="mechant"></span>
  <img id="mechantPic" onclick="document.getElementById('changeMechant').style.display='block';"></img>
  <div id="phaseMechant" onclick="if (document.getElementById('changePhaseVie').innerText!=0) document.getElementById('changePhase').style.display='block';"></div>
  <div>
    <input id='vieMechantMoins' type='button' value='<' onclick='document.getElementById("mechantLife").innerText-=1;ajaxPost("vieMechant",document.getElementById("mechantLife").innerText);'>
    <div id="mechantLife"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("mechantLife").innerText=parseInt(document.getElementById("mechantLife").innerText)+1;ajaxPost("vieMechant",document.getElementById("mechantLife").innerText);'>
  </div>
<?php
echo '<input id="mechantDesoriente" type="button" value="'.$str['villainConfused'].'"  onclick=\'ajaxPost("switch","mechantDesoriente");\'>
  <input id="mechantSonne" type="button" value="'.$str['villainStunned'].'" onclick=\'ajaxPost("switch","mechantSonne");\'>
  <input id="mechantTenace" type="button" value="'.$str['villainTough'].'" onclick=\'ajaxPost("switch","mechantTenace");\'>
  <input id="mechantRiposte" type="button" value="'.$str['villainRetaliate'].'" onclick=\'ajaxPost("switch","mechantRiposte");\'>
  <input id="mechantPercant" type="button" value="'.$str['villainPiercing'].'" onclick=\'ajaxPost("switch","mechantPercant");\'>
  <input id="mechantDistance" type="button" value="'.$str['villainRanged'].'" onclick=\'ajaxPost("switch","mechantDistance");\'>';

  if (!$mobile) echo "<div class='smartphoneIcone' onclick='window.open(\"mechant.php?p=\"+document.getElementById(\"partie\").value,\"\",\"titlebar=no,toolbar=no,status=no,menubar=no,scrollbars=no,height=170px,width=400px\");'></div>";
echo '</div><div id="compteurs">'.$str['counters'].'<br/><a  onclick=\'ajaxPost("addCompteur","");\' id="NewCompteurBtn">+</a><div id="compteursList"></div></div>';
for ($i = 1; $i <= 4; $i++) {
echo "<div id='joueur".$i."Disp' class='joueurDisp'><img class='picJoueur' id='picJoueur$i' onclick=\"document.getElementById('herosAChanger').value=$i;document.getElementById('changeHeros').style.display='block';\"></img><div><span id='joueur$i'></span></div><div id='vieDisp$i' class='vieDisp'></div><input id='desoriente$i' type='button' value='".$str['playerConfused']."'><input id='sonne$i' type='button' value='".$str['playerStunned']."'><input id='tenace$i' type='button' value='".$str['playerTough']."'><div id='joueur".$i."Etat' class='joueurEtat'></div><input id='joueur".$i."Numero' type='hidden' /><div id='online".$i."' class='pointVert'></div>";
  if (!$mobile) echo "<div class='smartphoneIcone' onclick='window.open(\"joueur.php?p=\"+document.getElementById(\"partie\").value+\"&j=\"+document.getElementById(\"joueur".$i."Numero\").value,\"\",\"titlebar=no,toolbar=no,status=no,menubar=no,scrollbars=no,height=170px,width=400px\");'></div>";
echo "</div>";}
displayBottom();
?>
<img id="indexFirst" src='img/first.png'/>
<div id="manigance">
<a  onclick='document.getElementById("NewPrincipale").style.display="block";' id="NewPrincipaleButton"></a>
<div>
  <div id="pincipaleMenace">
    <input id='maCrise' type='button' value='<' onclick='document.getElementById("manigancePri").innerText-=1;ajaxPost("manigance",document.getElementById("manigancePri").innerText);'>
    <div id="manigancePri"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("manigancePri").innerText=parseInt(document.getElementById("manigancePri").innerText)+1;ajaxPost("manigance",document.getElementById("manigancePri").innerText);'>
  </div>
  <div id="principaleAcc">
    <img src="img/Menace+.png"><div class="principaleAccButtons">
    <input class='vieBtn' type='button' value='<' onclick='document.getElementById("maniganceAcc").innerText-=1;ajaxPost("maniganceAcc",document.getElementById("maniganceAcc").innerText);'>
    <div id="maniganceAcc"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("maniganceAcc").innerText=parseInt(document.getElementById("maniganceAcc").innerText)+1;ajaxPost("maniganceAcc",document.getElementById("maniganceAcc").innerText);'> 
  </div></div>
  <div id="principaleMax">
    Max
    <input class='vieBtn' type='button' value='<' onclick='document.getElementById("manigancePriMax").innerText-=1;ajaxPost("maniganceMax",document.getElementById("manigancePriMax").innerText);'>
    <div id="manigancePriMax"></div>
    <input class='vieBtn' type='button' value='>' onclick='document.getElementById("manigancePriMax").innerText=parseInt(document.getElementById("manigancePriMax").innerText)+1;ajaxPost("maniganceMax",document.getElementById("manigancePriMax").innerText);'> 
  </div></div>
  <div id="manigancesAnnexes"></div>
  <a  onclick='document.getElementById("NewManigance").style.display="block";' id="PlusManigance">+</a>
</div>

<?php
echo '<div id="changePhase"><div class="titlePopup">'.$str['changePhase'].'</div>'.$str['confirmPhase'].'<br/>'.$str['toChangePhase'].' <span id="changePhaseNext"></span>, <span id="changePhaseMechant"></span> '.$str['phaseChange1'].' <span id="changePhaseVie"></span> '.$str['pahseChange2'].' ?<br/><br/>
  <div class="boutonsPopup">
    <input type ="button" value="'.$str['confirm'].'" onclick=\'ajaxPost("phase",document.getElementById("changePhaseNext").innerText);\' class=\'bouton\'>
    <input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("changePhase").style.display="none";\' class=\'bouton\'></div></div>

<div id="changeMechant">
  <div class="titlePopup">'.$str['changeVillain'].'</div>
  <div class="centre">&#9888; '.$str['changeVillain1'].' &#9888;<br/>'.$str['changeVillain2'].'.</div><br/>
  <div id="mechantSelect">';
foreach ($xml->box as $xmlBox) if ($xmlBox['own']==1) foreach ($xmlBox->mechant as $mechant) echo "<div class='changeMechant' onclick='ajaxPost(\"mechant\",".$mechant['id'].");'><img src='img/mechants/".$mechant['id'].".png'/>".$mechant['name'].'</div>';
echo '</div><br/><div class="boutonsPopup"><input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("changeMechant").style.display="none";\' class=\'bouton\'></div></div>';

echo '<div id="changeHeros">
  <div class="titlePopup">'.$str['changeHeros'].'</div>
  <input type="hidden" id="herosAChanger" value="0">
  <div class="centre">&#9888; '.$str['confirmHeroChange'].'.</div><br/><div id="herosSelect">';
foreach ($xml->box as $xmlBox) if ($xmlBox['own']==1) foreach ($xmlBox->heros as $heros) echo "<div class='changeHeros' onclick='ajaxPost(\"joueurNum=\"+document.getElementById(\"herosAChanger\").value+\"&heros\",".$heros['id'].");'><img src='img/heros/".$heros['id'].".png'/>".$heros['name'].'</div>';
echo '</div><br/><div class="boutonsPopup">
    <input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("changeHeros").style.display="none";\' class=\'bouton\'></div></div>';
echo '<div id="NewPrincipale"><div class="titlePopup">'.$str['ChangeMScheme'].'</div><div class="centre">&#9888; '.$str['confirmMScheme'].' &#9888;</div><br/><select name="NewPrincipaleId" id="NewPrincipaleId"><option value="">--'.$str['mainScheme'].'--</option>';
    foreach ($principales as $mpId => $mpNom) {echo"<option value='$mpId'>$mpNom</option>";}
  echo '</select><div class="boutonsPopup"><input type ="button" value="'.$str['confirm'].'" onclick=\'ajaxPost("NewPrincipale",document.getElementById("NewPrincipaleId").value);document.getElementById("NewPrincipale").style.display="none";\' class=\'bouton\'><input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("NewPrincipale").style.display="none";\' class=\'bouton\'></div></div>';

echo '<div id="NewManigance">
  <div class="titlePopup">'.$str['AddSideScheme'].'</div><select name=\'deck\' id=\'deck\'></select><select name="manigance" id="newManiganceId"></select><br/>
  <div class="boutonsPopup">
    <input type ="button" value="'.$str['confirm'].'" onclick=\'ajaxPost("newManigance",document.getElementById("newManiganceId").value);document.getElementById("newManiganceId").style.display="none";document.getElementById("NewManigance").style.display="none";document.getElementById("deck").value="0";\' class=\'bouton\' id=\'NewManiganceConfirm\' disabled>
    <input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("NewManigance").style.display="none";document.getElementById("newManiganceId").style.display="none";\' class=\'bouton\'></div></div>';

echo '<div id="changeNameIndex"><div class="titlePopup">'.$str['changePlayerName'].'</div>'.$str['newPlayerName'].' <span id="changeNameOld"></span> :<br/><input type=\'text\' id=\'playerName\' minlength="4" maxlength="50"><input type=\'hidden\' id=\'changeNameId\'>
  <div class="centre"><input type ="button" value="'.$str['confirm'].'" onclick=\'ajaxPost("j="+document.getElementById("changeNameId").value+"&changeName",document.getElementById("playerName").value);document.getElementById("changeNameIndex").style.display="none";document.getElementById("playerName").value="";\'><input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("changeNameIndex").style.display="none";\'></div></div>';

echo '<input id=\'popupNewManigance\' type=\'hidden\' value=\'\'/><input id=\'popupDelManigance\' type=\'hidden\' value=\'0\'/><div id=\'manigancePopup\'><h1>'.$str['sideSchemeInfo'].'</h1><span id=\'manigancePopupText\'></span><div class="boutonsPopup"><input type="button" value="OK" onclick=\'document.getElementById("manigancePopup").style.display="none";\'></div></div>';
?>
<script language="JavaScript">
  ajaxCallCache(ajaxMainSet,'ajax/'+encodeURIComponent(document.getElementById('partie').value)+'.xml?'+Math.random()*Math.random());
  setInterval("ajaxCallCache(ajaxMainSet,'ajax/'+encodeURIComponent(document.getElementById('partie').value)+'.xml?'+Math.random()*Math.random())",2000); 
</script>
</body>
</html>