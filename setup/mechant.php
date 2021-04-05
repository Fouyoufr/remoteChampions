<?php
$title='Remote Champions';
$bodyClass='mechant';
include 'include.php';
?>
<div id="mechantDispME">
  <span id="mechantME"></span>
  <img id="mechantPicME"></img>
  <div id="phaseMechantME" onclick="ajaxCall(ajaxPhase,'p='+encodeURIComponent(document.getElementById('partie').value)+'&phase')"></div>
  <div>
    <input id='vieMechantMoinsME' type='button' value='<' onclick='document.getElementById("mechantLifeME").innerText-=1;ajaxPost("vieMechant",document.getElementById("mechantLifeME").innerText);'>
    <div id="mechantLifeME"></div>
    <input class='vieBtnME' type='button' value='>' onclick='document.getElementById("mechantLifeME").innerText=parseInt(document.getElementById("mechantLifeME").innerText)+1;ajaxPost("vieMechant",document.getElementById("mechantLifeME").innerText);'>
  </div>
  <input id="mechantDesorienteME" type="button" value="Désor."  onclick='ajaxPost("switch","mechantDesoriente");'>
  <input id="mechantSonneME" type="button" value="Sonné" onclick='ajaxPost("switch","mechantSonne");'>
  <input id="mechantTenaceME" type="button" value="Tenace" onclick='ajaxPost("switch","mechantTenace");'>
  <input id="mechantRiposteME" type="button" value="Riposte" onclick='ajaxPost("switch","mechantRiposte");'>
  <input id="mechantPercantME" type="button" value="Perçant" onclick='ajaxPost("switch","mechantPercant");'>
  <input id="mechantDistanceME" type="button" value="A distance" onclick='ajaxPost("switch","mechantDistance");'>
</div>


<script language="JavaScript">
  ajaxCall(ajaxMechantSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)
  setInterval("ajaxCall(ajaxMechantSet,'pGet='+encodeURIComponent(document.getElementById('partie').value),true)",2000); 
</script>
</body>
</html>