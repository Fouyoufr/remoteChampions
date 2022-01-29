<?php
$title='Remote Champions';
$bodyClass='joueur';
include 'include.inc';
global $str;
$xmlBoxes=simplexml_load_file($boxFile);
echo "<div id='joueurDisp' class='joueur'><div id=\"mechantPicJoueur\"";
if ($mobile) echo " onclick='window.location.href=\".?desktop&p=\"+document.getElementById(\"partie\").value;'";
echo ">";
?>
    <div id="phaseMechantJoueur"></div>
    <div id="mechantLifeJoueur"></div>
  </div>
  <div id="picJoueur" onclick='document.getElementById("changeHerosMobile").style.display="block";'></div>
  <div id="joueur" onclick='document.getElementById("changeNameOld").innerText=document.getElementById("joueur").innerText;document.getElementById("changeNameMobile").style.display="block";document.getElementById("playerName").focus();'></div>
  <div class='vieJoueur'>
    <input class="vieJoueur" id="vieJoueurMoins" type="button" value="<" onclick='document.getElementById("vieJoueur").innerText-=1;ajaxPost("vieJoueur",document.getElementById("vieJoueur").innerText,"ajaxJoueurSet");'>
    <div id="vieJoueur"></div>
    <input class="vieJoueur" type="button" value=">" onclick='document.getElementById("vieJoueur").innerText=parseInt(document.getElementById("vieJoueur").innerText)+1;ajaxPost("vieJoueur",document.getElementById("vieJoueur").innerText,"ajaxJoueurSet");'>
  </div>
<?php
  echo '<input id="desJoueur" type="button" value="'.$str['playerConfused'].'" onclick=\'ajaxPost("switch","desoriente","ajaxJoueurSet");\'>
  <input id="sonJoueur" type="button" value="'.$str['playerStunned'].'"  onclick=\'ajaxPost("switch","sonne","ajaxJoueurSet");\'>
  <input id="tenJoueur" type="button" value="'.$str['playerTough'].'" onclick=\'ajaxPost("switch","tenace","ajaxJoueurSet");\'>';
?>
  <div id="etatJoueur" onclick='ajaxPost("switch","etat","ajaxJoueurSet");'></div>
  <img src="img/first.png" id="joueurFirst" onclick='ajaxPost("switch","premier","ajaxJoueurSet");'/>
  <a href="aide.html" target="_blank" class="joueurAide">&nbsp;</a>
</div>
<?php
echo '<div id="changeHerosMobile"><div class="mobilePopuptitle">'.$str['changeHeros'].'</div><form class="mobilePopupContent" onsubmit=\'ajaxPost("joueur="+document.getElementById("jId").value+"&heros",document.getElementById("herosSelect").value,"ajaxJoueurSet");document.getElementById("changeHerosMobile").style.display="none";return false;\'>
    &#9888; '.$str['confirmHeroChange'].'.<br/><br/><select id="herosSelect">';
foreach ($xmlBoxes->box as $xmlBox) if ($xmlBox['own']==1) foreach ($xmlBox->heros as $heros) echo '<option value="'.$heros['id'].'">'.$heros['name'].'</option>';
echo '</select><div class="buttonChange"><input type ="submit" value="'.$str['confirm'].'"><input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("changeHerosMobile").style.display="none";\' class=\'bouton\'></div></form></div>';
echo '<div id="changeNameMobile"><div class="mobilePopuptitle">'.$str['changePlayerName'].'</div><form class="mobilePopupContent" onsubmit=\'ajaxPost("changeName",document.getElementById("playerName").value,"ajaxJoueurSet");document.getElementById("changeNameMobile").style.display="none";return false;\'>'.$str['newPlayerName'].' <span id="changeNameOld"></span><br/><br/><input type=\'text\' id=\'playerName\' minlength="4" maxlength="50"><br/><br/>
<div class="centre"><input type ="submit" value="'.$str['confirm'].'"><input type="button" value="'.$str['cancel'].'" onclick=\'document.getElementById("changeNameMobile").style.display="none";\'></div></form></div>';
?>
<script type="application/javascript">ajaxPush(document.getElementById('partie').value,ajaxJoueurSet);</script>
</body>
</html>