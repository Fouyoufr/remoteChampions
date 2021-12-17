<?php
include 'include.php';
include_once 'maniganceInfo.php';

global $str;
if (isset($_POST['phase'])) {
  #Changement de phase du méchant
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  $phase=htmlspecialchars($_POST['phase']);
  $mechant=sql_get("SELECT `mVieMax1`,`mVieMax2`,`mVieMax3` FROM `mechants` WHERE `mId`=".$xml['pMechant']);
  $mechant=mysqli_fetch_assoc($mechant);
  $vieMax=$mechant['mVieMax'.$phase]*$nbJoueurs;
  $nextPhase=$phase+1;
  if ($nextPhase==4) $nextPhase=1;
  $xml['pMechPhase']=$phase;
  $xml['pMechVie']=$vieMax;
  $xml['nextPhaseVie']=$mechant['mVieMax'.$nextPhase];
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if (isset($_POST['mechant'])) {
  #Changement de méchant
  $mechant=htmlspecialchars($_POST['mechant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  if ($nbJoueurs>0) {$premier=mt_rand(1,$nbJoueurs);} else {
  	$premier=0;
  	$nbJoueurs=1;}
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jNumero']==$premier) {$premier=$jValue['jId'];}
  $mechantSql=mysqli_fetch_assoc(sql_get("SELECT `mVieMax1`,`mVieMax2`,`mNom` FROM `mechants` WHERE `mId`='$mechant'"));
  $vieMechant=$mechantSql['mVieMax1']*$nbJoueurs;
  $xml['pMechant']=$mechant;
  $xml['pMechVie']=$vieMechant;
  $xml['nextPhaseVie']=$mechantSql['mVieMax2'];
  $xml['pPremier']=$premier;
  $xml['pMechDesoriente']=0;
  $xml['pMechSonne']=0;
  $xml['pMechTenace']=0;
  $xml['pMechPhase']=1;
  $xml['mNom']=$mechantSql['mNom'];
  $xml['pManiPrincipale']=0;
  $xml['pManiCourant']=0;
  $xml['pManiMax']=0;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  echo 'SelectManigance';}

if(isset($_POST['heros'])) {
  $heros=htmlspecialchars($_POST['heros']);
  $joueur=htmlspecialchars($_POST['joueur']);
  $newHeros=mysqli_fetch_assoc(sql_get("SELECT `hVie`,`hNom` FROM `heros` WHERE `hId`='$heros'"));
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ((isset($_POST['joueurNum']) and $jValue['jNumero']==$_POST['joueurNum']) or ($jValue['jId'])==$_POST['joueur']) {
      foreach ($xml->deck as $dValue) if ($dValue['dId']=='h'.$jValue['jHeros']) {$nodeToDelete=$dValue;} //supprimer ancien deck Heros...
      unset($nodeToDelete[0]);
       //Ajouter nouveau deck Héros... 
      $xmlDeck=$xml->addChild('deck');
      xmlAttr($xmlDeck,array('dId'=>'h'.$heros,'dNom'=>$newHeros['hNom']));
      #récupération des manigances du héros choisi
      $sqlManigances=sql_get("SELECT `maId`,`maNom` FROM `manigances` WHERE `maDeck`='0' AND `maNumero`='$heros' ORDER BY `maNom` ASC");
      while ($manigance=mysqli_fetch_assoc($sqlManigances)) {
        $xmlMani=$xmlDeck->addChild('maniChoice');
        xmlAttr($xmlMani,array('maId'=>$manigance['maId'],'maNom'=>$manigance['maNom']));}
      $jValue['jHeros']=$heros;
      $jValue['jVie']=$newHeros['hVie'];
      $jValue['hNom']=$newHeros['hNom'];}
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['vieMechant'])) {
  $vieMechant=htmlspecialchars($_POST['vieMechant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pMechVie']=$vieMechant;
  $xml->saveXML('ajax/'.$partieId.'.xml');}

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
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if (isset($_POST['boite'])) {
  $xmlBoxes=simplexml_load_file('boxes.xml');
  $changedBoxes=false;
  if (htmlspecialchars($_POST['inclus'])=='true') $boxOwn=1; else $boxOwn=0;
  foreach ($xmlBoxes as $xmlBox) if ($xmlBox['id']==htmlspecialchars($_POST['boite']) and $xmlBox['own']<>$boxOwn) {
    $xmlBox['own']=$boxOwn;
    $changedBoxes=true;}
  if ($changedBoxes) xmlSave($xmlBoxes,'boxes.xml');}

if(isset($_POST['suivant'])) {
  $joueur=htmlspecialchars($_POST['suivant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pPremier']=$joueur;
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['changeName'])) {
  $joueur=htmlspecialchars($_POST['changeName']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jNom']=$joueur;}
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['vieJoueur'])) {
  $vie=htmlspecialchars($_POST['vieJoueur']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jVie']=$vie;}
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['manigance'])) {
  $manigance=htmlspecialchars($_POST['manigance']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiCourant']=$manigance;
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['maniganceMax'])) {
  $manigance=htmlspecialchars($_POST['maniganceMax']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiMax']=$manigance;
  $xml->saveXML('ajax/'.$partieId.'.xml');;}

if(isset($_POST['maniganceAcc'])) {
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $maniAccel=0;
  foreach ($xml->manigance as $mani) if($mani['maAcceleration']==1) {$maniAccel++;}
  $manigance=htmlspecialchars($_POST['maniganceAcc'])-$maniAccel;
  $xml['pManiAcceleration']=$manigance;
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['newManigance'])) {
  $xmlBoxes=simplexml_load_file('boxes.xml');
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
$xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['NewPrincipale'])) {
  $manigance=htmlspecialchars($_POST['NewPrincipale']);
  $maniDetail=mysqli_fetch_assoc(sql_get("SELECT * FROM `ManigancesPrincipales` WHERE `mpId`='$manigance'"));
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  $mpMax=$maniDetail['mpMax'];
  if ($maniDetail['mpMaxMultiplie']==true) {$mpMax=$mpMax*$nbJoueurs;}
  $mpInit=$maniDetail['mpInit'];
  if ($maniDetail['mpMultiplie']==true) $mpInit=$mpInit*$nbJoueurs;
  $xml['pManiMax']=$mpMax;
  $xml['pManiCourant']=$mpInit;
  $xml['pManiPrincipale']=$manigance;
  $xml['mpNom']=$maniDetail['mpNom'];
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['addCompteur'])) {
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $cId=0;
  foreach ($xml->compteur as $XMLcompteur) if ($cId<=$XMLcompteur['cId']) {$cId=$XMLcompteur['cId']+1;}
  $xmlCompteur=$xml->addChild('compteur');
  xmlAttr($xmlCompteur,array('cId'=>$cId,'cValeur'=>0));  
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if (isset($_POST['delCompteur'])) {
  $compteur=htmlspecialchars($_POST['delCompteur']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->compteur as $XMLcompteur) if ($XMLcompteur['cId']==$compteur) {$nodeToDelete=$XMLcompteur;}
  unset($nodeToDelete[0]);
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['compteur'])) {
  $compteur=htmlspecialchars($_POST['compteur']);
  $value=htmlspecialchars($_POST['value']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->compteur as $XMLcompteur) if ($XMLcompteur['cId']==$compteur) {$XMLcompteur['cValeur']=$value;}
  $xml->saveXML('ajax/'.$partieId.'.xml');}
?>