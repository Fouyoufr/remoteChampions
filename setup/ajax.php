<?php
include 'include.php';
global $str;

if (isset($_GET['jGet'])) {
  $jId=htmlspecialchars($_GET['jGet']);
  sql_get("UPDATE `joueurs` SET `jOnline`=current_timestamp() WHERE `jId`='$jId'");
  $sqlJoueur=sql_get("SELECT * FROM parties, joueurs WHERE `jId`='$jId' AND `pUri`=`jPartie`");
  $xml = new XMLWriter();
  $xml->openURI("php://output");
  $xml->startDocument();
  $xml->setIndent(true);
  $xml->startElement('ajax');
    $xml->startElement('joueur');
    foreach (mysqli_fetch_assoc($sqlJoueur) as $cleJoueur => $valJoueur) {$xml->writeAttribute($cleJoueur, $valJoueur);}
  $xml->endElement();
  $xml->endElement();
  header('Content-type: text/xml');
  $xml->flush();}

if (isset($_GET['phase'])) {
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  $phaseSql=sql_get("SELECT `mVieMax1`,`mVieMax2`,`mVieMax3`,`mNom` FROM `mechants` WHERE `mId`=".$xml['pMechant']);
  $phase=mysqli_fetch_assoc($phaseSql);
  $phase['pMechPhase']=$xml['pMechPhase'];
  $xml = new XMLWriter();
  $xml->openURI("php://output");
  $xml->startDocument();
  $xml->setIndent(true);
  $xml->startElement('ajax');
    $xml->startElement('phase');
    foreach ($phase as $clePhase => $valPhase) {$xml->writeAttribute($clePhase, $valPhase);}
    $xml->writeAttribute('nbJoueurs', $nbJoueurs);
    $xml->endElement();
  $xml->endElement();
  header('Content-type: text/xml');
  $xml->flush();}

