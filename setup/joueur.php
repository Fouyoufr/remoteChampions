<?php
$title='Remote Champions';
$bodyClass='joueur';
include 'include.php';
echo "<div id='joueurDisp' class='joueur'><div id=\"mechantPicJoueur\"";
if ($mobile) echo " onclick='window.location.href=\".?desktop&p=\"+document.getElementById(\"partie\").value;'";
echo ">";
?>
    <div id="phaseMechantJoueur"></div>
    <div id="mechantLifeJoueur"></div>
  </div>
  <div id="picJoueur" onclick='document.getElementById("changeHerosMobile").style.display="block";'></div>
  <div id="joueur" onclick='document.getElementById("changeNameOld").innerText=document.getElementById("joueur").innerText;document.getElementById("changeNameMobile").style.display="block";document.getElementById("playerName").focus();'></div>
  <div style='text-align:center;margin:2%;'>
    <input class="vieJoueur" id="vieJoueurMoins" type="button" value="<" onclick='document.getElementById("vieJoueur").innerText-=1;ajaxPost("vieJoueur",document.getElementById("vieJoueur").innerText);'>
    <div id="vieJoueur"></div>
    <input class="vieJoueur" type="button" value=">" onclick='document.getElementById("vieJoueur").innerText=parseInt(document.getElementById("vieJoueur").innerText)+1;ajaxPost("vieJoueur",document.getElementById("vieJoueur").innerText);'>
  </div>
  <input id="desJoueur" type="button" value="Désorienté" onclick='ajaxPost("switch","desoriente");'>
  <input id="sonJoueur" type="button" value="Sonné"  onclick='ajaxPost("switch","sonne");'>
  <input id="tenJoueur" type="button" value="Tenace" onclick='ajaxPost("switch","tenace");'>
  <div id="etatJoueur" onclick='ajaxPost("switch","etat");'></div>
  <img src="img/first.png" id="joueurFirst" onclick='ajaxPost("switch","premier");'/>
</div>

<div id="changeHerosMobile">
  <div class="mobilePopuptitle">Choisir/Changer de personnage</div>
  <form class="mobilePopupContent" onsubmit='ajaxPost("joueur="+document.getElementById("jId").value+"&heros",document.getElementById("herosSelect").value);document.getElementById("changeHerosMobile").style.display="none";return false;'>
    &#9888; Si vous changez de personnage, la vie de ce joueur sera réinitialisée.<br/><br/>
    <select id="herosSelect">
<?php
  $heros=sql_get("SELECT * FROM `heros`,`boites` WHERE `hId`>0 AND `hBoite`=`bID` AND `bInclus`='1' ORDER BY `hNom` ASC");
  while ($hero=mysqli_fetch_assoc($heros)) echo '<option value="'.$hero['hId'].'">'.$hero['hNom'].'</option>';7
?>
    </select>
    <div style="text-align:center;margin-top:10%;">
    <input type ="submit" value="Confirmer">
    <input type="button" value="Annuler" onclick='document.getElementById("changeHerosMobile").style.display="none";' class='bouton'>
    </div>
</form>
</div>

<div id="changeNameMobile">
  <div class="mobilePopuptitle">Changement de Nom</div>
  <form class="mobilePopupContent" onsubmit='ajaxPost("changeName",document.getElementById("playerName").value);document.getElementById("changeNameMobile").style.display="none";return false;'>
    Entrez le nouveau nom à affecter à <span id="changeNameOld"></span><br/><br/>
    <input type='text' id='playerName' minlength="4" maxlength="50"><br/><br/>
    <div style="text-align:center;">
      <input type ="submit" value="Confirmer">
      <input type="button" value="Annuler" onclick='document.getElementById("changeNameMobile").style.display="none";'>
    </div>
</form>
</div>

<script language="JavaScript">
  ajaxCall(ajaxJoueurSet,'jGet='+encodeURIComponent(document.getElementById('jId').value),true)
  setInterval("ajaxCall(ajaxJoueurSet,'jGet='+encodeURIComponent(document.getElementById('jId').value),true)",2000); 
</script>
</body>
</html>