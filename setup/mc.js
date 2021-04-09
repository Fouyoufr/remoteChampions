function ajaxCall (ajaxTraite,getParam) {
  document.getElementById('ajaxLoad').style.display='block';
  ajaxReq=new XMLHttpRequest();
  ajaxReq.open('GET','ajax.php?'+getParam);
  ajaxReq.onreadystatechange=ajaxTraite;
  ajaxReq.send();}
  
function ajaxPost (key,value) {
  if (document.getElementById('ajaxSave')) {document.getElementById('ajaxSave').style.display='block';}
  ajaxReqPost=new XMLHttpRequest();
  ajaxReqPost.open('POST','ajax.php',true);
  ajaxReqPost.onreadystatechange=function() {
  	if (document.getElementById('changePhase')) {document.getElementById('changePhase').style.display='none';}
  	if (document.getElementById('changeMechant')) {document.getElementById('changeMechant').style.display='none';}
  	if (document.getElementById('changeName')) {document.getElementById('changeName').style.display='none';}
    if (document.getElementById('changeHeros')) {document.getElementById('changeHeros').style.display='none';}
  	if (ajaxReqPost.responseText!='') {
  		if (ajaxReqPost.responseText=='SelectManigance') {document.getElementById('NewPrincipale').style.display='block';}
  		console.log(ajaxReqPost.responseText);}}
  ajaxReqPost.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  uri=key+'='+encodeURIComponent(value);
  if (document.getElementById('partie')) {uri+='&p='+encodeURIComponent(document.getElementById('partie').value);}
  if (document.getElementById('jId')) {uri+='&j='+encodeURIComponent(document.getElementById('jId').value);}
  ajaxReqPost.send(uri);}
  
function ajaxSelecSet() {
  if (ajaxReq.readyState === XMLHttpRequest.DONE) {
  	if (ajaxReq.status === 200) {
  	var xmlDoc = ajaxReq.responseXML;
    var nbJoueurs=xmlDoc.getElementsByTagName('joueur').length;
    for (i=1;i<=parseInt(nbJoueurs);i++) {
      var numero=parseInt(xmlDoc.getElementsByTagName('joueur')[i-1].getAttribute('jNumero'));
      if (document.getElementById('selecJ'+numero).style.display!='block') {document.getElementById('selecJ'+numero).style.display='block'}
      document.getElementById('selecJ'+numero).value=xmlDoc.getElementsByTagName('joueur')[i-1].getAttribute('jNom');
      document.getElementById('selecJ'+numero).setAttribute('onclick','window.location.href="joueur.php?j='+xmlDoc.getElementsByTagName('joueur')[i-1].getAttribute('jId')+'";');}
    if (nbJoueurs<4 && document.getElementById('selecJ4').style.display!='none') {document.getElementById('selecJ4').style.display='none'}
    if (nbJoueurs<3 && document.getElementById('selecJ3').style.display!='none') {document.getElementById('selecJ3').style.display='none'}
    if (nbJoueurs<2 && document.getElementById('selecJ2').style.display!='none') {document.getElementById('selecJ2').style.display='none'}
    if (nbJoueurs==0) {window.location.href='mechant.php?p='+document.getElementById('partie').value;}
    document.getElementById('ajaxLoad').style.display='none';
    document.getElementById('ajaxSave').style.display='none';}}}

function ajaxManigancesMenu () {
  if (ajaxReq.readyState === XMLHttpRequest.DONE) {
  	if (ajaxReq.status === 200) {
  	var xmlDoc = ajaxReq.responseXML;
    var manigances = xmlDoc.getElementsByTagName('manigance');
    var manigancesList = Array.prototype.slice.call(manigances);
    document.getElementById('newManiganceId').innerHTML='';
    manigancesList.forEach(function(value,index,array) {document.getElementById('newManiganceId').innerHTML+='<option value="'+value.getAttribute('maId')+'">'+value.getAttribute('maNom')+'</option>';});
    document.getElementById('newManiganceId').style.display='inline-block';}}}

