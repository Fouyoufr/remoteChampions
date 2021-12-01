<?php
include 'include.php';

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

if (isset($_GET['pGet'])) {
  $partieId=htmlspecialchars($_GET['pGet']);
  $sqlPartie=sql_get("SELECT * FROM parties, mechants WHERE mId = pMechant AND pURI='$partieId'");
  $sqlJoueurs=sql_get("SELECT * FROM joueurs WHERE jPartie='$partieId'");
  $sqlDecks=sql_get("SELECT `dId`,`dNom` FROM `manigances` LEFT JOIN `maniAnnexes` ON (`maId`=`mnManigance` AND `mnPartie`='$partieId'),`decks`,`deckParties` WHERE `maDeck`!=0 AND `dId`=`maDeck` AND `mnPartie` IS NULL AND `dpPartie`='$partieId' AND `dpDeck`=`dId` GROUP BY `dNom` ORDER BY `dNom` ASC");
  $sqlHeros=sql_get("SELECT `hId`,`hNom` FROM `manigances` LEFT JOIN `maniAnnexes` ON (`maId`=`mnManigance` AND `mnPartie`='$partieId'),`joueurs`,heros WHERE `maDeck`=0 AND `jPartie`='$partieId' AND `maNumero`=`jHeros` AND `hId`=`jHeros` AND `mnPartie` IS NULL GROUP BY `hNom` ORDER BY `hNom` ASC");
  $sqlManigances=sql_get("SELECT * FROM `maniAnnexes`,`manigances`,`decks` WHERE `mnPartie`='$partieId' AND `maId`=`mnManigance` AND `dId`=`maDeck`");
  $sqlManiHeros=sql_get("SELECT * FROM `maniAnnexes`,`manigances`,`heros` WHERE `mnPartie`='$partieId' AND `maDeck`='0' AND `maId`=`mnManigance` AND `maNumero`=`hId`");
  $sqlLastManigance=sql_get("SELECT * FROM `maniAnnexes` WHERE `mnPartie`='$partieId' AND mnDate > DATE_SUB(NOW(), INTERVAL 10 SECOND) ORDER BY `mnDate` DESC LIMIT 1");
  $sqlPrincipale=sql_get("SELECT `mpId`,`mpNom`, `mpMax` FROM `parties`, `ManigancesPrincipales` WHERE `mpID`=`pManiPrincipale` AND `pURI`='$partieId'");
  $sqlCompteurs=sql_get("SELECT * FROM `compteurs` WHERE `cPartie`='$partieId'");
  $xml = new XMLWriter();
  $xml->openURI("php://output");
  $xml->startDocument();
  $xml->setIndent(true);
  $xml->startElement('ajax');
    $xml->startElement('partie');
    foreach (mysqli_fetch_assoc($sqlPartie) as $clePartie => $valPartie) {
      $xml->writeAttribute($clePartie, $valPartie);
      if ($clePartie=='pManiDelete') {$maniDelete=$valPartie;}}
    $xml->endElement();
    $xml->startElement('principale');
    foreach (mysqli_fetch_assoc($sqlPrincipale) as $clePrincipale => $valPrincipale) {$xml->writeAttribute($clePrincipale, $valPrincipale);}
    $xml->endElement();
    while ($compteur=mysqli_fetch_assoc($sqlCompteurs)) {
      $xml->startElement('compteur');
      foreach ($compteur as $cleCompteur => $valCompteur) {$xml->writeAttribute($cleCompteur, $valCompteur);}
      $xml->endElement();}
    while ($manigance=mysqli_fetch_assoc($sqlManigances)) {
      $xml->startElement('manigance');
      foreach ($manigance as $cleManigance => $valManigance) {$xml->writeAttribute($cleManigance,$valManigance);}
      $xml->endElement();}
    while ($manigance=mysqli_fetch_assoc($sqlManiHeros)) {
        $xml->startElement('manigance');
        foreach ($manigance as $cleManigance => $valManigance) {$xml->writeAttribute($cleManigance,$valManigance);}
        $xml->endElement();}
    while ($joueur=mysqli_fetch_assoc($sqlJoueurs)) {
      $xml->startElement('joueur');
      foreach ($joueur as $clePartie => $valPartie) {
      	if ($clePartie=='jOnline') {$valPartie=strtotime($valPartie)*1000;}
      	$xml->writeAttribute($clePartie, $valPartie);}
      $xml->endElement();}
    if ($sqlDecks) while ($deck=mysqli_fetch_assoc($sqlDecks)) {
      $xml->startElement('deck');
      $xml->writeAttribute('dId',$deck['dId']);
      $xml->writeAttribute('dNom',$deck['dNom']);
      $xml->endElement();}
    if ($sqlHeros) while ($heros=mysqli_fetch_assoc($sqlHeros)) {
      $xml->startElement('deck');
      $xml->writeAttribute('dId', 'h'.$heros['hId']);
      $xml->writeAttribute('dNom', $heros['hNom']);
      $xml->endElement();}
    if ($sqlLastManigance) while ($lastManigance=mysqli_fetch_assoc($sqlLastManigance)) {
      $xml->startElement('lastManigance');
      $xml->writeAttribute('id',$lastManigance['mnManigance']);
      $xml->endElement();}
    if ($maniDelete<>0) {
      $sqlManiDelete=mysqli_fetch_assoc(sql_get("SELECT * FROM `manigances` WHERE `maId`='$maniDelete'"));
      $maniDelete='<h2>'.$sqlManiDelete['maNom'].'</h2><b>Une fois déjouée :</b> '.nl2br($sqlManiDelete['maDejoue']);
      $xml->startElement('maniDelete');
      $xml->writeAttribute('text',$maniDelete);
      $xml->endElement();}
  $xml->endElement();
  header('Content-type: text/xml');
  $xml->flush();}

