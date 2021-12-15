<!doctype html>
<html lang='fr'>
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
include_once('functions.php');
global $str;
session_start();
$gitUrl='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main';
$adminPasswordInitial='8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918';
if (!file_exists('./config.inc')) {
	#Le fichier de paramètrage n'existe pas
	echo "<div class='pannel'><div class='pannelTitle'>".$str['firstSetup']."</div>";
	$error='';
	if (file_exists('dockerSetup')) {
	  #Gestion de la mise en place initiale du contenu dans un conteneur Docker.
	  $_POST['serverName']='';
	  $_POST['serverUser']='root';
	  $_POST['serverPass']='';
	  $_POST['serverDb']='remoteChampions';
	  $_POST['newDb']='1';}
	if(!is_writable(__DIR__)) {$error=$str['dirNoWrite']." '".__DIR__."', ".$str['pleaseCheck']."!";}
	elseif (isset($_POST['serverName'])) {
		if (isset($_POST['serverPort'])) $_POST['serverName'].=':'.$_POST['serverPort'];
		$configContent="<?php\nfunction sql_get(\$sqlQuery) {\n  global \$sqlConn;\n  \$sqlConn=mysqli_connect('".$_POST['serverName']."','".$_POST['serverUser']."','".$_POST['serverPass']."','".$_POST['serverDb']."');\n  if(!\$sqlConn ) {die('".$str['sqlNoConnect'].": '.mysqli_connect_error());}\n  \$sqlResult=mysqli_query(\$sqlConn,\$sqlQuery);\n  return \$sqlResult;}\n\$adminPassword='$adminPasswordInitial';\n\$publicPass='';\n?>";
		if (!isset($_POST['serverDb']) or $_POST['serverDb']==''){$error=$str['dbNoName']."...";}
		elseif ($_POST['newDb']=='1') {
			#Création d'une nouvelle base de donnée
			$sqlConn=@mysqli_connect($_POST['serverName'],$_POST['serverUser'],$_POST['serverPass']);
			if (!$sqlConn){$error=$str['sqlNoConnect'].', '.$str['pleaseCheck'].".<br/><div class='subError'>".mysqli_connect_error()."</div>";}
			else {
				$sqlQuery="CREATE DATABASE `".$_POST['serverDb']."`";
				mysqli_query($sqlConn,$sqlQuery);
				$sqlError=mysqli_error($sqlConn);
				if ($sqlError!='') {
					$error=$str['dbCreateImpossible']." '".$_POST['serverDb']."'.<div class='subError'><i>$sqlQuery</i> : $sqlError</div>";}
				else {
					file_put_contents ('config.inc',$configContent);
					$_SESSION['adminPassword']=$adminPasswordInitial;
					header("Refresh:0");}}}
		else {
			#Utilisation d'une base de donnée existante
			$sqlConn=@mysqli_connect($_POST['serverName'].':'.$_POST['serverPort'],$_POST['serverUser'],$_POST['serverPass'],$_POST['serverDb']);
			if (!$sqlConn){$error=$str['sqlNoConnect'].', '.$str['pleaseCheck'].".<br/><div class='subError'>".mysqli_connect_error()."</div>";}
			else {
				file_put_contents ('config.inc',$configContent);
				$_SESSION['adminPassword']=$adminPasswordInitial;
				header("Refresh:0");}}}
	if (!isset($_POST['serverPort'])) $_POST['serverPort']='3306';
	echo "<form action='' method='post'>";
	if ($error<>'') echo"<div class='error'>$error</div>";
	if (!isset($_POST['serverName']) or (isset($_POST['serverName']) and $error<>'')) {
	  echo $str['notInstalled'].", ".$str['sqlServInfoAsk'].":<br/><table><tr><td>".$str['serverName']." :</td><td><input type='text' name='serverName' value='".$_POST['serverName']."'></td></tr><tr><td>".$str['serverPort']." :</td><td><input type='text' name='serverPort' value='".$_POST['serverPort']."'></td></tr><tr><td>".$str['serverUser']." :</td><td><input type='text' name='serverUser' value='".$_POST['serverUser']."'></td></tr><tr><td>".$str['serverPass']." :</td><td><input type='password' name='serverPass' value='".$_POST['serverPass']."'></td></tr><tr><td><input type='radio' name='newDb' value='0'";
	  if ($_POST['newDb']=='0') echo ' checked';
	  echo ">".$str['existingDB']."<br><input type='radio' name='newDb' value='1'";
	  if ($_POST['newDb']<>'0') echo ' checked';
	exit(">".$str['newDB']." :</td><td><input type='text' name='serverDb' value='".$_POST['serverDb']."'></td></tr><tr><td></td><td><input type='submit' value='".$str['ok']."'></td></tr></table>".$str['initialPassNote']."</form>");}}

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