function ajaxPhase () {
  if (ajaxReq.readyState === XMLHttpRequest.DONE) {
  	if (ajaxReq.status === 200) {
  	var xmlDoc = ajaxReq.responseXML;
  	document.getElementById('changePhaseMechant').innerText=xmlDoc.getElementsByTagName('phase')[0].getAttribute('mNom');
  	var changePhaseNext=parseInt(xmlDoc.getElementsByTagName('phase')[0].getAttribute('pMechPhase'))+1;
  	if (changePhaseNext==4) {changePhaseNext=1;}
  	document.getElementById('changePhaseNext').innerText=changePhaseNext;
  	var changePhaseVie=xmlDoc.getElementsByTagName('phase')[0].getAttribute('mVieMax'+changePhaseNext)*xmlDoc.getElementsByTagName('phase')[0].getAttribute('nbJoueurs');
  	document.getElementById('changePhaseVie').innerText=changePhaseVie;
  	document.getElementById('changePhase').style.display='block';}}}
  
function ajaxJoueurSet() {
  if (ajaxReq.readyState === XMLHttpRequest.DONE) {
  	if (ajaxReq.status === 200) {
  	  var xmlDoc = ajaxReq.responseXML;
      var vieMechant=xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pMechVie');
      if (vieMechant<10) {vieMechant="0"+vieMechant;}
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jId')==xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pPremier')) {
      	document.getElementById('joueurFirst').className='on';} else {
      	document.getElementById('joueurFirst').className='off';}
      document.getElementById('mechantPicJoueur').style.background='url(img/mechants/'+xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pMechant')+'.png) no-repeat center';
      document.getElementById('mechantLifeJoueur').innerText=vieMechant;
      document.getElementById('phaseMechantJoueur').innerText=phMechant(xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pMechPhase'))
      document.getElementById('picJoueur').style.background='url(img/heros/'+xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jHeros')+'.png) no-repeat center';
      document.getElementById('mechantPicJoueur').classList.remove('mechantDesJ','mechantSonJ','mechantTenJ');
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pMechDesoriente')==1) { document.getElementById('mechantPicJoueur').classList.add('mechantDesJ');}
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pMechSonne')==1) {document.getElementById('mechantPicJoueur').classList.add('mechantSonJ');}
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('pMechTenace')==1) {document.getElementById('mechantPicJoueur').classList.add('mechantTenJ');}
      var joueur=xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jNom');
      document.title='Marvel Champions - '+joueur;
      var vie=xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jVie');
      if (vie<10) {vie="0"+vie;}
      var etat=xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jStatut');
      if (etat=='AE') {etat='Alter-Égo';} else {etat='Super-Héros';}
      var jId=xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jId');
      document.getElementById('joueur').innerText=joueur;
      document.getElementById('vieJoueur').innerText=vie;
      document.getElementById('etatJoueur').innerText=etat;
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jDesoriente')==0) {document.getElementById('desJoueur').className='disabledJoueur';} else {document.getElementById('desJoueur').className='desJoueur';}
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jSonne')==0) {document.getElementById('sonJoueur').className='disabledJoueur';} else {document.getElementById('sonJoueur').className='sonJoueur';}
      if (xmlDoc.getElementsByTagName('joueur')[0].getAttribute('jTenace')==0) {
      	document.getElementById('vieJoueurMoins').className='vieJoueur';
      	document.getElementById('tenJoueur').className='disabledJoueur';}
      else {
      	document.getElementById('vieJoueurMoins').className='vieJoueurRed';
      	document.getElementById('tenJoueur').className='tenJoueur';}
    document.getElementById('ajaxLoad').style.display='none';  
    document.getElementById('ajaxSave').style.display='none';}}}
  
