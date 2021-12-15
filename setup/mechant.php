<?php
$title='Remote Champions';
$bodyClass='mechant';
include 'include.php';
global $str;
?>
<div id="mechantDispME">
  <span id="mechantME"></span>
  <img id="mechantPicME"></img>
  <div id="phaseMechantME"></div>
  <div>
    <input id='vieMechantMoinsME' type='button' value='<' onclick='document.getElementById("mechantLifeME").innerText-=1;ajaxPost("vieMechant",document.getElementById("mechantLifeME").innerText);'>
    <div id="mechantLifeME"></div>
    <input class='vieBtnME' type='button' value='>' onclick='document.getElementById("mechantLifeME").innerText=parseInt(document.getElementById("mechantLifeME").innerText)+1;ajaxPost("vieMechant",document.getElementById("mechantLifeME").innerText);'>
  </div>
<?php
echo '<input id="mechantDesorienteME" type="button" value="'.$str['villainConfused'].'"  onclick=\'ajaxPost("switch","mechantDesoriente");\'><input id="mechantSonneME" type="button" value="'.$str['villainStunned'].'" onclick=\'ajaxPost("switch","mechantSonne");\'><input id="mechantTenaceME" type="button" value="'.$str['villainTough'].'" onclick=\'ajaxPost("switch","mechantTenace");\'><input id="mechantRiposteME" type="button" value="'.$str['villainRetaliate'].'" onclick=\'ajaxPost("switch","mechantRiposte");\'><input id="mechantPercantME" type="button" value="'.$str['villainPiercing'].'" onclick=\'ajaxPost("switch","mechantPercant");\'><input id="mechantDistanceME" type="button" value="'.$str['villainRanged'].'" onclick=\'ajaxPost("switch","mechantDistance");\'>';
?>
</div>

<script language="JavaScript">
  ajaxCallCache(ajaxMechantSet,'ajax/'+encodeURIComponent(document.getElementById('partie').value)+'.xml?'+Math.random()*Math.random());
  setInterval("ajaxCallCache(ajaxMechantSet,'ajax/'+encodeURIComponent(document.getElementById('partie').value)+'.xml?'+Math.random()*Math.random())",2000); 
</script>
</body>
</html>