function imageUpdate($imgFolder,$imgId,$imgNom) {
	global $str;
	#Mise à jour des images du dossier
	global $updateSourcePath;
	$nothingToDo=true;
	if (!file_exists("img/$imgFolder")) {if(!mkdir("img/$imgFolder",0777,true)) {
		$nothingToDo=false;
		exit("<tr><td></td><div class='error'>".$str['noImgDir']."...</div></td></tr>");}}
	$images=sql_get("SELECT `$imgId`,`$imgNom` FROM `$imgFolder`");
	while ($image=mysqli_fetch_assoc($images)) {
		$imageFile="img/$imgFolder/".$image[$imgId].'.png';
		if (!file_exists($imageFile)) {
			$nothingToDo=false;
			echo "<tr><td>".$image[$imgNom]."</td><td>Ajout";
			if (!@copy("$updateSourcePath/$imageFile",$imageFile)) {echo "<div class='error'>".$str['noCopy']."....<div class='subError'>".error_get_last()['message']."</div></div>";}
			echo '</td></tr>';}}
	if ($nothingToDo) echo "<tr><td>".$str['folder']." $imgFolder</td><td>".$str['imagesOk']."</td></tr>";}

function updateSQLcontent($tableFile,$tableId) {
	global $str;
	#Mise à jour (ajout, modification et suppression) du contenu d'une table fixe.
	global $sqlConn;
	$file = fopen ($tableFile, "r");
	if (!$file) exit("<div class='error'>".$str['openFileErr'].".<div class='subError'>".$str['openFileErrsub']." $tableFile'.</div></div>");
	$newTable=array();
	$nothingToDo=true;
	while (!feof ($file)) {
		$line=explode(',',fgets($file));
		if ($line[0][0]=='#') {
			$line[0]=substr($line[0],1);
			$cols=$line;
			$cols[count($cols)-1]=rtrim($cols[count($cols)-1]);}
		else {
			$newTable[$line[0]]=$line[1];
			$entry=sql_get("SELECT * FROM $tableId WHERE `$cols[0]`='$line[0]'");
			if ((!mysqli_num_rows($entry))) {
				$nothingToDo=false;
				echo "<tr><td>$tableId</td><td>".$str['sqlAddEntry']." `$cols[0]`='$line[0]'";
				$sqlQuery1='';
				$sqlQuery2=") VALUES (";
				foreach ($cols as $key=>$value) {
					$value=rtrim($value);
					$sqlQuery1.="`$value`, ";
					$sqlQuery2.="'".mysqli_real_escape_string($sqlConn,rtrim($line[$key]))."', ";}
				$sqlQuery="INSERT INTO `$tableId` (".substr($sqlQuery1,0,-2).substr($sqlQuery2,0,-2).')';
				sql_get($sqlQuery);
				$sqlError=mysqli_error($sqlConn);
				if ($sqlError!='') {echo "<div class='error'>".$str['error']."<div class='subError'>$sqlQuery<br/><span class='gras'>$sqlError</span></div></div>";}
				echo "</td></tr>";}
			else {
				$news=false;
				$entry=mysqli_fetch_assoc($entry);
				foreach ($cols as $key=>$value) if (!($entry[rtrim($value)]===rtrim($line[$key])) and $value<>'bInclus') $news=true;
				#L'ajout $key<>'bInclus' permet de ne pas remplacer la valeur 'possédée' de la boite...
				if ($news) {
					$nothingToDo=false;
					echo"<tr><td>$tableId</td><td>".$str['sqlChangeEntry']." `$cols[0]`='$line[0]'";
					$sqlQuery="UPDATE `$tableId` SET ";
					foreach ($cols as $key=>$value) {if ($key<>0) {$sqlQuery.="`".rtrim($value)."`='".mysqli_real_escape_string($sqlConn,rtrim($line[$key]))."', ";}}
					$sqlQuery=substr($sqlQuery,0,-2)." WHERE `$cols[0]`='".mysqli_real_escape_string($sqlConn,rtrim($line[0]))."'";
					sql_get($sqlQuery);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<div class='error'>".$str['error']."<div class='subError'>$sqlQuery<br/><span class='gras'>$sqlError</span></div></div>";}
					echo '<br/>';}}}}
	#Supression des enregistrements dipsarus.
	$oldTable=sql_get("SELECT `$cols[0]`, `$cols[1]` FROM $tableId");
	while($oldLine=mysqli_fetch_assoc($oldTable)) {
		if(!isset($newTable[$oldLine[$cols[0]]])) {
			$nothingToDo=false;
			echo "<tr><td>$tableId</td><td>".$str['sqlDeleteEntry']." '".$oldLine[$cols[0]]."'";
			sql_get ("DELETE FROM `$tableId` WHERE `$cols[0]`='".$oldLine[$cols[0]]."' AND `$cols[1]`='".$oldLine[$cols[1]]."'");
			$sqlError=mysqli_error($sqlConn);
			if ($sqlError!='') {echo "<div class='error'>".$str['error']."<div class='subError'>$sqlQuery<br/><span class='gras'>$sqlError</span></div></div>";}
			echo "</td></tr>";}}
	if ($nothingToDo) echo "<tr><td>$tableId</td><td>".$str['allFine']."</td></tr>";}