function ajaxMainSet() {
  if (ajaxReq.readyState === XMLHttpRequest.DONE) {
  	if (ajaxReq.status === 200) {
  	var xmlDoc = ajaxReq.responseXML;
  	var partie=xmlDoc.getElementsByTagName('partie')[0];
    var nbJoueurs=xmlDoc.getElementsByTagName('joueur').length;
    var vieMechant=partie.getAttribute('pMechVie');
    var premier=partie.getAttribute('pPremier');
    if (vieMechant<10) {vieMechant="0"+vieMechant;}
    document.getElementById('mechantRiposte').className='mechantRiposte'+partie.getAttribute('pMechRiposte');
    document.getElementById('mechantPercant').className='mechantPercant'+partie.getAttribute('pMechPercant');
    document.getElementById('mechantDistance').className='mechantDistance'+partie.getAttribute('pMechDistance');
    document.getElementById('NewPrincipaleButton').innerText=xmlDoc.getElementsByTagName('principale')[0].getAttribute('mpNom');
    document.getElementById('mechantPic').src='img/mechants/'+partie.getAttribute('pMechant')+'.png';
    document.getElementById('mechantLife').innerText=vieMechant;
    document.getElementById('mechant').innerText=partie.getAttribute('mNom');
    var maniCourant=partie.getAttribute('pManiCourant');
    if (maniCourant<10) {maniCourant='0'+maniCourant;}
    document.getElementById('manigancePri').innerText=maniCourant;
    var maniMax=partie.getAttribute('pManiMax');
    if (maniMax<10) {maniMax='0'+maniMax;}
    document.getElementById('manigancePri').innerText=maniCourant;
    document.getElementById('manigancePriMax').innerText=maniMax;
    var maniganceAcc=partie.getAttribute('pManiAcceleration');
    var Decks=xmlDoc.getElementsByTagName('deck');
    var DeckList=Array.prototype.slice.call(Decks);
    var newDecks="<option value='0'>--Choisissez le deck--</option>";
    var newDeckSeparation=false;
    //document.getElementById('deck').innerHTML=document.getElementById('deckOptionsInit').value;
    DeckList.forEach(function(value,index,array) {
      if (!newDeckSeparation && value.getAttribute('dId').charAt(0)=='h') {
        newDeckSeparation=true;
        newDecks+='<option disabled>Manigances des héros</option>';}
      newDecks+='<option value="'+value.getAttribute('dId')+'">'+value.getAttribute('dNom')+'</option>';});
    if (document.getElementById('NewManigance').style.display!='block') {document.getElementById('deck').innerHTML=newDecks;}
    var manigances = xmlDoc.getElementsByTagName('manigance');
    var manigancesList = Array.prototype.slice.call(manigances);
    document.getElementById('manigancesAnnexes').innerHTML='';
    document.getElementById('maCrise').className='vieBtn';
    manigancesList.forEach(function(value,index,array) {
      maniAnnexe='"><input class="vieBtn" type="button" value="<" onclick="document.getElementById(\'MA'+value.getAttribute('maId')+'\').innerText-=1;ajaxPost(\'MA='+value.getAttribute('maId')+'&menace\',document.getElementById(\'MA'+value.getAttribute('maId')+'\').innerText);">';
      menaceToDisplay=value.getAttribute('mnMenace');
      if (menaceToDisplay<10) {menaceToDisplay='0'+menaceToDisplay;}
      maniAnnexe+='<div class="MA" id="MA'+value.getAttribute('maId')+'">'+menaceToDisplay+'</div>';
      maniAnnexe+='<input class="vieBtn" type="button" value=">" onclick="document.getElementById(\'MA'+value.getAttribute('maId')+'\').innerText=parseInt(document.getElementById(\'MA'+value.getAttribute('maId')+'\').innerText)+1;ajaxPost(\'MA='+value.getAttribute('maId')+'&menace\',document.getElementById(\'MA'+value.getAttribute('maId')+'\').innerText);">';
      maniAnnexe+='<div class="tooltip">'+value.getAttribute('maNom')+'<span class="tooltiptext">';
      if (value.getAttribute('maDeck')==0) {maniAnnexe+=value.getAttribute('hNom');}
      else {
        maniAnnexe+=value.getAttribute('dNom');
        if (value.getAttribute('maNumero')!=0) maniAnnexe+=' N°'+value.getAttribute('maNumero');}
      maniAnnexe+='</div><div class="maniganceIcones">';
      if (value.getAttribute('maCrise')==1) {
        maniAnnexe+='<img src="img/MenaceCrise.png" alt="Crise"/>';
        document.getElementById('maCrise').className='vieBtnRed';}
      if (value.getAttribute('maRencontre')>0) for (let pas=0;pas<value.getAttribute('maRencontre');pas++) maniAnnexe+='<img src="img/MenaceRencontre.png" alt="Rencontre"/>';
      if (value.getAttribute('maAcceleration')>0) {
        for (let pas=0;pas<value.getAttribute('maAcceleration');pas++) maniAnnexe+='<img src="img/MenaceAcceleration.png" alt="Accelération"/>';
        maniganceAcc=parseInt(maniganceAcc)+parseInt(value.getAttribute('maAcceleration'));}
      if (value.getAttribute('maAmplification')>0) for (let pas=0;pas<value.getAttribute('maAmplification');pas++) maniAnnexe+='<img src="img/amplification.png" alt="Accelération"/>';
      document.getElementById('manigancesAnnexes').innerHTML+='<div class="maniganceLine'+maniAnnexe+'</div></div>';});
    var compteurs = xmlDoc.getElementsByTagName('compteur');
    var compteursList = Array.prototype.slice.call(compteurs);
    document.getElementById('compteursList').innerHTML='';
    compteursList.forEach(function(value,index,array) {
      compteur='<input class="vieBtn" type="button" value="<" onclick="document.getElementById(\'compteur'+value.getAttribute('cId')+'\').innerText-=1;ajaxPost(\'compteur='+value.getAttribute('cId')+'&value\',document.getElementById(\'compteur'+value.getAttribute('cId')+'\').innerText);">';
      compteurToDisplay=value.getAttribute('cValeur');
      if (compteurToDisplay<10) {compteurToDisplay='0'+compteurToDisplay;}
      compteur+='<div class="compteur" id="compteur'+value.getAttribute('cId')+'">'+compteurToDisplay+'</div>';
      compteur+='<input class="vieBtn" type="button" value=">" onclick="document.getElementById(\'compteur'+value.getAttribute('cId')+'\').innerText=parseInt(document.getElementById(\'compteur'+value.getAttribute('cId')+'\').innerText)+1;ajaxPost(\'compteur='+value.getAttribute('cId')+'&value\',document.getElementById(\'compteur'+value.getAttribute('cId')+'\').innerText);">';
      compteur+='<a class="compteurMoins" onclick="ajaxPost(\'delCompteur\',\''+value.getAttribute('cId')+'\');">-</a>';
      document.getElementById('compteursList').innerHTML+='<li class="compteurLine">'+compteur+'</li>';});
    document.getElementById('phaseMechant').innerText=phMechant(partie.getAttribute('pMechPhase'))
    if (partie.getAttribute('pMechDesoriente')==0) { document.getElementById('mechantDesoriente').className='disabledButtonMechant bouton';} else {document.getElementById('mechantDesoriente').className='desorienteMechant';}
    if (partie.getAttribute('pMechSonne')==0) {document.getElementById('mechantSonne').className='disabledButtonMechant bouton';} else {document.getElementById('mechantSonne').className='sonneMechant';}
    if (partie.getAttribute('pMechTenace')==0) {
    	document.getElementById('mechantTenace').className='disabledButtonMechant bouton';
    	document.getElementById('vieMechantMoins').className='vieBtn';}
    else {
      document.getElementById('mechantTenace').className='tenaceMechant';
      document.getElementById('vieMechantMoins').className='vieBtnRed';}
    for (i=1;i<=parseInt(nbJoueurs);i++) {
      var jDoc=xmlDoc.getElementsByTagName('joueur')[i-1];
      var jId=jDoc.getAttribute('jId');
      var numero=parseInt(jDoc.getAttribute('jNumero'));
      var joueur=jDoc.getAttribute('jNom');
      var vie=jDoc.getAttribute('jVie');
      if (vie<10) {vie="0"+vie;}
      var etat=jDoc.getAttribute('jStatut');
      if (etat=='AE') {etat='Alter-Égo';} else {etat='Super-Héros';}
      if (jDoc.getAttribute('jDesoriente')==0) { document.getElementById('desoriente'+numero).className='disabledButton';} else {document.getElementById('desoriente'+numero).className='desoriente';}
      if (jDoc.getAttribute('jSonne')==0) {document.getElementById('sonne'+numero).className='disabledButton';} else {document.getElementById('sonne'+numero).className='sonne';}
      if (jDoc.getAttribute('jTenace')==0) {document.getElementById('tenace'+numero).className='disabledButton';} else {document.getElementById('tenace'+numero).className='tenace';}
      if (Date.now()-jDoc.getAttribute('jOnline')>5000) {
      	joueurOnline=false;
        document.getElementById('picJoueur'+numero).src='img/heros/'+jDoc.getAttribute('jHeros')+'.png';
      	document.getElementById('online'+numero).style.display='none';
      	document.getElementById('vieDisp'+numero).innerHTML='<input class="vieBtn" type="button" value="<" onclick="document.getElementById(\'vie'+numero+'\').innerText-=1;ajaxPost(\'j='+jId+'&vieJoueur\',document.getElementById(\'vie'+numero+'\').innerText);">';
      	document.getElementById('vieDisp'+numero).innerHTML+='<div id="vie'+numero+'" class="playerLife">'+vie+'</div>'
      	document.getElementById('vieDisp'+numero).innerHTML+='<input class="vieBtn" type="button" value=">" onclick="document.getElementById(\'vie'+numero+'\').innerText=parseInt(document.getElementById(\'vie'+numero+'\').innerText)+1;ajaxPost(\'j='+jId+'&vieJoueur\',document.getElementById(\'vie'+numero+'\').innerText);">';
      	document.getElementById('joueur'+numero+'Etat').innerHTML='<div class="etatJoueur" onclick="ajaxPost(\'j='+jId+'&switch\',\'etat\');"><span>'+etat+'</span></div>';
      	document.getElementById('desoriente'+numero).setAttribute('onclick','ajaxPost("j='+jId+'&switch","desoriente");');
      	document.getElementById('desoriente'+numero).className+=' bouton';
      	document.getElementById('sonne'+numero).setAttribute('onclick','ajaxPost("j='+jId+'&switch","sonne");');
      	document.getElementById('sonne'+numero).className+=' bouton';
      	document.getElementById('tenace'+numero).setAttribute('onclick','ajaxPost("j='+jId+'&switch","tenace");');
      	document.getElementById('tenace'+numero).className+=' bouton';
      	document.getElementById('joueur'+numero).setAttribute('onclick','document.getElementById("changeNameId").value=\''+jId+'\';document.getElementById("changeNameOld").innerText=\''+joueur+'\';document.getElementById("changeNameIndex").style.display="block";document.getElementById("playerName").focus();');
      	document.getElementById('joueur'+numero).className='playerNameBouton';}
      else {
      	joueurOnline=true;
      	document.getElementById('joueur'+numero+'Etat').innerText=etat;
      	document.getElementById('online'+numero).style.display='block';
      	document.getElementById('vieDisp'+numero).innerHTML='<div id="vie'+numero+'" class="playerLife">'+vie+'</div>';
      	document.getElementById('desoriente'+numero).setAttribute('onclick','');
      	document.getElementById('sonne'+numero).setAttribute('onclick','');
      	document.getElementById('tenace'+numero).setAttribute('onclick','');
      	document.getElementById('joueur'+numero).className='playerName';}
      if (jId==premier) {
      	premier=numero;
      	var suivant=parseInt(numero)+1;
      	if (suivant>nbJoueurs) {suivant=1;}
      	suivant=xmlDoc.getElementsByTagName('joueur')[suivant-1].getAttribute('jId');}
      if (document.getElementById('joueur'+numero+'Disp').style.visibility!='visible') {document.getElementById('joueur'+numero+'Disp').style.visibility='visible'}
      document.getElementById('joueur'+numero+'Numero').value=jId;
      document.getElementById('joueur'+numero).innerText=joueur;}
    document.getElementById('indexFirst').className='first'+premier;
    document.getElementById('indexFirst').setAttribute('onclick','ajaxPost("suivant",'+suivant+');');
    document.getElementById('maniganceAcc').innerText=maniganceAcc;
    document.getElementById('ajaxLoad').style.display='none';
    document.getElementById('ajaxSave').style.display='none';
    if (nbJoueurs<4 && document.getElementById('joueur4Disp').style.visibility!='hidden') {document.getElementById('joueur4Disp').style.visibility='hidden'}
    if (nbJoueurs<3 && document.getElementById('joueur3Disp').style.visibility!='hidden') {document.getElementById('joueur3Disp').style.visibility='hidden'}
    if (nbJoueurs<2 && document.getElementById('joueur2Disp').style.visibility!='hidden') {document.getElementById('joueur2Disp').style.visibility='hidden'}
    if (nbJoueurs<1 && document.getElementById('joueur1Disp').style.visibility!='hidden') {document.getElementById('joueur1Disp').style.visibility='hidden'}
    if (nbJoueurs<2) {document.getElementById('indexFirst').style.visibility='hidden';}
 
}}}

