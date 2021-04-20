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
	input {background:transparent;}
	input[type=submit] {font-size:1.5em;font-weight:bold;color:white;background-color:black;margin-top:10px;display:inline-block;}
	a.button {margin-left:calc(50% - 100px);width:200px;font-size:1.5em;font-weight:bold;color:black;background-color:darkgray;margin-top:10px;display:inline-block;padding:4px;text-decoration:none;border:solid 2px black;text-align:center;}
	a.button:hover {background-color:lightgray;}
  </style>
</head>
<body>
<?php
include_once('functions.php');
session_start();
$gitUrl='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main';
$adminPasswordInitial='8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918';
if (!file_exists('./config.inc')) {
	#Le fichier de paramètrage n'existe pas
	echo "<div class='pannel'><div class='pannelTitle'>Installation initiale</div>";
	$error='';
	if(!is_writable(__DIR__)) {$error="Ecriture impossible dans le répertoire '".__DIR__."', merci de vérifier!";}
	elseif (isset($_POST['serverName'])) {
		$configContent="<?php\nfunction sql_get(\$sqlQuery) {\n  global \$sqlConn;\n  \$sqlConn=mysqli_connect('".$_POST['serverName'].":".$_POST['serverPort']."','".$_POST['serverUser']."','".$_POST['serverPass']."','".$_POST['serverDb']."');\n  if(!\$sqlConn ) {die('Could not connect: '.mysqli_connect_error());}\n  \$sqlResult=mysqli_query(\$sqlConn,\$sqlQuery);\n  return \$sqlResult;}\n\$adminPassword='$adminPasswordInitial';\n\$publicPass='';\n?>";
		if (!isset($_POST['serverDb']) or $_POST['serverDb']==''){$error="Vous n'avez pas saisi de nom pour la base...";}
		elseif ($_POST['newDb']=='1') {
			#Création d'une nouvelle base de donnée
			$sqlConn=@mysqli_connect($_POST['serverName'].':'.$_POST['serverPort'],$_POST['serverUser'],$_POST['serverPass']);
			if (!$sqlConn){$error="Erreur,Connection impossible, merci de vérifier.<br/><div class='subError'>".mysqli_connect_error()."</div>";}
			else {
				$sqlQuery="CREATE DATABASE `".$_POST['serverDb']."`";
				mysqli_query($sqlConn,$sqlQuery);
				$sqlError=mysqli_error($sqlConn);
				if ($sqlError!='') {
					$error="Création de la nouvelle Base de données '".$_POST['serverDb']."' impossible.<div class='subError'><i>$sqlQuery</i> : $sqlError</div>";}
				else {
					file_put_contents ('config.inc',$configContent);
					$_SESSION['adminPassword']=$adminPasswordInitial;
					header("Refresh:0");}}}
		else {
			#Utilisation d'une base de donnée existante
			$sqlConn=@mysqli_connect($_POST['serverName'].':'.$_POST['serverPort'],$_POST['serverUser'],$_POST['serverPass'],$_POST['serverDb']);
			if (!$sqlConn){$error="Erreur,Connection impossible, merci de vérifier.<br/><div class='subError'>".mysqli_connect_error()."</div>";}
			else {
				file_put_contents ('config.inc',$configContent);
				$_SESSION['adminPassword']=$adminPasswordInitial;
				header("Refresh:0");}}}
	if (!isset($_POST['serverPort'])) $_POST['serverPort']='3306';
	echo "<form action='' method='post'>";
	if ($error<>'') echo"<div class='error'>$error</div>";
	echo "Le site n'est pas installé, veuillez saisir les informations de connexion au serveur mySql:<br/><table><tr><td>Nom/adresse du serveur :</td><td><input type='text' name='serverName' value='".$_POST['serverName']."'></td></tr><tr><td>Numéro de port du serveur :</td><td><input type='text' name='serverPort' value='".$_POST['serverPort']."'></td></tr><tr><td>Nom de connexion au serveur :</td><td><input type='text' name='serverUser' value='".$_POST['serverUser']."'></td></tr><tr><td>Mot de passe de connexion au serveur :</td><td><input type='password' name='serverPass' value='".$_POST['serverPass']."'></td></tr><tr><td><input type='radio' name='newDb' value='0'";
	if ($_POST['newDb']=='0') echo ' checked';
	echo ">Nom de la base existante<br><input type='radio' name='newDb' value='1'";
	if ($_POST['newDb']<>'0') echo ' checked';
	exit(">Nom de la base à créer :</td><td><input type='text' name='serverDb' value='".$_POST['serverDb']."'></td></tr><tr><td></td><td><input type='submit' value='valider'></td></tr></table>(Nota: le mot de passe administratif initial est 'admin', changez le dans la page d'administration.)</form>");}

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
	#Mise à jour des images du dossier
	global $updateSourcePath;
	$nothingToDo=true;
	if (!file_exists("img/$imgFolder")) {if(!mkdir("img/$imgFolder",0777,true)) {
		$nothingToDo=false;
		exit("<tr><td></td><div class='error'>Création de sous-répertoire dans '/img' impossible...</div></td></tr>");}}
	$images=sql_get("SELECT `$imgId`,`$imgNom` FROM `$imgFolder`");
	while ($image=mysqli_fetch_assoc($images)) {
		$imageFile="img/$imgFolder/".$image[$imgId].'.png';
		if (!file_exists($imageFile)) {
			$nothingToDo=false;
			echo "<tr><td>".$image[$imgNom]."</td><td>Ajout";
			if (!@copy("$updateSourcePath/$imageFile",$imageFile)) {echo "<div class='error'>Copie échouée....<div class='subError'>".error_get_last()['message']."</div></div>";}
			echo '</td></tr>';}}
	if ($nothingToDo) echo "<tr><td>dossier $imgFolder</td><td>Images au complet</td></tr>";}