function sqlUpdate($sqlUpdateFile) {
#Récupération des éléments de structure SQL depuis référence gitHub et mise à jour/création dans la base
	global $sqlConn,$gitUrl,$str;
	$engine='ENGINE=InnoDB DEFAULT CHARSET=utf8';
	$file = fopen ($sqlUpdateFile, "r");
	if (!$file) {
		exit("<div class='error'>".$str['openFileErr'].".<div class='subError'>".$str['openFileErrsub']." '$sqlUpdateFile'.</div></div>");}
	$fileTable=array();
	while (!feof ($file)) {
		$nothingToDo=true;
    	$table=explode('=>',fgets($file),2);
    	$tableId=$table[0];
		$fileTable[]=$tableId;
    	$addTab=(!mysqli_num_rows(sql_get("SHOW TABLES LIKE '$tableId';")));
		$tableAdd="CREATE TABLE `$tableId` (";
		$tableContent=explode(',',$table[1]);
		$fileColumn=array();
		foreach ($tableContent as $line) {
			$line=explode('=>',$line,2);
			$key=rtrim($line[0]);
			$value=rtrim(str_replace(';',',',$line[1]));
			if ($key=='PRIMARY KEY') {$tableAdd.="PRIMARY KEY (`$value`), ";}
			else {
				$fileColumn[]=$key;
				$tableAdd.="`$key` $value, ";
				if (!$addTab AND !(mysqli_num_rows(sql_get("SHOW COLUMNS FROM `$tableId` LIKE '$key'") ))) {
					$nothingToDo=false;
					echo "<tr><td>$tableId</td><td>".$str['sqladdCol']." '$key'";
					$columnAdd="ALTER TABLE $tableId ADD COLUMN `$key` $value;";
					sql_get($columnAdd);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<div class='error'>".$str['error']."<div class='subError'>$columnAdd<br/><span class='gras'>$sqlError</span></div></div>";}
					echo "</td></tr>";}
				elseif (!$addTab) {
					$oldType=mysqli_fetch_assoc(sql_get("SHOW COLUMNS FROM `$tableId` LIKE '$key'"))['Type'];
					if (strtoupper($oldType) <> strtoupper(substr($value,0,strlen($oldType)))) {
						#Changement du type de la colonne.
						echo "<tr><td>$tableId</td><td>".$str['sqlChangeCol']." '$key'";
						sql_get("ALTER TABLE `$tableId` MODIFY `$key` $value"); 
						$nothingToDo=false;}}}}
		if ($addTab) {
			echo "<tr><td>$tableId</td><td>".$str['sqladdTable'].".";
			$tableAdd=substr($tableAdd,0,-2).") $engine";
			sql_get($tableAdd);
			$sqlError=mysqli_error($sqlConn);
			if ($sqlError!='') {echo "<div class='error'>".$str['error']."<div class='subError'>$tableAdd<br/><span class='gras'>$sqlError</span></div></div>";}
			echo "</td></tr>";}
		elseif ($nothingToDo) {
		  #Supression des colonne dipsarues.
          $oldTable=sql_get("SHOW COLUMNS FROM `$tableId`");
		  while($oldCol=mysqli_fetch_assoc($oldTable)) if (!in_array($oldCol['Field'],$fileColumn)) {
			sql_get('ALTER TABLE `'.$tableId.'` DROP COLUMN `'.$oldCol['Field'].'`');
			echo "<tr><td>$tableId</td><td>".$str['sqlDelCol']."</td></tr>";
			$nothingToDo=false;}
		if($nothingToDo) echo "<tr><td>$tableId</td><td>".$str['allFine']."</td></tr>";}}
	fclose($file);
	##Suppression des bases disparues.
	$oldTables=sql_get("SHOW TABLES");
	while($oldTable=mysqli_fetch_array($oldTables)) {
		if (!in_array($oldTable[0],$fileTable)) {
	  sql_get('DROP TABLE `'.$oldTable[0].'`');
	  echo '<tr><td>'.$oldTable[0].'</td><td>'.$str['sqlDelTable'].'</td></tr>';}}}	
	
