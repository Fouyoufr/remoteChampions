<?php
$title='Marvel Champions';
$bodyClass='joueur';
include 'include.php';
?>
<div id='joueurDisp' class='joueur'>
  <div id="mechantPicJoueur">
    <div id="phaseMechantJoueur"></div>
    <div id="mechantLifeJoueur"></div>
  </div>
  <div id="joueur" onclick='document.getElementById("changeNameOld").innerText=document.getElementById("joueur").innerText;document.getElementById("changeName").style.display="block";document.getElementById("playerName").focus();'></div>
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
  <div id="changeName">
    <div class="titlePopup">Changement de Nom</div>
    Entrez le nouveau nom à affecter à <span id="changeNameOld"></span> :<br/>
    <input type='text' id='playerName' minlength="4" maxlength="50" style="width:90%;margin-top:50px;margin-bottom:50px;">
    <div style="text-align:center;">
      <input type ="button" value="Confirmer" onclick='ajaxPost("changeName",document.getElementById("playerName").value);'>
      <input type="button" value="Annuler" onclick='document.getElementById("changeName").style.display="none";'>
    </div>
  </div> 
</div>


<script language="JavaScript">
  ajaxCall(ajaxJoueurSet,'jGet='+encodeURIComponent(document.getElementById('jId').value),true)
  setInterval("ajaxCall(ajaxJoueurSet,'jGet='+encodeURIComponent(document.getElementById('jId').value),true)",2000); 
</script>
</body>
</html>