if (isset($_POST['phase'])) {
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  $phase=htmlspecialchars($_POST['phase']);
  $mechant=sql_get("SELECT `mVieMax1`,`mVieMax2`,`mVieMax3` FROM `mechants` WHERE `mId`=".$xml['pMechant']);
  $mechant=mysqli_fetch_assoc($mechant);
  $vieMax=$mechant['mVieMax'.$phase]*$nbJoueurs;
  $xml['pMechPhase']=$phase;
  $xml['pMechVie']=$vieMax;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `parties` SET `pMechPhase`='$phase', `pMechVie`='$vieMax' WHERE `pUri`='$partieId'");}

if (isset($_POST['mechant'])) {
  $mechant=htmlspecialchars($_POST['mechant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $nbJoueurs=$xml->joueur->count();
  if ($nbJoueurs>0) {$premier=mt_rand(1,$nbJoueurs);} else {
  	$premier=0;
  	$nbJoueurs=1;}
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jNumero']==$premier) {$premier=$jValue['jId'];}
  $mechantSql=mysqli_fetch_assoc(sql_get("SELECT `mVieMax1`,`mNom` FROM `mechants` WHERE `mId`='$mechant'"));
  $vieMechant=$mechantSql['mVieMax1']*$nbJoueurs;
  sql_get("UPDATE `parties` SET `pMechDesoriente`=0, `pMechSonne`=0, `pMechTenace`=0, `pMechPhase`='1', `pMechVie`='$vieMechant', `pMechant`='$mechant',`pPremier`='$premier',`pManiPrincipale`='0',`pManiCourant`='0',`pManiMax`='0' WHERE `pUri`='$partieId'");
  $xml['pMechant']=$mechant;
  $xml['pMechVie']=$vieMechant;
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
  $sqlReq="UPDATE `joueurs` SET `jHeros`='$heros', `jVie`='".$newHeros['hVie']."' WHERE `jPartie`='$partieId' AND ";
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
  if (isset($_POST['joueurNum'])) {$sqlReq.='`jNumero`=\''.$_POST['joueurNum'].'\'';} else {$sqlReq.='`jId`=\''.$_POST['joueur'].'\'';}
  $xml->saveXML('ajax/'.$partieId.'.xml');  
  sql_get($sqlReq);}

if(isset($_POST['vieMechant'])) {
  $vieMechant=htmlspecialchars($_POST['vieMechant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pMechVie']=$vieMechant;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `parties` SET `pMechVie`='$vieMechant' WHERE `pUri`='$partieId'");}

if(isset($_POST['switch'])) {
  $switch=htmlspecialchars($_POST['switch']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  switch ($switch) {
    case 'mechantDesoriente':
        sql_get("UPDATE `parties` SET `pMechDesoriente`=!`pMechDesoriente` WHERE `pUri`='$partieId'");
        $xml['pMechDesoriente']=1-$xml['pMechDesoriente'];
        break;
    case 'mechantSonne':
        sql_get("UPDATE `parties` SET `pMechSonne`=!`pMechSonne` WHERE `pUri`='$partieId'");
        $xml['pMechSonne']=1-$xml['pMechSonne'];
        break;
    case 'mechantTenace':
        sql_get("UPDATE `parties` SET `pMechTenace`=!`pMechTenace` WHERE `pUri`='$partieId'");
        $xml['pMechTenace']=1-$xml['pMechTenace'];
        break;
    case 'desoriente':
    	sql_get("UPDATE `joueurs` SET `jDesoriente`=!`jDesoriente` WHERE `jId`='$joueurId'");
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jDesoriente']=1-$jValue['jDesoriente'];}
        break;
    case 'sonne':
    	sql_get("UPDATE `joueurs` SET `jSonne`=!`jSonne` WHERE `jId`='$joueurId'");
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jSonne']=1-$jValue['jSonne'];}
        break;
    case 'tenace':
    	sql_get("UPDATE `joueurs` SET `jTenace`=!`jTenace` WHERE `jId`='$joueurId'");
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jTenace']=1-$jValue['jTenace'];}
        break;
    case 'etat':
    	$etat=sql_get("SELECT `jStatut` FROM `joueurs` WHERE `jId`='$joueurId'");
    	$etat=mysqli_fetch_assoc($etat);
    	if ($etat['jStatut']=='AE') {$etat='SH';} else {$etat='AE';}
      sql_get("UPDATE `joueurs` SET `jStatut`='$etat' WHERE `jId`='$joueurId'");
      foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {
        if ($jValue['jStatut']=='AE') {$jValue['jStatut']='SH';} else {$jValue['jStatut']='AE';}}
      break;
    case 'premier':
    	sql_get("UPDATE `parties` SET `pPremier`='$joueurId' WHERE `pUri`='$partieId'");
      $xml['pPremier']=$joueurId;
      break;
    case 'mechantRiposte':
    	sql_get("UPDATE `parties` SET `pMechRiposte`=!`pMechRiposte` WHERE `pUri`='$partieId'");
      $xml['pMechRiposte']=1-$xml['pMechRiposte'];
    	break;
    case 'mechantPercant':
    	sql_get("UPDATE `parties` SET `pMechPercant`=!`pMechPercant` WHERE `pUri`='$partieId'");
      $xml['pMechPercant']=1-$xml['pMechPercant'];
    	break;
    case 'mechantDistance':
    	sql_get("UPDATE `parties` SET `pMechDistance`=!`pMechDistance` WHERE `pUri`='$partieId'");
      $xml['pMechDistance']=1-$xml['pMechDistance'];
    	break;}
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if (isset($_POST['boite'])) {
  $boite=htmlspecialchars($_POST['boite']);
  $inclus=htmlspecialchars($_POST['inclus']);
  sql_get("UPDATE `boites` SET `bInclus`=$inclus WHERE `bId`='$boite'");}

if(isset($_POST['suivant'])) {
  $joueur=htmlspecialchars($_POST['suivant']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pPremier']=$joueur;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `parties` SET `pPremier`='$joueur' WHERE `pUri`='$partieId'");}

if(isset($_POST['changeName'])) {
  $joueur=htmlspecialchars($_POST['changeName']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jNom']=$joueur;}
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `joueurs` SET `jNom`='$joueur' WHERE `jId`='$joueurId'");}

if(isset($_POST['vieJoueur'])) {
  $vie=htmlspecialchars($_POST['vieJoueur']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  foreach ($xml->joueur as $jId=>$jValue) if ($jValue['jId']==$joueurId) {$jValue['jVie']=$vie;}
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `joueurs` SET `jVie`='$vie' WHERE `jId`='$joueurId'");}

if(isset($_POST['manigance'])) {
  $manigance=htmlspecialchars($_POST['manigance']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiCourant']=$manigance;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `parties` SET `pManiCourant`='$manigance' WHERE `pUri`='$partieId'");}

if(isset($_POST['maniganceMax'])) {
  $manigance=htmlspecialchars($_POST['maniganceMax']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiMax']=$manigance;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `parties` SET `pManiMax`='$manigance' WHERE `pUri`='$partieId'");}

if(isset($_POST['maniganceAcc'])) {
  $maniAccel=mysqli_num_rows(sql_get("SELECT `maId` FROM `manigances`,`maniAnnexes` WHERE `mnPartie`='$partieId' AND `mnManigance`=`maId` AND `maAcceleration`='1'"));
  $manigance=htmlspecialchars($_POST['maniganceAcc'])-$maniAccel;
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiAcceleration']=$manigance;
  $xml->saveXML('ajax/'.$partieId.'.xml');
  sql_get("UPDATE `parties` SET `pManiAcceleration`='$manigance' WHERE `pUri`='$partieId'");}

if(isset($_POST['newManigance'])) {
  $manigance=htmlspecialchars($_POST['newManigance']);
  $maniDetail=mysqli_fetch_assoc(sql_get("SELECT * FROM `manigances` WHERE `maId`='$manigance'"));
  $maInit=$maniDetail['maInit'];
  if ($maniDetail['maMultiplie']==true) {
  	 $nbJoueurs=mysqli_num_rows(sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'"));
  	 $maInit=$maInit*$nbJoueurs;}
  if ($maniDetail['maEntrave']!=0) {
     $nbJoueurs=mysqli_num_rows(sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'"));
     $maInit=$maInit+$nbJoueurs*$maniDetail['maEntrave'];}
  sql_get("INSERT INTO `maniAnnexes` (`mnPartie`,`mnManigance`,`mnMenace`) VALUES ('$partieId','$manigance','$maInit')");
  sql_get("UPDATE `parties` SET `pManiDelete`='0' WHERE `pUri`='$partieId'");
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiDelete']=0;
  foreach ($xml->deck as $maniDeck) {
    $nodesToDelete=array();
    foreach ($maniDeck->maniChoice as $mValue) if ($mValue['maId']==$manigance) {
      $nodeToDelete=$mValue; //supprimer manigance des choix possibles.
      $fromDeckId=$maniDeck['dId'];
      $fromDeckNom=$maniDeck['dNom'];}}
    unset($nodeToDelete[0]);
  //Ajouter nouvelle manigance en jeu...
  $xmlManigance=$xml->addChild('manigance');
  xmlAttr($xmlManigance,array('maId'=>$manigance,'maNom'=>$maniDetail['maNom'],'mnMenace'=>$maInit,'fromDeckId'=>$fromDeckId,'fromDeckNom'=>$fromDeckNom,'maRevele'=>$maniDetail['maRevele'],'maDejoue'=>$maniDetail['maDejoue'],'maInfo'=>$maniDetail['maInfo'],'maNumero'=>$maniDetail['maNumero'],'maCrise'=>$maniDetail['maCrise'],'maRencontre'=>$maniDetail['maRencontre'],'maAcceleration'=>$maniDetail['maAcceleration'],'maAmplification'=>$maniDetail['maAmplification'],'maDeck'=>$maniDetail['maDeck']));
  if ($maniDetail['maDeck']==0) {
    foreach($xml->joueur as $maniJoueur) if ($maniDetail['maNumero']==$maniJoueur['jHeros']) {
      xmlAttr($xmlManigance,array('hNom'=>$maniJoueur['hNom']));}}
  else {xmlAttr($xmlManigance,array('dNom'=>deckNames()[$maniDetail['maDeck']]));}
  foreach ($xml->deck as $maniDeck) if ($maniDeck->maniChoice->count()==0) {$nodesToDelete[]=$maniDeck;} //Plus de manigance sur ce deck !
  foreach ($nodesToDelete as $nodeToDelete) {unset($nodeToDelete[0]);} // Suppression du deck vide.
  $xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['MA'])) {
  $manigance=htmlspecialchars($_POST['MA']);
  $menace=htmlspecialchars($_POST['menace']);
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $maniDelete=0;
  if ($menace<1) {
    foreach ($xml->manigance as $maniXML) if ($maniXML['maId']==$manigance) {$nodeToDelete=$maniXML;}
    if ($nodeToDelete['maDejoue']<>'') {
      $xml['maniDelete']=$nodeToDelete['maDejoue'];
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
    if (isset($nodeToDelete)) {unset($nodeToDelete[0]);}
    sql_get("DELETE FROM `maniAnnexes` WHERE `mnPartie`='$partieId' AND `mnManigance`='$manigance'");
    if (mysqli_fetch_assoc(sql_get("SELECT * FROM `manigances` WHERE `maId`='$manigance'"))['maDejoue']<>'') {sql_get("UPDATE `parties` SET `pManiDelete`='$manigance' WHERE `pUri`='$partieId'");}
  else {sql_get("UPDATE `parties`SET `pManiDelete`='0' WHERE `pUri`='$partieId'");}}
  else {
    sql_get("UPDATE `maniAnnexes` SET `mnMenace`='$menace' WHERE `mnPartie`='$partieId' AND `mnManigance`='$manigance'");
    sql_get("UPDATE `parties`SET `pManiDelete`='0' WHERE `pUri`='$partieId'");
    foreach ($xml->manigance as $maniXML) if ($maniXML['maId']==$manigance) {$maniXML['mnMenace']=$menace;}}
$xml['pManiDelete']=$maniDelete;
$xml->saveXML('ajax/'.$partieId.'.xml');}

if(isset($_POST['NewPrincipale'])) {
  $manigance=htmlspecialchars($_POST['NewPrincipale']);
  $maniDetail=mysqli_fetch_assoc(sql_get("SELECT * FROM `ManigancesPrincipales` WHERE `mpId`='$manigance'"));
  $mpMax=$maniDetail['mpMax'];
  if ($maniDetail['mpMaxMultiplie']==true) {
    $nbJoueurs=mysqli_num_rows(sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'"));
    $mpMax=$mpMax*$nbJoueurs;}
  $mpInit=$maniDetail['mpInit'];
  if ($maniDetail['mpMultiplie']==true) $mpInit=$mpInit*$nbJoueurs;
  sql_get("UPDATE `parties` SET `pManiMax`='$mpMax', `pManiCourant`='$mpInit', `pManiPrincipale`='$manigance' WHERE `pUri`='$partieId'");
  $xml=simplexml_load_file('ajax/'.$partieId.'.xml');
  $xml['pManiMax']=$mpMax;
  $xml['pManiCourant']=$mpInit;
  $xml['pManiPrincipale']=$manigance;
  $xml['mpNom']=$maniDetail['mpNom'];
  $xml->saveXML('ajax/'.$partieId.'.xml');}

//A traiter en mode cache
if(isset($_POST['addCompteur'])) {sql_get("INSERT INTO `compteurs` (`cPartie`) VALUES ('$partieId')");}

if (isset($_POST['delCompteur'])) {
  $compteur=htmlspecialchars($_POST['delCompteur']);
  sql_get("DELETE FROM `compteurs` WHERE `cId`='$compteur'");}

if(isset($_POST['compteur'])) {
  $compteur=htmlspecialchars($_POST['compteur']);
  $value=htmlspecialchars($_POST['value']);
  sql_get("UPDATE `compteurs` SET `cValeur`='$value' WHERE `cId`='$compteur'");}
?>