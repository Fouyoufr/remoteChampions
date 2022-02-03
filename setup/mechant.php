<?php
$title='Remote Champions';
$bodyClass='mechant';
include 'include.inc';
global $str;
?>
<div id="mechantDispME">
  <span id="mechantME"></span>
  <img id="mechantPicME"></img>
  <div id="phaseMechantME"></div>
  <div>
    <input id='vieMechantMoinsME' type='button' value='<' onclick='document.getElementById("mechantLifeME").innerText-=1;ajaxPost("vieMechant",document.getElementById("mechantLifeME").innerText,"ajaxMechantSet");'>
    <div id="mechantLifeME"></div>
    <input class='vieBtnME' type='button' value='>' onclick='document.getElementById("mechantLifeME").innerText=parseInt(document.getElementById("mechantLifeME").innerText)+1;ajaxPost("vieMechant",document.getElementById("mechantLifeME").innerText,"ajaxMechantSet");'>
  </div>
<?php
echo '<input id="mechantDesorienteME" type="button" value="'.$str['villainConfused'].'"  onclick=\'ajaxPost("switch","mechantDesoriente","ajaxMechantSet");\'><input id="mechantSonneME" type="button" value="'.$str['villainStunned'].'" onclick=\'ajaxPost("switch","mechantSonne","ajaxMechantSet");\'><input id="mechantTenaceME" type="button" value="'.$str['villainTough'].'" onclick=\'ajaxPost("switch","mechantTenace","ajaxMechantSet");\'><input id="mechantRiposteME" type="button" value="'.$str['villainRetaliate'].'" onclick=\'ajaxPost("switch","mechantRiposte","ajaxMechantSet");\'><input id="mechantPercantME" type="button" value="'.$str['villainPiercing'].'" onclick=\'ajaxPost("switch","mechantPercant","ajaxMechantSet");\'><input id="mechantDistanceME" type="button" value="'.$str['villainRanged'].'" onclick=\'ajaxPost("switch","mechantDistance","ajaxMechantSet");\'>';
?>
</div>
<a href="javascript:fullScreen();" class="fsOn" id="fullScreen">&nbsp;</a>
<div id="smartPhoneTilt"><script type="application/javascript">smartPhoneTilt();</script></div>
<script type="application/javascript">
ajaxPush(document.getElementById('partie').value,ajaxMechantSet);
if (!fsRequestMethod) {document.getElementById('fullScreen').style.display='none';}
</script>
</body>
</html>