function updateSQLcontent($tableFile,$tableId) {
	#Mise à jour (ajout, modification et suppression) du contenu d'une table fixe.
	global $sqlConn;
	$file = fopen ($tableFile, "r");
	if (!$file) exit("<div class='error'>Ouverture de fichier impossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire le fichier $tableFile'.</div></div>");
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
				echo "<tr><td>$tableId</td><td>Ajout de l'entrée `$cols[0]`='$line[0]'";
				$sqlQuery1='';
				$sqlQuery2=") VALUES (";
				foreach ($cols as $key=>$value) {
					$value=rtrim($value);
					$sqlQuery1.="`$value`, ";
					$sqlQuery2.="'".mysqli_real_escape_string($sqlConn,rtrim($line[$key]))."', ";}
				$sqlQuery="INSERT INTO `$tableId` (".substr($sqlQuery1,0,-2).substr($sqlQuery2,0,-2).')';
				sql_get($sqlQuery);
				$sqlError=mysqli_error($sqlConn);
				if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$sqlQuery<br/><b>$sqlError</b></div></div>";}
				echo "</td></tr>";}
			else {
				$news=false;
				$entry=mysqli_fetch_assoc($entry);
				foreach ($cols as $key=> $value) if ($entry[rtrim($value)]<>rtrim($line[$key]) and $value<>'bInclus') $news=true;
				#L'ajout $key<>'bInclus' permet de ne pas remplacer la valeur 'possédée' de la boite...
				if ($news) {
					$nothingToDo=false;
					echo"<tr><td>$tableId</td><td>Modification de l'entrée `$cols[0]`='$line[0]'";
					$sqlQuery="UPDATE `$tableId` SET ";
					foreach ($cols as $key=>$value) {if ($key<>0) {$sqlQuery.="`".rtrim($value)."`='".mysqli_real_escape_string($sqlConn,rtrim($line[$key]))."', ";}}
					$sqlQuery=substr($sqlQuery,0,-2)." WHERE `$cols[0]`='".mysqli_real_escape_string($sqlConn,rtrim($line[0]))."'";
					sql_get($sqlQuery);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$sqlQuery<br/><b>$sqlError</b></div></div>";}
					echo '<br/>';}}}}
	#Supression des enregistrements dipsarus.
	$oldTable=sql_get("SELECT `$cols[0]`, `$cols[1]` FROM $tableId");
	while($oldLine=mysqli_fetch_assoc($oldTable)) {
		if(!isset($newTable[$oldLine[$cols[0]]])) {
			$nothingToDo=false;
			echo "<tr><td>$tableId</td><td>Suppression de l'entrée '".$oldLine[$cols[0]]."'";
			sql_get ("DELETE FROM `$tableId` WHERE `$cols[0]`='".$oldLine[$cols[0]]."' AND `$cols[1]`='".$oldLine[$cols[1]]."'");
			$sqlError=mysqli_error($sqlConn);
			if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$sqlQuery<br/><b>$sqlError</b></div></div>";}
			echo "</td></tr>";}}
	if ($nothingToDo) echo "<tr><td>$tableId</td><td>Tout est en ordre</td></tr>";}

