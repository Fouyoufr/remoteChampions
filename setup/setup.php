<?php
  $gitUrl='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/fullXML';
  $adminPasswordInitial='8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918';
  $phpFiles=array(
    'admin.php','aide.css','ajax.php','fucntions.inc','include.inc','index.php','joueur.php','mechant.php','new.php',
    'aide.css','ecran.css',
	'mc.js',
	'aide.png','amplification.png','bug.png','counter.png','first.png','link.png','load.png','marvel-fullHD.png','Menace+.png','MenaceAcceleration.png','MenaceCrise.png','MenaceRencontre.png','pointVert.png','pp.png','refresh.png','save.png','saveB.png','smartphone.png','trash.png');
  error_reporting(E_ERROR | E_PARSE);

  function remoteFileSize ($phpFile) {
	global $setupSourcePath;
	if (substr($setupSourcePath,0,4)=='http') {
	  $remoteCall = curl_init("$setupSourcePath/$phpFile");
	  curl_setopt($remoteCall, CURLOPT_RETURNTRANSFER, TRUE);
	  curl_setopt($remoteCall, CURLOPT_HEADER, TRUE);
	  curl_setopt($remoteCall, CURLOPT_NOBODY, TRUE);
   	  $data = curl_exec($remoteCall);
	  $remoteSize = curl_getinfo($remoteCall, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	  curl_close($remoteCall);}
	else $remoteSize=filesize("$setupSourcePath/$phpFile");
	return $remoteSize;}

function imageUpdate($imgFolder,$imgObject) {
	global $str,$setupSourcePath,$xmlBoxes;
	#Mise à jour des images du dossier
	$nothingToDo=true;
	if (!file_exists("img/$imgFolder")) {if(!mkdir("img/$imgFolder",0777,true)) {
		$nothingToDo=false;
		exit("<tr><td></td><div class='error'>".$str['noImgDir']."...</div></td></tr>");}}
	foreach ($xmlBoxes as $xmlBox) foreach ($xmlBox->$imgObject as $xmlObject) {
		$imageFile="img/$imgFolder/".$xmlObject['id'].'.png';
		if (!file_exists($imageFile)) {
			$nothingToDo=false;
			echo "<tr><td>".$xmlObject['name']."</td><td>Ajout";
			if (!@copy("$setupSourcePath/$imageFile",$imageFile)) {echo "<div class='error'>".$str['noCopy']."....<div class='subError'>".error_get_last()['message']."</div></div>";}
			echo '</td></tr>';}}
	if ($nothingToDo) echo "<tr><td>".$str['folder']." $imgFolder</td><td>".$str['imagesOk']."</td></tr>";}

  include_once('functions.inc');
  session_start();
  echo "<!doctype html>\n<html lang='$rcLang'>\n";
?>
<head>
  <META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>
  <META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>
  <meta charset='UTF-8'>
  <link rel='icon' href='favicon.ico'/>
  <title>Remote Champions - Installation/Mise à jour</title>
  <script language='JavaScript'>
  </script>
  <style>
	body {background-color:#7e696a;color:white;}
	.pannel {position:relative;border:solid 2px white;padding:2px;color:white;font-family:"Arial", Gadget, sans-serif;background:lightgray;margin-top:2px;border-color:black;color:black;font-weight:bold;font-size:1em;}
	.pannel table,.pannel td {border: 1px solid #555;border-collapse: collapse;}
	.pannel tr:nth-child(odd) {background-color:bisque;}
	.pannel th {background-color: #555;color: #e5d2e5;}
	.pannelTitle {background-color:black;color:white;font-weight:bold;font-size:2em;margin:-2px;margin-bottom:2px;padding-left:10px;text-align:left;}
	.error {background-color:red;color:white;font-weight:bold;width:100%;text-align:center;font-size:1.5em;margin:-2px;padding:3px;}
	.subError {text-align:left;font-size:.5em;font-style:italic;color:lightgray;font-weight:normal;}
	.gras {font-weight:bold;}
	input {background:transparent;}
	input[type=submit] {font-size:1.5em;font-weight:bold;color:white;background-color:black;margin-top:10px;display:inline-block;}
	a.button {margin-left:calc(50% - 100px);width:200px;font-size:1.5em;font-weight:bold;color:black;background-color:darkgray;margin-top:10px;display:inline-block;padding:4px;text-decoration:none;border:solid 2px black;text-align:center;}
	a.button:hover {background-color:lightgray;}
  </style>
</head>
<body>
<?php
include 'config.inc';
$dockerSetup=false;
$localUpdate=false;
#Vérification du mot de passe d'administration.
if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) {
	exit("<div class='pannel'><div class='pannelTitle'>".$str['restrictedTitle']."</div>".$str['restricted']."...<br/><a class='button' href='.'>".$str['restrictedBack']."</a></div>");}
clearstatcache();
#Gestion de la mise à jour par utilisation de fichiers locaux.
if (isset($_POST['autoUpdate']) and $_POST['autoUpdate']=='non') {
  if (!file_exists('updates')) {if(!mkdir('updates',0777,true)) exit("<div class='error'>".$str['noUpdatesDir']."...</div>");}
  $target_file = 'updates/'.basename($_FILES['zipUpdate']['name']);
  if(strtolower(pathinfo($target_file,PATHINFO_EXTENSION)) != 'zip') exit("<div class='error'>Fichier incorrect.<div class='subError'>".$str['nozip'].".<br/>".$str['readDoc']."...</div></div>");
  $zip = new ZipArchive;
  if ($zip->open($_FILES['zipUpdate']['tmp_name']) === TRUE) {
      $zip->extractTo('updates/');
	  $zipFolder=$zip->getNameIndex(0);
      $zip->close();}
  else exit("<div class='error'>".$str['nozip2'].".<div class='subError'>".error_get_last()['message']."</div></div>");
  if (substr($zipFolder,-1,1)!='/') exit("<div class='error'>".$str['incorrectFormat'].".<div class='subError'>".$str['nozip3']."<br/>".$str['readDoc'].".</div></div>"); else $zipFolder=explode('/',$zipFolder)[0];
  $setupSourcePath="updates/$zipFolder/setup";
  $localUpdate=true;}
elseif (file_exists('dockerSetup')) {
  #insertion initiale de contenu pour Docker
  $setupSourcePath='docker';
  $dockerSetup=true;}
else {
  $setupSourcePath=$gitUrl.'/setup';
  $setupDate=gitFileDate('/setup/setup.php');}
if ($dockerSetup) echo "<div class='pannel'><div class='pannelTitle'>".$str['docker1']."</div>\n".$str['docker2'].".";
else {
  echo "<div class='pannel'><div class='pannelTitle'".$str['updateScript']."</div>";
  #Récupération de la dernière version du présent script
  if (isset($setupDate['erreur'])) echo "<div class='error'>".$str['gitHubError']."....<div class='subError'>".$setupDate['erreur']."</div></div>";
  elseif ($localUpdate or (isset($setupDate['date']) and new dateTime('@'.filemtime('setup.php'))<$setupDate['date']))  {
    echo $str['updateScript2'].".<br/>";
    if (@copy("$setupSourcePath/setup.php",'setup.php')) {
	  if (!$localUpdate) exit ("<a href='' class='button'>".$str['relaunch']."</a>");}
    else exit("<div class='error'>".$str['noCopy']."....<div class='subError'>".error_get_last()['message']."</div></div>");}
  else echo $str['allreadyUp'].".";}

#Mise a jour pour la version 1.5 : depuis le full SQL vers le cache AJAX en mode fichiers.
if (function_exists('sql_get') and mysqli_num_rows(sql_get("SHOW TABLES LIKE 'parties'"))) {
	echo "</div><div class='pannel'><div class='pannelTitle'>".$str['update5']."</div>".$str['stillGameTable'].".<br/>";
	$sqlParties=sql_get('SELECT * FROM `parties`');
	while ($sqlPartie=mysqli_fetch_assoc($sqlParties)) {
		$mNom=mysqli_fetch_assoc(sql_get("SELECT `mNom` FROM `mechants` WHERE `mId`='".$sqlPartie['pMechant']."'"))['mNom'];
		$mpNom=mysqli_fetch_assoc(sql_get("SELECT `mpNom` FROM `ManigancesPrincipales` WHERE `mpId`='".$sqlPartie['pManiPrincipale']."'"))['mpNom'];
		echo '- '.$str['XMLFromSQL'].' \''.$sqlPartie['pUri'].'\':<ul>';
		$partieXML=<<<XML
		<?xml version='1.0' encoding='UTF-8'?>
		<partie>
		</partie>
		XML;
		$xml=new SimpleXMLElement($partieXML);
		$nextPhaseVie=$sqlPartie['pMechPhase']+1;
		if ($nextPhaseVie==4) $nextPhaseVie=1;
		$nextPhaseVie=mysqli_fetch_assoc(sql_get("SELECT `mVie".$nextPhaseVie."` FROM `mechants` WHERE `mId`='".$sqlPartie['pMechant']."'"))['mVie'.$nextPhaseVie];
		xmlAttr($xml,array('pUri'=>$sqlPartie['pUri'],'pMechant'=>$sqlPartie['pMechant'],'pMechVie'=>$sqlPartie['pMechVie'],'pMechPhase'=>$sqlPartie['pMechPhase'],'pDate'=>strtotime($sqlPartie['pDate']),'pPremier'=>$sqlPartie['pPremier'],'pManiDelete'=>$sqlPartie['pManiDelete'],'pManiCourant'=>$sqlPartie['pManiCourant'],'pManiMax'=>$sqlPartie['pManiMax'],'pManiAcceleration'=>$sqlPartie['pManiAcceleration'],'pMechRiposte'=>$sqlPartie['pMechRiposte'],'pMechPercant'=>$sqlPartie['pMechPercant'],'pMechDistance'=>$sqlPartie['pMechDistance'],'mNom'=>$mNom,'mpNom'=>$mpNom,'pMechDesoriente'=>$sqlPartie['pMechDesoriente'],'pMechSonne'=>$sqlPartie['pMechSonne'],'pMechTenace'=>$sqlPartie['pMechTenace'],'nextPhaseVie'=>$nextPhaseVie));
		$sqlManigances=sql_get("SELECT * From `maniAnnexes`,`manigances` WHERE `mnPartie`='".$sqlPartie['pUri']."' AND `mnManigance`=`maId`");
		$maniEnJeu=array();
		//Ajout des manigances en jeu
		while ($sqlManigance=mysqli_fetch_assoc($sqlManigances)) {
			$maniEnJeu[]=$sqlManigance['maId'];
			echo '<li>'.$str['5addScheme'].' "'.$sqlManigance['maNom'].'".</li>';
			if ($sqlManigance['maDeck']==0) {
				$sqlManigance['maDeck']='h'.$sqlManigance['maDeck'];
				$dNom=mysqli_fetch_assoc(sql_get("SELECT `hNom` FROM `heros` WHERE `hId`='".$sqlManigance['maNumero']."'"))['hNom'];}
			else {
				$dNom=mysqli_fetch_assoc(sql_get("SELECT `dNom` FROM `decks` WHERE `dId`='".$sqlManigance['maDeck']."'"))['dNom'];}
			$xmlManigance=$xml->addChild('manigance');
			xmlAttr($xmlManigance,array('maId'=>$sqlManigance['maId'],'maNom'=>$sqlManigance['maNom'],'mnMenace'=>$sqlManigance['mnMenace'],'fromDeckId'=>$sqlManigance['maDeck'],'fromDeckNom'=>$dNom,'maRevele'=>$sqlManigance['maRevele'],'maDejoue'=>$sqlManigance['maDejoue'],'maInfo'=>$sqlManigance['maInfo'],'maNumero'=>$sqlManigance['maNumero'],'maCrise'=>$sqlManigance['maCrise'],'maRencontre'=>$sqlManigance['maRencontre'],'maAcceleration'=>$sqlManigance['maAcceleration'],'maAmplification'=>$sqlManigance['maAmplification'],'maDeck'=>$sqlManigance['maDeck'],'dNom'=>$dNom));}
		$sqlJoueurs=sql_get("SELECT * FROM `joueurs` WHERE `jPartie`='".$sqlPartie['pUri']."'");
		//Ajout des joueurs
		while ($sqlJoueur=mysqli_fetch_assoc($sqlJoueurs)) {
		  $hNom=mysqli_fetch_assoc(sql_get("SELECT `hNom` FROM `heros` WHERE `hId`='".$sqlJoueur['jHeros']."'"))['hNom'];
		  echo '<li>'.$str['5addPlayer'].' "'.$sqlJoueur['jNom'].'".</li>';
		  $xmlJoueur=$xml->addChild('joueur');
		  xmlAttr($xmlJoueur,array('jId'=>$sqlJoueur['jId'],'jNom'=>$sqlJoueur['jNom'],'jNumero'=>$sqlJoueur['jNumero'],'jVie'=>$sqlJoueur['jVie'],'jStatut'=>$sqlJoueur['jStatut'],'jDesoriente'=>$sqlJoueur['jDesoriente'],'jSonne'=>$sqlJoueur['jSonne'],'jTenace'=>$sqlJoueur['jTenace'],'jOnline'=>0,'jHeros'=>$sqlJoueur['jHeros'],'hNom'=>$hNom));
		  if ($sqlJoueur['jHeros']!=0) {
			  //Ajout du deck héros
			  $deckToAdd=true;
			  $sqlManigances=sql_get("SELECT * FROM `manigances` WHERE `maDeck`=0 AND `maNumero`='".$sqlJoueur['jHeros']."'");
			  while ($sqlManigance=mysqli_fetch_assoc($sqlManigances)) if (!in_array($sqlManigance['maId'],$maniEnJeu)) {
				  if ($deckToAdd) {
					  $deckXML=$xml->addChild('deck');
					  xmlAttr($deckXML,array('dId'=>'h'.$sqlManigance['maNumero'],'dNom'=>$hNom));
					  $deckToAdd=false;}
				$maniChoiceXML=$deckXML->addChild('maniChoice');
				xmlAttr($maniChoiceXML,array('maId'=>$sqlManigance['maId'],'maNom'=>$sqlManigance['maNom']));}}}
		//Ajout des manigances à choisir
		$sqlManigances=sql_get("SELECT * FROM `deckParties`,`decks`,`manigances` WHERE `dpPartie`='".$sqlPartie['pUri']."' AND `dpDeck`=`dId` AND `maDeck`!=0 AND `maDeck`=`dId` ORDER BY `dId`");
		$deckToAdd=0;
		while ($sqlManigance=mysqli_fetch_assoc($sqlManigances)) if (!in_array($sqlManigance['maId'],$maniEnJeu)) {
		  if ($deckToAdd!=$sqlManigance['dId']) {
			echo '<li>'.$str['5addDeck'].' "'.$sqlManigance['dNom'].'".</li>';
			$deckXML=$xml->addChild('deck');
			xmlAttr($deckXML,array('dId'=>$sqlManigance['dId'],'dNom'=>$sqlManigance['dNom']));
			$deckToAdd=$sqlManigance['dId'];}
		  $maniChoiceXML=$deckXML->addChild('maniChoice');
		  xmlAttr($maniChoiceXML,array('maId'=>$sqlManigance['maId'],'maNom'=>$sqlManigance['maNom']));}
		//Ajout des compteurs annexes
		$sqlCompteurs=sql_get("SELECT * FROM `compteurs` WHERE `cPartie`='".$sqlPartie['pUri']."'");
		while ($sqlCompteur=mysqli_fetch_assoc($sqlCompteurs)) {
		  $xmlCompteur=$xml->addChild('compteur');
		  xmlAttr($xmlCompteur,array('cId'=>$sqlCompteur['cId'],'cValeur'=>$sqlCompteur['cValeur']));}
		$xml->saveXML('ajax/'.$sqlPartie['pUri'].'.xml');
		echo '</ul>';}}

#Mise à jour des fichiers d'aide HTML		
echo "</div><div class='pannel'><div class='pannelTitle'>".$str['gameHelpUp']."</div>";

#A vérifier/gérer :
if ($localUpdate) echo 'coucou';
if ($dockerSetup) echo 'coucou';

foreach ($rcLangs as $helpLang) {
  $helpDate=gitFileDate("/setup/$helpLang/aide.md");
  if (isset($helpDate['erreur'])) echo "<div class='error'>".$str['gitHubError']."<div class='subError'>".$helpDate['erreur']."</div></div>";
elseif (!file_exists("$helpLang/aide.html") or (new dateTime('@'.filemtime("$helpLang/aide.html"))<$helpDate['date'])) {
  echo $str['gameHelpUp']." : $helpLang";
  $helpFile="<!doctype html>\n<html lang='fr'>\n<head>\n<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>\n<meta charset='UTF-8'>\n<link rel='stylesheet' href='aide.css'>\n<link rel='icon' href='../favicon.ico'/>\n<title>Remote Champions - Aide</title>\n</head>\n<body>\n<div id='TDMUp'></div>";
  $file = @fopen ("$setupSourcePath/$helpLang/aide.md", "r");
  if (!$file) echo "<div class='error'>".$str['openFileErr'].".<div class='subError'>".$str['gameHelpUp2']." '$setupSourcePath/$helpLang/aide.md'.</div></div>";
  else {
    $luEncours=false;
    $entryId=0;
    $numbEncours=false;
	$table='';
    while (!feof ($file)) {
	  $line=str_replace(["\r","\n"],'',fgets($file));
	  if (substr($line,0,2)=='# ') {
	    if ($luEncours) { $table.="</ul>\n"; $luEncours=false;}
	    if ($numbEncours) { $table.="</ol>"; $numbEncours=false;}
		$entryId++;
		$table.="</div></div>\n<div id='aide$entryId' class='aideChapter'><div class='title' onclick='contentStyle=document.getElementById(this.parentElement.getElementsByClassName(\"content\")[0].id).style;if (contentStyle.display==\"block\") contentStyle.display=\"none\"; else contentStyle.display=\"block\";'>".substr($line,2)."</div>\n<div id='content$entryId' class='content'>\n";}
	  elseif (substr($line,0,2)=='- ' or substr($line,0,5)=='   - ') {
	    if (!$luEncours) {$luEncours=true; $table.="<ul>\n";}
	    if (substr($line,0,5)=='   - ') $line=substr($line,3); elseif ($numbEncours) $table.="</ol>\n";
	    $table.='<li>'.substr($line,2)."</li>\n";}
	  elseif (substr($line,0,3)=='1. ') {
	    if (!$numbEncours) {$numbEncours=true; $table.="<ol>\n";}
	    if ($luEncours) {$luEncours=false; $table.="</ul>\n";}
		$table.='<li>'.substr($line,3)."</li>\n";}
	  else {
	    if ($luEncours) {$table.="</ul>\n";	$luEncours=false;}
	    if ($numbEncours) {$table.="</ol>\n"; $numbEncours=false;}
	    $table.=$line;
	    if (substr($line,-2)=='  ') $table.="<br/>\n";}}
    fclose($file);
	$helpFile.=substr($table,13)."\n</div>\n</div>\n<div id='TDMDown'></div>\n<a href='#' id='collapse' onclick='Array.from(document.getElementsByClassName(\"content\")).forEach(content => content.style.display=\"block\");'>+</a>\n<a href='#' id='moveUp'>&#10148;</a>\n</body>\n</html>";
	file_put_contents ("$helpLang/aide.html",$helpFile);}}
	else echo $str['gameHelpUp3'].".";}

if ($dockerSetup) {
	#Fin d'insertion Docker : nettoyage
    $files = glob($setupSourcePath.'/*',GLOB_MARK);
    foreach ($files as $file) unlink($file);
    rmdir($setupSourcePath);
	echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['endUpdate']."</div><a class='button' href='.'>".$str['restrictedBack']."</a></div>";}
else {
  echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['phpUp']."</div><table><tr><th>".$str['file']."</th><th></th></tr>";
  #Vérification des fichiers par leur taille.
  foreach ($phpFiles as $phpFile) {
	  if (explode('.',$phpFile)[1]=='png') $phpFile='img/'.$phpFile;
	$localSize=filesize($phpFile);
	$remoteSize = remoteFileSize($phpFile);
	echo "<tr><td>$phpFile</td><td>";
	if ($localSize<>$remoteSize) {
		echo $str['update'];
		if (!@copy("$gitUrl/setup/$phpFile",$phpFile)) echo "<div class='error'>".$str['noCopy']."....<div class='subError'>".error_get_last()['message']."</div></div>";}
	else echo $str['noUpdate'];
	echo "</td></tr>";}
  
  #Mise à jour des boxes...
  echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['boxesUpdate']."</div>";
  $xmlBoxes=simplexml_load_file($boxFile);
  foreach ($rcLangs as $boxLang) {
    if (!$newBoxes=simplexml_load_file("$setupSourcePath/$boxLang/boxes.xml")) echo "<div class='error'>".$str['openFileErr'].".<div class='subError'>".$str['gameHelpUp2']." '$setupSourcePath/$boxLang/boxes.xml'.</div></div>";
	else {
	  echo "$boxLang - OK.<br/>";
	  foreach($newBoxes->box as $newbox) foreach ($xmlBoxes->box as $oldBox) if ($newBox['id']<>1) $newBox['own']=$oldBox['own']->__toString();
	  xmlSave($newBoxes,"$boxLang/boxes.xml");}}

  echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['imgUp']."</div><table><tr><th>".$str['pic']."</th><th></th></tr>";
  imageUpdate('mechants','mechant');
  imageUpdate('boites','box');
  imageUpdate('heros','heros');
echo "</table></div>";}

echo "<div class='pannel'><div class='pannelTitle'>".$str['endUpdate']."</div><a class='button' href='.'>".$str['restrictedBack']."</a></div>";
?>
</body>
</html>