if (isset($_GET['phase'])) {
  $nbJoueurs=sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'");
  $nbJoueurs=mysqli_num_rows($nbJoueurs);
  $phaseSql=sql_get("SELECT `mVieMax1`,`mVieMax2`,`mVieMax3`,`mNom`,`pMechPhase` FROM `parties`, `mechants` WHERE `pUri`='$partieId' AND `mId`=`pMechant`");
  $phase=mysqli_fetch_assoc($phaseSql);
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

if (isset($_GET['mGet'])) {
  $deck=htmlspecialchars($_GET['mGet']);
  if (substr($deck,0,1)=='h') {
    #récupération des manigances du héros choisi
    $sqlManigances=sql_get("SELECT * FROM `manigances` LEFT JOIN `maniAnnexes` ON (`maId`=`mnManigance` AND `mnPartie`='$partieId') WHERE `maDeck`='0' AND `maNumero`='".substr($deck,1)."' AND `mnPartie` IS NULL ORDER BY `maNom` ASC");}
  elseif ($deck<>'0') {
    #récupération des manigances du deck choisi
    $sqlManigances=sql_get("SELECT * FROM `manigances` LEFT JOIN `maniAnnexes` ON `maId`=`mnManigance` WHERE `maDeck`='$deck' AND (`mnPartie`!='$partieId' or `mnPartie` IS NULL) ORDER BY `maNom`ASC");}
  else exit();
  $xml = new XMLWriter();
  $xml->openURI("php://output");
  $xml->startDocument();
  $xml->setIndent(true);
  $xml->startElement('ajax');
  while ($manigance=mysqli_fetch_assoc($sqlManigances)) {
  	  $xml->startElement('manigance');
      foreach ($manigance as $cleMani => $valMani) {$xml->writeAttribute($cleMani, $valMani);}
    $xml->endElement();}
  $xml->endElement();
  header('Content-type: text/xml');
  $xml->flush();}

if (isset($_POST['phase'])) {
  $phase=htmlspecialchars($_POST['phase']);
  $nbJoueurs=sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'");
  $nbJoueurs=mysqli_num_rows($nbJoueurs);
  $mechant=sql_get("SELECT `mVieMax1`,`mVieMax2`,`mVieMax3` FROM `parties`, `mechants` WHERE `pUri`='$partieId' AND `mId`=`pMechant`");
  $mechant=mysqli_fetch_assoc($mechant);
  $vieMax=$mechant['mVieMax'.$phase]*$nbJoueurs;
  sql_get("UPDATE `parties` SET `pMechPhase`='$phase', `pMechVie`='$vieMax' WHERE `pUri`='$partieId'");}

if (isset($_POST['mechant'])) {
  $mechant=htmlspecialchars($_POST['mechant']);
  $nbJoueurs=sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'");
  $nbJoueurs=mysqli_num_rows($nbJoueurs);
  if ($nbJoueurs>0) {$premier=mt_rand(1,$nbJoueurs);} else {
  	$premier=0;
  	$nbJoueurs=1;}
  $joueurs=sql_get("SELECT `jId`, `jNumero` FROM `joueurs` WHERE `jPartie`='$partieId'");
  while ($joueur=mysqli_fetch_assoc($joueurs)) {if($joueur['jNumero']==$premier) {$premier=$joueur['jId'];}}
  $vieMechant=sql_get("SELECT `mVieMax1` FROM `mechants` WHERE `mId`='$mechant'");
  $vieMechant=mysqli_fetch_assoc($vieMechant)['mVieMax1']*$nbJoueurs;
  sql_get("UPDATE `parties` SET `pMechDesoriente`=0, `pMechSonne`=0, `pMechTenace`=0, `pMechPhase`='1', `pMechVie`='$vieMechant', `pMechant`='$mechant',`pPremier`='$premier',`pManiPrincipale`='0',`pManiCourant`='0',`pManiMax`='0' WHERE `pUri`='$partieId'");
  echo 'SelectManigance';}

if(isset($_POST['heros'])) {
  $heros=htmlspecialchars($_POST['heros']);
  $joueur=htmlspecialchars($_POST['joueur']);
  $newVie=mysqli_fetch_assoc(sql_get("SELECT `hVie` FROM `heros` WHERE `hId`='$heros'"));
  $sqlReq="UPDATE `joueurs` SET `jHeros`='$heros', `jVie`='".$newVie['hVie']."' WHERE `jPartie`='$partieId' AND ";
  if (isset($_POST['joueurNum'])) $sqlReq.='`jNumero`=\''.$_POST['joueurNum'].'\''; else $sqlReq.='`jId`=\''.$_POST['joueur'].'\'';
  sql_get($sqlReq);}

if(isset($_POST['vieMechant'])) {
  $vieMechant=htmlspecialchars($_POST['vieMechant']);
  sql_get("UPDATE `parties` SET `pMechVie`='$vieMechant' WHERE `pUri`='$partieId'");}

if(isset($_POST['switch'])) {
  $switch=htmlspecialchars($_POST['switch']);
  switch ($switch) {
    case 'mechantDesoriente':
        sql_get("UPDATE `parties` SET `pMechDesoriente`=!`pMechDesoriente` WHERE `pUri`='$partieId'");
        break;
    case 'mechantSonne':
        sql_get("UPDATE `parties` SET `pMechSonne`=!`pMechSonne` WHERE `pUri`='$partieId'");
        break;
    case 'mechantTenace':
        sql_get("UPDATE `parties` SET `pMechTenace`=!`pMechTenace` WHERE `pUri`='$partieId'");
        break;
    case 'video':
        sql_get("UPDATE `parties` SET `pVideo`=!`pVideo` WHERE `pUri`='$partieId'");
        break;
    case 'desoriente':
    	sql_get("UPDATE `joueurs` SET `jDesoriente`=!`jDesoriente` WHERE `jId`='$joueurId'");
        break;
    case 'sonne':
    	sql_get("UPDATE `joueurs` SET `jSonne`=!`jSonne` WHERE `jId`='$joueurId'");
        break;
    case 'tenace':
    	sql_get("UPDATE `joueurs` SET `jTenace`=!`jTenace` WHERE `jId`='$joueurId'");
        break;
    case 'etat':
    	$etat=sql_get("SELECT `jStatut` FROM `joueurs` WHERE `jId`='$joueurId'");
    	$etat=mysqli_fetch_assoc($etat);
    	echo $etat['jStatut'];
    	if ($etat['jStatut']=='AE') {$etat='SH';} else {$etat='AE';}
        sql_get("UPDATE `joueurs` SET `jStatut`='$etat' WHERE `jId`='$joueurId'");
        break;
    case 'premier':
    	sql_get("UPDATE `parties` SET `pPremier`='$joueurId' WHERE `pUri`='$partieId'");
        break;
    case 'mechantRiposte':
    	sql_get("UPDATE `parties` SET `pMechRiposte`=!`pMechRiposte` WHERE `pUri`='$partieId'");
    	break;
    case 'mechantPercant':
    	sql_get("UPDATE `parties` SET `pMechPercant`=!`pMechPercant` WHERE `pUri`='$partieId'");
    	break;
    case 'mechantDistance':
    	sql_get("UPDATE `parties` SET `pMechDistance`=!`pMechDistance` WHERE `pUri`='$partieId'");
    	break;}}

if (isset($_POST['boite'])) {
  $boite=htmlspecialchars($_POST['boite']);
  $inclus=htmlspecialchars($_POST['inclus']);
  sql_get("UPDATE `boites` SET `bInclus`=$inclus WHERE `bId`='$boite'");}

if(isset($_POST['suivant'])) {
  $joueur=htmlspecialchars($_POST['suivant']);
  sql_get("UPDATE `parties` SET `pPremier`='$joueur' WHERE `pUri`='$partieId'");}

if(isset($_POST['changeName'])) {
  $joueur=htmlspecialchars($_POST['changeName']);
  sql_get("UPDATE `joueurs` SET `jNom`='$joueur' WHERE `jId`='$joueurId'");}

if(isset($_POST['vieJoueur'])) {
  $vie=htmlspecialchars($_POST['vieJoueur']);
  sql_get("UPDATE `joueurs` SET `jVie`='$vie' WHERE `jId`='$joueurId'");}

if(isset($_POST['manigance'])) {
  $manigance=htmlspecialchars($_POST['manigance']);
  sql_get("UPDATE `parties` SET `pManiCourant`='$manigance' WHERE `pUri`='$partieId'");}

if(isset($_POST['maniganceMax'])) {
  $manigance=htmlspecialchars($_POST['maniganceMax']);
  sql_get("UPDATE `parties` SET `pManiMax`='$manigance' WHERE `pUri`='$partieId'");}

if(isset($_POST['maniganceAcc'])) {
  $maniAccel=mysqli_num_rows(sql_get("SELECT `maId` FROM `manigances`,`maniAnnexes` WHERE `mnPartie`='$partieId' AND `mnManigance`=`maId` AND `maAcceleration`='1'"));
  $manigance=htmlspecialchars($_POST['maniganceAcc'])-$maniAccel;
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
     $maInit=$maInit+$nbJoueurs*$maniDetail['maEntrave'];  }
  sql_get("INSERT INTO `maniAnnexes` (`mnPartie`,`mnManigance`,`mnMenace`) VALUES ('$partieId','$manigance','$maInit')");
  sql_get("UPDATE `parties` SET `pManiDelete`='0' WHERE `pUri`='$partieId'");}

