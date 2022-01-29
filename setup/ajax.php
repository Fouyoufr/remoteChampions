<?php
header('Content-Type: application/xml; charset=utf-8');
include 'include.inc';

if (isset($_POST['phase'])) {
  #Changement de phase du méchant
  $xmlBoxes=simplexml_load_file($boxFile);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  $phase=htmlspecialchars($_POST['phase']);
  foreach ($xmlBoxes->box as $xmlBox) foreach ($xmlBox->mechant as $xmlMechant) if ($xmlMechant['id']->__toString()==$xml['pMechant']->__toString()) $mechant=$xmlMechant;
  $nextPhase=$phase+1;
  echo "$hase, puis $nextPhase";
  if ($nextPhase==4) $nextPhase=1;
  if($mechant['vie'.$nextPhase]==0) $nextPhase=1;
  $xml['pMechPhase']=$phase;
  if ($nbJoueurs!=0) $xml['pMechVie']=$mechant['vie'.$phase]*$nbJoueurs; else $xml['pMechVie']=$mechant['vie'.$phase];
  $xml['nextPhase']=$nextPhase;
  $xml['nextPhaseVie']=$mechant['vie'.$nextPhase];
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if (isset($_POST['mechant'])) {
  #Changement de méchant
  $xmlBoxes=simplexml_load_file($boxFile);
  $mechant=htmlspecialchars($_POST['mechant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  if ($nbJoueurs>0) {$premier=mt_rand(1,$nbJoueurs);} else {
  	$premier=0;
  	$nbJoueurs=1;}
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jNumero']==$premier) $premier=$jValue['jId'];
  foreach ($xmlBoxes->box as $xmlBox) foreach ($xmlBox->mechant as $xmlMechant) if ($xmlMechant['id']==$mechant) $newMechant=$xmlMechant;
  $xml['pMechant']=$mechant;
  $xml['pMechVie']=$newMechant['vie1']*$nbJoueurs;
  $xml['nextPhaseVie']=$newMechant['vie2'];
  $xml['pPremier']=$premier;
  $xml['pMechDesoriente']=0;
  $xml['pMechSonne']=0;
  $xml['pMechTenace']=0;
  $xml['pMechPhase']=1;
  if($newMechant['vie2']==0) $xml['nextPhase']=1; else $xml['nextPhase']=2;
  $xml['mNom']=$newMechant['name'];
  $xml['pManiPrincipale']=0;
  $xml['pManiCourant']=0;
  $xml['pManiMax']=0;
  xmlSave($xml,'ajax/'.$partieId.'.xml');
  echo 'SelectManigance';}

if(isset($_POST['heros'])) {
  $heros=htmlspecialchars($_POST['heros']);
  $joueur=htmlspecialchars($_POST['joueur']);
  $xmlBoxes=simplexml_load_file($boxFile);
  foreach ($xmlBoxes->box as $xmlBox) foreach ($xmlBox->heros as $xmlHeros) if ($xmlHeros['id']==$heros) $newHeros=$xmlHeros;
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ((isset($_POST['joueurNum']) and $jValue['jNumero']->__toString()==$_POST['joueurNum']) or $jValue['jId']->__toString()==$_POST['joueur']) {
    foreach ($xml->deck as $dValue) if ($dValue['dId']=='h'.$jValue['jHeros']) {$nodeToDelete=$dValue;} //supprimer ancien deck Heros...
    unset($nodeToDelete[0]);
     //Ajouter nouveau deck Héros... 
    $xmlDeck=$xml->addChild('deck');
    xmlAttr($xmlDeck,array('dId'=>'h'.$heros,'dNom'=>$newHeros['name']));
    #récupération des manigances du héros choisi
    foreach ($newHeros->scheme as $newMani) {
      $xmlMani=$xmlDeck->addChild('maniChoice');
      xmlAttr($xmlMani,array('maId'=>$newMani['id'],'maNom'=>$newMani['name']));}
    $jValue['jHeros']=$heros;
    $jValue['jVie']=$newHeros['vie'];
    $jValue['hNom']=$newHeros['name'];}
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['vieMechant'])) {
  $vieMechant=htmlspecialchars($_POST['vieMechant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pMechVie']=$vieMechant;
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['switch'])) {
  $switch=htmlspecialchars($_POST['switch']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  switch ($switch) {
    case 'mechantDesoriente':
        $xml['pMechDesoriente']=1-$xml['pMechDesoriente'];
        break;
    case 'mechantSonne':
        $xml['pMechSonne']=1-$xml['pMechSonne'];
        break;
    case 'mechantTenace':
        $xml['pMechTenace']=1-$xml['pMechTenace'];
        break;
    case 'desoriente':
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jDesoriente']=1-$jValue['jDesoriente'];}
        break;
    case 'sonne':
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jSonne']=1-$jValue['jSonne'];}
        break;
    case 'tenace':
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jTenace']=1-$jValue['jTenace'];}
        break;
    case 'etat':
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {
        if ($jValue['jStatut']=='AE') {$jValue['jStatut']='SH';} else {$jValue['jStatut']='AE';}}
      break;
    case 'premier':
      $xml['pPremier']=$joueurId;
      break;
    case 'mechantRiposte':
      $xml['pMechRiposte']=1-$xml['pMechRiposte'];
    	break;
    case 'mechantPercant':
      $xml['pMechPercant']=1-$xml['pMechPercant'];
    	break;
    case 'mechantDistance':
      $xml['pMechDistance']=1-$xml['pMechDistance'];
    	break;}
    echo $xml->asXML();
    xmlSave($xml,'ajax/'.$partieId.'.xml');}

if (isset($_POST['boite'])) {
  $changedBoxes=false;
  if (htmlspecialchars($_POST['inclus'])=='true') $boxOwn=1; else $boxOwn=0;
  foreach ($rcLangs as $boxLang) {
    $xmlBoxes=simplexml_load_file($boxLang.'/boxes.xml');
    foreach ($xmlBoxes as $xmlBox) if ($xmlBox['id']==htmlspecialchars($_POST['boite']) and $xmlBox['own']<>$boxOwn) {
      $xmlBox['own']=$boxOwn;
      $changedBoxes=true;}
    if ($changedBoxes) xmlSave($xmlBoxes,$boxLang.'/boxes.xml');}}

if(isset($_POST['suivant'])) {
  $joueur=htmlspecialchars($_POST['suivant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pPremier']=$joueur;
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['changeName'])) {
  $joueur=htmlspecialchars($_POST['changeName']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jNom']=$joueur;}
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['vieJoueur'])) {
  $vie=htmlspecialchars($_POST['vieJoueur']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jVie']=$vie;}
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['manigance'])) {
  $manigance=htmlspecialchars($_POST['manigance']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiCourant']=$manigance;
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['maniganceMax'])) {
  $manigance=htmlspecialchars($_POST['maniganceMax']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiMax']=$manigance;
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['maniganceAcc'])) {
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $maniAccel=0;
  foreach ($xml->manigance as $mani) if($mani['maAcceleration']==1) {$maniAccel++;}
  $manigance=htmlspecialchars($_POST['maniganceAcc'])-$maniAccel;
  $xml['pManiAcceleration']=$manigance;
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['newManigance'])) {
  $xmlBoxes=simplexml_load_file($boxFile);
  $manigance=htmlspecialchars($_POST['newManigance']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  foreach($xmlBoxes as $xmlBox) foreach ($xmlBox->deck as $xmlDeck) foreach ($xmlDeck->scheme as $xmlScheme) if ($xmlScheme['id']==$manigance) {
    $newScheme=$xmlScheme;
    $newDeck=$xmlDeck;}
  if (!isset($newScheme)) foreach($xmlBoxes as $xmlBox) foreach ($xmlBox->heros as $xmlHeros) foreach ($xmlHeros->scheme as $xmlScheme) if ($xmlScheme['id']==$manigance) {
    $newScheme=$xmlScheme;
    $newDeck=array('id'=>'h'.$xmlHeros['id'],'name'=>$xmlHeros['name']);}
  if (isset($newScheme)) {
    $maInit=$newScheme['init'];
    if ($newScheme['initX']==1) $maInit=$maInit*$nbJoueurs;
    if ($newScheme['entrave']<>0) $maInit=$maInit+$nbJoueurs*$newScheme['entrave'];
    $xml['pManiDelete']=0;
    if (!isset($xml->lastManigance)) {
      $xml->addChild('lastManigance');
      xmlAttr($xml->lastManigance,array('id'=>$manigance,'title'=>$newScheme['name'],'text'=>$newScheme['revele']));}
    else {
      $xml->lastManigance['id']=$manigance;
      $xml->lastManigance['text']=$newScheme['revele'];}
    foreach ($xml->deck as $maniDeck) foreach ($maniDeck->maniChoice as $mValue) if ($mValue['maId']==$manigance) $nodeToDelete=$mValue; 
    unset($nodeToDelete[0]);
    //Ajouter nouvelle manigance en jeu...
    $xmlManigance=$xml->addChild('manigance');
    xmlAttr($xmlManigance,array('maId'=>$manigance,'maNom'=>$newScheme['name'],'mnMenace'=>$maInit,'fromDeckId'=>$newDeck['id'],'fromDeckNom'=>$newDeck['name'],'maRevele'=>$newScheme['revele'],'maDejoue'=>$newScheme['dejoue'],'maInfo'=>$newScheme['info'],'maNumero'=>$newScheme['card'],'maCrise'=>$newScheme['crise'],'maRencontre'=>$newScheme['rencontre'],'maAcceleration'=>$newScheme['accel'],'maAmplification'=>$newScheme['ampli'],'maDeck'=>$newDeck['id']));
    if ($newDeck['id']==0) xmlAttr($xmlManigance,array('hNom'=>$newDeck['name']));
    else xmlAttr($xmlManigance,array('dNom'=>$newDeck['name']));
  foreach ($xml->deck as $maniDeck) if ($maniDeck->maniChoice->count()==0) $nodesToDelete[]=$maniDeck; //Plus de manigance sur ce deck !
  if (isset($nodesToDelete)) foreach ($nodesToDelete as $nodeToDelete) unset($nodeToDelete[0]); // Suppression du deck vide.
  xmlSave($xml,'ajax/'.$partieId.'.xml');}}

if(isset($_POST['MA'])) {
  $manigance=htmlspecialchars($_POST['MA']);
  $menace=htmlspecialchars($_POST['menace']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $maniDelete=0;
  if ($menace<1) {
    foreach ($xml->manigance as $maniXML) if ($maniXML['maId']==$manigance) {$nodeToDelete=$maniXML;}
    if ($nodeToDelete['maDejoue']<>'') {
      $xml['maniDelete']='<h2>'.$nodeToDelete['maNom'].'</h2>'.$nodeToDelete['maDejoue'];
      $maniDelete=$manigance;}
    foreach ($xml->deck as $maniDeck) if ($maniDeck['dId']->__toString()==$nodeToDelete['fromDeckId']->__toString()) {
      //replacer la manigance dans son deck
      $foundDeck=$maniDeck->addChild('maniChoice');
      xmlAttr($foundDeck,array('maId'=>$manigance,'maNom'=>$nodeToDelete['maNom']));}
    if (!isset($foundDeck)) {
      //Nécessité de recréer le deck pour y ajouter la manigance !
      $addDeck=$xml->addChild('deck');
      xmlAttr($addDeck,array('dId'=>$nodeToDelete['fromDeckId'],'dNom'=>$nodeToDelete['fromDeckNom']));
      $addMani=$addDeck->addChild('maniChoice');
      xmlAttr($addMani,array('maId'=>$manigance,'maNom'=>$nodeToDelete['maNom']));}
    if (isset($nodeToDelete)) {unset($nodeToDelete[0]);}}
  else {
    foreach ($xml->manigance as $maniXML) if ($maniXML['maId']==$manigance) {$maniXML['mnMenace']=$menace;}}
$xml['pManiDelete']=$maniDelete;
xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['NewPrincipale'])) {
  $manigance=htmlspecialchars($_POST['NewPrincipale']);
  $xmlBoxes=simplexml_load_file($boxFile);
  foreach ($xmlBoxes->box as $xmlBox) foreach ($xmlBox->principale as $xmlPrincipale) if ($xmlPrincipale['id']==$manigance) $newPrincipale=$xmlPrincipale;
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  $mpMax=$newPrincipale['max'];
  if ($newPrincipale['maxX']==1) {$mpMax=$mpMax*$nbJoueurs;}
  $mpInit=$newPrincipale['init'];
  if ($newPrincipale['initX']==1) $mpInit=$mpInit*$nbJoueurs;
  $xml['pManiMax']=$mpMax;
  $xml['pManiCourant']=$mpInit;
  $xml['pManiPrincipale']=$manigance;
  $xml['mpNom']=$newPrincipale['name'];
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['addCompteur'])) {
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $cId=0;
  foreach ($xml->compteur as $XMLcompteur) if ($cId<=$XMLcompteur['cId']) {$cId=$XMLcompteur['cId']+1;}
  $xmlCompteur=$xml->addChild('compteur');
  xmlAttr($xmlCompteur,array('cId'=>$cId,'cValeur'=>0));  
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if (isset($_POST['delCompteur'])) {
  $compteur=htmlspecialchars($_POST['delCompteur']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->compteur as $XMLcompteur) if ($XMLcompteur['cId']==$compteur) {$nodeToDelete=$XMLcompteur;}
  unset($nodeToDelete[0]);
  xmlSave($xml,'ajax/'.$partieId.'.xml');}

if(isset($_POST['compteur'])) {
  $compteur=htmlspecialchars($_POST['compteur']);
  $value=htmlspecialchars($_POST['value']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->compteur as $XMLcompteur) if ($XMLcompteur['cId']==$compteur) {$XMLcompteur['cValeur']=$value;}
  xmlSave($xml,'ajax/'.$partieId.'.xml');}
?>