function ajaxMechantSet() {
  if (ajaxReq.readyState === XMLHttpRequest.DONE) {
  	if (ajaxReq.status === 200) {
  	var xmlDoc = ajaxReq.responseXML;
  	var partie=xmlDoc.getElementsByTagName('partie')[0];
    var vieMechant=partie.getAttribute('pMechVie');
    if (vieMechant<10) {vieMechant="0"+vieMechant;}
    document.getElementById('mechantRiposteME').className='mechantRiposte'+partie.getAttribute('pMechRiposte');
    document.getElementById('mechantPercantME').className='mechantPercant'+partie.getAttribute('pMechPercant');
    document.getElementById('mechantDistanceME').className='mechantDistance'+partie.getAttribute('pMechDistance');
    document.getElementById('mechantPicME').src='img/mechants/'+partie.getAttribute('pMechant')+'.png';
    document.getElementById('mechantLifeME').innerText=vieMechant;
    document.getElementById('mechantME').innerText=partie.getAttribute('mNom');
    document.getElementById('phaseMechantME').innerText=phMechant(partie.getAttribute('pMechPhase'))
    if (partie.getAttribute('pMechDesoriente')==0) { document.getElementById('mechantDesorienteME').className='disabledButtonMechantME bouton';} else {document.getElementById('mechantDesorienteME').className='desorienteMechantME bouton';}
    if (partie.getAttribute('pMechSonne')==0) {document.getElementById('mechantSonneME').className='disabledButtonMechantME bouton';} else {document.getElementById('mechantSonneME').className='sonneMechantME';}
    if (partie.getAttribute('pMechTenace')==0) {
    	document.getElementById('mechantTenaceME').className='disabledButtonMechantME bouton';
    	document.getElementById('vieMechantMoinsME').className='vieBtnME';}
    else {
      document.getElementById('mechantTenaceME').className='tenaceMechantME';
      document.getElementById('vieMechantMoinsME').className='vieBtnRedME';}
    document.getElementById('ajaxLoad').style.display='none';
    document.getElementById('ajaxSave').style.display='none';
 
}}}

function phMechant (phase) {
  switch (phase) {
      case '2':
        return 'II';
      break;
      case '3':
        return 'III';
      break;
      default:
    return 'I';}}