include 'config.inc';
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
	  $zipFolder = $zip->getNameIndex(0);
      $zip->close();}
  else exit("<div class='error'>".$str['nozip2'].".<div class='subError'>".error_get_last()['message']."</div></div>");
  if (substr($zipFolder,-1,1)!='/') exit("<div class='error'>".$str['incorrectFormat'].".<div class='subError'>".$str['nozip3']."<br/>".$str['readDoc'].".</div></div>"); else $zipFolder=explode('/',$zipFolder)[0];
  $updateSourcePath="updates/$zipFolder/updates";
  $setupSourcePath="updates/$zipFolder/setup";
  $setupDate=array('date'=>new dateTime());
  $helpDate=array('date' => new dateTime());}
elseif (file_exists('dockerSetup')) {
	#insertion initiale de contenu pour Docker
	$updateSourcePath='dockerSetup';
	$setupSourcePath='docker';
	$setupDate=array('date'=>new dateTime());
	$helpDate=array('date'=>new dateTime());}
else {
  $updateSourcePath=$gitUrl.'/updates';
  $setupSourcePath=$gitUrl.'/setup';
  $setupDate=gitFileDate('/setup/setup.php');
  $helpDate=gitFileDate('/updates/aide.md');}
if ($setupSourcePath=='docker') echo "<div class='pannel'><div class='pannelTitle'>".$str['docker1']."</div>\n".$str['docker2'].".";
else {
  echo "<div class='pannel'><div class='pannelTitle'".$str['updateScript']."</div>";
  #Récupération de la dernière version du présent script
  if (isset($setupDate['erreur'])) echo "<div class='error'>".$str['gitHubError']."....<div class='subError'>".$setupDate['erreur']."</div></div>";
  elseif (new dateTime('@'.filemtime('setup.php'))<$setupDate['date'])  {
    echo $str['updateScript2'].".<br/>";
    if (@copy("$setupSourcePath/setup.php",'setup.php')) {
	  echo $str['updateScript2']." !<br>";
	  if (!isset($_POST['autoUpdate']) or $_POST['autoUpdate']=='oui') exit ("<a href='' class='button'>".$str['relaunch']."</a>");}
    else exit("<div class='error'>".$str['noCopy']."....<div class='subError'>".error_get_last()['message']."</div></div>");}
  else echo $str['allreadyUp'].".";}