if(isset($_POST['MA'])) {
  $manigance=htmlspecialchars($_POST['MA']);
  $menace=htmlspecialchars($_POST['menace']);
  if ($menace<1) {
    sql_get("DELETE FROM `maniAnnexes` WHERE `mnPartie`='$partieId' AND `mnManigance`='$manigance'");
    if (mysqli_fetch_assoc(sql_get("SELECT * FROM `manigances` WHERE `maId`='$manigance'"))['maDejoue']<>'') {sql_get("UPDATE `parties` SET `pManiDelete`='$manigance' WHERE `pUri`='$partieId'");}
  else {sql_get("UPDATE `parties`SET `pManiDelete`='0' WHERE `pUri`='$partieId'");}}
  else {
    sql_get("UPDATE `maniAnnexes` SET `mnMenace`='$menace' WHERE `mnPartie`='$partieId' AND `mnManigance`='$manigance'");
    sql_get("UPDATE `parties`SET `pManiDelete`='0' WHERE `pUri`='$partieId'");}}

if(isset($_POST['NewPrincipale'])) {
  $manigance=htmlspecialchars($_POST['NewPrincipale']);
  $maniDetail=mysqli_fetch_assoc(sql_get("SELECT * FROM `ManigancesPrincipales` WHERE `mpId`='$manigance'"));
  $mpMax=$maniDetail['mpMax'];
  if ($maniDetail['mpMaxMultiplie']==true) {
    $nbJoueurs=mysqli_num_rows(sql_get("SELECT `jId`FROM `joueurs` WHERE `jPartie`='$partieId'"));
    $mpMax=$mpMax*$nbJoueurs;}
  $mpInit=$maniDetail['mpInit'];
  if ($maniDetail['mpMultiplie']==true) $mpInit=$mpInit*$nbJoueurs;
  sql_get("UPDATE `parties` SET `pManiMax`='$mpMax', `pManiCourant`='$mpInit', `pManiPrincipale`='$manigance' WHERE `pUri`='$partieId'");}

if(isset($_POST['addCompteur'])) {sql_get("INSERT INTO `compteurs` (`cPartie`) VALUES ('$partieId')");}

if (isset($_POST['delCompteur'])) {
  $compteur=htmlspecialchars($_POST['delCompteur']);
  sql_get("DELETE FROM `compteurs` WHERE `cId`='$compteur'");}

if(isset($_POST['compteur'])) {
  $compteur=htmlspecialchars($_POST['compteur']);
  $value=htmlspecialchars($_POST['value']);
  sql_get("UPDATE `compteurs` SET `cValeur`='$value' WHERE `cId`='$compteur'");}
?>