function sqlUpdate($sqlUpdateFile) {
#Récupération des éléments de structure SQL depuis référence gitHub et mise à jour/création dans la base
	global $sqlConn,$gitUrl;
	$engine='ENGINE=InnoDB DEFAULT CHARSET=utf8';
	$file = fopen ($sqlUpdateFile, "r");
	if (!$file) {
		exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire le fichier '$sqlUpdateFile'.</div></div>");}
	while (!feof ($file)) {
		$nothingToDo=true;
    	$table=explode('=>',fgets($file),2);
    	$tableId=$table[0];
    	$addTab=(!mysqli_num_rows(sql_get("SHOW TABLES LIKE '$tableId';")));
		$tableAdd="CREATE TABLE `$tableId` (";
		$tableContent=explode(',',$table[1]);
		foreach ($tableContent as $line) {
			$line=explode('=>',$line,2);
			$key=rtrim($line[0]);
			$value=rtrim(str_replace(';',',',$line[1]));
			if ($key=='PRIMARY KEY') {$tableAdd.="PRIMARY KEY (`$value`), ";}
			else {
				$tableAdd.="`$key` $value, ";
				if (!$addTab AND !(mysqli_num_rows(sql_get("SHOW COLUMNS FROM `$tableId` LIKE '$key'") ))) {
					$nothingToDo=false;
					echo "<tr><td>$tableId</td><td>Ajout de la colonne '$key'";
					$columnAdd="ALTER TABLE $tableId ADD COLUMN `$key` $value;";
					sql_get($columnAdd);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$columnAdd<br/><b>$sqlError</b></div></div>";}
					echo "</td></tr>";}}}
		if ($addTab) {
			echo "<tr><td>$tableId</td><td>Création de la table dans la base de donnée.";
			$tableAdd=substr($tableAdd,0,-2).") $engine";
			sql_get($tableAdd);
			$sqlError=mysqli_error($sqlConn);
			if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$tableAdd<br/><b>$sqlError</b></div></div>";}
			echo "</td></tr>";}
		elseif($nothingToDo) echo "<tr><td>$tableId</td><td>Déja à jour</td></tr>";}
	fclose($file);}	
	
include 'config.inc';
#Vérification du mot de passe d'administration.
if (!isset($_SESSION['adminPassword']) or $_SESSION['adminPassword']<>$adminPassword) {
	exit("<div class='pannel'><div class='pannelTitle'>Accès restreint</div>Désolé, l'accès à cette partie du site est protégé par un mot de passe...<br/><a class='button' href='.'>Retour au site</a></div>");}
clearstatcache();
#Gestion de la mise à jour par utilisation de fichiers locaux.
if (isset($_POST['autoUpdate']) and $_POST['autoUpdate']=='non') {
  if (!file_exists('updates')) {if(!mkdir('updates',0777,true)) exit("<div class='error'>Création de sous-répertoire 'updates' impossible...</div>");}
  $target_file = 'updates/'.basename($_FILES['zipUpdate']['name']);
  if(strtolower(pathinfo($target_file,PATHINFO_EXTENSION)) != 'zip') exit("<div class='error'>Fichier incorrect.<div class='subError'>Le fchier de mise à jour fourni ne semble pas être un fichier ZIP.<br/>Merci de consulter la documentation...</div></div>");
  $zip = new ZipArchive;
  if ($zip->open($_FILES['zipUpdate']['tmp_name']) === TRUE) {
      $zip->extractTo('updates/');
	  $zipFolder = $zip->getNameIndex(0);
      $zip->close();}
  else exit("<div class='error'>Décompression impossible.<div class='subError'>".error_get_last()['message']."</div></div>");
  if (substr($zipFolder,-1,1)!='/') exit("<div class='error'>Format incorrect.<div class='subError'>Le fichier zip fourni ne comprend pas de sous-répertoire avec l'ensemble du débot gitHub.<br/>Merci de consulter la documentation.</div></div>"); else $zipFolder=explode('/',$zipFolder)[0];
  $updateSourcePath="updates/$zipFolder/updates";
  $setupSourcePath="updates/$zipFolder/setup";
  $setupDate=array('date'=>new dateTime('@'.filemtime("updates/$zipFolder/setup/setup.php")));
  $helpDate=array('date' => new dateTime('@'.filemtime("updates/$zipFolder/updates/aide.md")));}
elseif (file_exists('dockerSetup')) {
	#insertion initiale de contenu pour Docker
	$updateSourcePath='dockerSetup';
	$setupSourcePath='dockersetup';
	$setupDate=array('date'=>0);
	$helpDate=array('date'=>0);}
else {
  $updateSourcePath='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main/updates';
  $setupSourcePath='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main/setup';
  $setupDate=gitFileDate('/setup/setup.php');
  $helpDate=gitFileDate('/update/aide.md');}
echo "<div class='pannel'><div class='pannelTitle'>Mise à jour du script d'installation</div>";
#Récupération de la dernière version du présent script
if (isset($setupDate['erreur'])) echo "<div class='error'>Echec de la requête gitHub....<div class='subError'>".$setupDate['erreur']."</div></div>";
elseif (new dateTime('@'.filemtime('setup.php'))<$setupDate['date'])  {
  echo "Nouvelle version du script de mise à jour.<br/>";
  if (@copy("$setupSourcePath/setup.php",'setup.php')) {
	  echo "Mise à jour du script de mise à jour !<br>";
	  if (!isset($_POST['autoUpdate']) or $_POST['autoUpdate']=='oui') exit ("<a href='' class='button'>Relancer la mise à jour</a>");}
  else exit("<div class='error'>Copie échouée....<div class='subError'>".error_get_last()['message']."</div></div>");}
else echo "Script Déjà à jour.";
echo "</div><div class='pannel'><div class='pannelTitle'>Mise à jour de l'aide</div>";
if (isset($helpDate['erreur'])) echo "<div class='error'>Echec de la requête gitHub....<div class='subError'>".$helpDate['erreur']."</div></div>";
elseif (!file_exists('aide.html') or (new dateTime('@'.filemtime('aide.html'))<$helpDate['date']))  {
  echo "Mise à jour de l'aide de jeu.";
  $helpFile="<!doctype html>\n<html lang='fr'>\n<head>\n<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>\n<meta charset='UTF-8'>\n<link rel='stylesheet' href='aide.css'>\n<link rel='icon' href='../favicon.ico'/>\n<title>Remote Champions - Aide</title>\n</head>\n<body>\n<div id='TDMUp'></div>";
  $file = @fopen ("$updateSourcePath/aide.md", "r");
  if (!$file) echo "<div class='error'>Ouverture de fichier ipossible.<div class='subError'>La mise en forme de l'aide a besoin que le moteur php puisse lire le fichier '$updateSourcePath/aide.md'.</div></div>";
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
else echo "Aide de jeu déjà à jour.";
echo "</div><div class='pannel'><div class='pannelTitle'>Vérification/mise à jour des tables SQL</div><table><tr><th>Table</th><th>Action</th></tr>";
sqlUpdate("$updateSourcePath/sqlTables");
echo "</table></div><div class='pannel'><div class='pannelTitle'>Vérification/mise à jour du contenu fixe de la base SQL</div><table><tr><th>Table</th><th>Action</th></tr>";
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
    rmdir($updateSourcePath);}
else {
  echo "</table></div><div class='pannel'><div class='pannelTitle'>Ajout des images manquantes</div><table><tr><th>Image</th><th></th></tr>";
  imageUpdate('mechants','mId','mNom');
  imageUpdate('boites','bId','bNom');
  imageUpdate('heros','hId','hNom');
  echo "</table></div><div class='pannel'><div class='pannelTitle'>Vérification des fichiers PHP</div><table><tr><th>Fichier</th><th></th></tr>";
  #Vérification des fichiers php par leur taille.
  $phpFiles=array('admin.php','ajax.php','ecran.css','favicon.ico','include.php','functions.php','index.php','joueur.php','mc.js','mechant.php','new.php','aide.css','img/amplification.png','img/counter.png','img/first.png','img/Menace+.png','img/MenaceAcceleration.png','img/MenaceCrise.png','img/MenaceRencontre.png','img/pointVert.png','img/refresh.png','img/save.png','img/smartphone.png','img/trash.png','img/link.png','img/bug.png','img/aide.png');
  foreach ($phpFiles as $phpFile) {
	$localSize=filesize($phpFile);
	$remoteSize = remoteFileSize($phpFile);
	echo "<tr><td>$phpFile</td><td>";
	if ($localSize<>$remoteSize) {
		echo "Mise à jour";
		if (!@copy("$gitUrl/setup/$phpFile",$phpFile)) echo "<div class='error'>Copie échouée....<div class='subError'>".error_get_last()['message']."</div></div>";}
	else echo "Déjà à jour";
	echo "</td></tr>";}
echo "</table></div>";}
echo "<div class='pannel'><div class='pannelTitle'>Fin d'installation/mise à jour</div><a class='button' href='.'>Accéder au site</a></div>";
?>
</body>
</html>