#Mise a jour pour la version 1.5 : depuis le full SQL vers le cache AJAX en mode fichiers.
if (mysqli_num_rows(sql_get("SHOW TABLES LIKE 'parties'"))) {
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
		xmlAttr($xml,array('pUri'=>$sqlPartie['pUri'],'pMechant'=>$sqlPartie['pMechant'],'pMechVie'=>$sqlPartie['pMechVie'],'pMechPhase'=>$sqlPartie['pMechPhase'],'pDate'=>strtotime($sqlPartie['pDate']),'pPremier'=>$sqlPartie['pPremier'],'pManiDelete'=>$sqlPartie['pManiDelete'],'pManiCourant'=>$sqlPartie['pManiCourant'],'pManiMax'=>$sqlPartie['pManiMax'],'pManiAcceleration'=>$sqlPartie['pManiAcceleration'],'pMechRiposte'=>$sqlPartie['pMechRiposte'],'pMechPercant'=>$sqlPartie['pMechPercant'],'pMechDistance'=>$sqlPartie['pMechDistance'],'mNom'=>$mNom,'mpNom'=>$mpNom,'pMechDesoriente'=>$sqlPartie['pMechDesoriente'],'pMechSonne'=>$sqlPartie['pMechSonne'],'pMechTenace'=>$sqlPartie['pMechTenace']));
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

echo "</div><div class='pannel'><div class='pannelTitle'>".$str['gameHelpUp']."</div>";
if (isset($helpDate['erreur'])) echo "<div class='error'>".$str['gitHubError']."<div class='subError'>".$helpDate['erreur']."</div></div>";
elseif (!file_exists('aide.html') or (new dateTime('@'.filemtime('aide.html'))<$helpDate['date']))  {
  echo $str['gameHelpUp'].".";
  $helpFile="<!doctype html>\n<html lang='fr'>\n<head>\n<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>\n<meta charset='UTF-8'>\n<link rel='stylesheet' href='aide.css'>\n<link rel='icon' href='../favicon.ico'/>\n<title>Remote Champions - Aide</title>\n</head>\n<body>\n<div id='TDMUp'></div>";
  $file = @fopen ("$updateSourcePath/aide.md", "r");
  if (!$file) echo "<div class='error'>".$str['openFileErr'].".<div class='subError'>".$str['gameHelpUp2']." '$updateSourcePath/aide.md'.</div></div>";
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
	file_put_contents ('aide.html', $helpFile);}}
else echo $str['gameHelpUp3'].".";
echo "</div><div class='pannel'><div class='pannelTitle'>".$str['sqlDBUp']."</div><table><tr><th>Table</th><th>Action</th></tr>";
sqlUpdate("$updateSourcePath/sqlTables");
echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['sqlDBUp2']."</div><table><tr><th>Table</th><th>Action</th></tr>";
updateSQLcontent("$updateSourcePath/boites",'boites');
updateSQLcontent("$updateSourcePath/mechants",'mechants');
updateSQLcontent("$updateSourcePath/ManigancesPrincipales",'ManigancesPrincipales');
updateSQLcontent("$updateSourcePath/manigances",'manigances');
updateSQLcontent("$updateSourcePath/decks",'decks');
updateSQLcontent("$updateSourcePath/heros",'heros');
if ($updateSourcePath=='dockerSetup') {
	#Fin d'insertion Docker : nettoyage
    $files = glob($updateSourcePath.'/*',GLOB_MARK);
    foreach ($files as $file) unlink($file);
    rmdir($updateSourcePath);
	echo "</table></div>";}
else {
  echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['imgUp']."</div><table><tr><th>".$str['pic']."</th><th></th></tr>";
  imageUpdate('mechants','mId','mNom');
  imageUpdate('boites','bId','bNom');
  imageUpdate('heros','hId','hNom');
  echo "</table></div><div class='pannel'><div class='pannelTitle'>".$str['phpUp']."</div><table><tr><th>".$str['file']."</th><th></th></tr>";
  #Vérification des fichiers php par leur taille.
  $phpFiles=array('admin.php','ajax.php','ecran.css','favicon.ico','include.php','functions.php','index.php','joueur.php','mc.js','mechant.php','new.php','maniganceInfo.php','aide.css','img/amplification.png','img/counter.png','img/first.png','img/Menace+.png','img/MenaceAcceleration.png','img/MenaceCrise.png','img/MenaceRencontre.png','img/pointVert.png','img/refresh.png','img/save.png','img/saveB.png','img/load.png','img/smartphone.png','img/trash.png','img/link.png','img/bug.png','img/aide.png','img/pp.png','lang-fr.php','lang-en.php');
  foreach ($phpFiles as $phpFile) {
	$localSize=filesize($phpFile);
	$remoteSize = remoteFileSize($phpFile);
	echo "<tr><td>$phpFile</td><td>";
	if ($localSize<>$remoteSize) {
		echo $str['update'];
		if (!@copy("$gitUrl/setup/$phpFile",$phpFile)) echo "<div class='error'>".$str['noCopy']."....<div class='subError'>".error_get_last()['message']."</div></div>";}
	else echo $str['noUpdate'];
	echo "</td></tr>";}
echo "</table></div>";}
echo "<div class='pannel'><div class='pannelTitle'>".$str['endUpdate']."</div><a class='button' href='.'>".$str['restrictedBack']."</a></div>";
?>
</body>
</html>