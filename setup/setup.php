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
session_start();
$gitUrl='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main';
$adminPasswordInitial='8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918';
if (!file_exists('./config.inc')) {
	#Le fichier de paramètrage n'existe pas
	echo "<div class='pannel'><div class='pannelTitle'>Installation initiale</div>";
	$error='';
	if(!is_writable(__DIR__)) {$error="Ecriture impossible dans le répertoire '".__DIR__."', merci de vérifier!";}
	elseif (isset($_POST['serverName'])) {
		$configContent="<?php\nfunction sql_get(\$sqlQuery) {\n  global \$sqlConn;\n  \$sqlConn=mysqli_connect('".$_POST['serverName'].":".$_POST['serverPort']."','".$_POST['serverUser']."','".$_POST['serverPass']."','".$_POST['serverDb']."');\n  if(!\$sqlConn ) {die('Could not connect: '.mysqli_error());}\n  \$sqlResult=mysqli_query(\$sqlConn,\$sqlQuery);\n  return \$sqlResult;}\n\$adminPassword='$adminPasswordInitial';\n?>";
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
	if (!isset($_POST['serverPort'])) {$_POST['serverPort']='3306';}
	echo "<form action='' method='post'>";
	if ($error<>'') echo"<div class='error'>$error</div>";
	echo "Le site n'est pas installé, veuillez saisir les informations de connexion au serveur mySql:<br/><table><tr><td>Nom/adresse du serveur :</td><td><input type='text' name='serverName' value='".$_POST['serverName']."'></td></tr><tr><td>Numéro de port du serveur :</td><td><input type='text' name='serverPort' value='".$_POST['serverPort']."'></td></tr><tr><td>Nom de connexion au serveur :</td><td><input type='text' name='serverUser' value='".$_POST['serverUser']."'></td></tr><tr><td>Mot de passe de connexion au serveur :</td><td><input type='password' name='serverPass' value='".$_POST['serverPass']."'></td></tr><tr><td><input type='radio' name='newDb' value='0'";
	if ($_POST['newDb']=='0') {echo ' checked';}
	echo ">Nom de la base existante<br><input type='radio' name='newDb' value='1'";
	if ($_POST['newDb']<>'0') {echo ' checked';}
	exit(">Nom de la base à créer :</td><td><input type='text' name='serverDb' value='".$_POST['serverDb']."'></td></tr><tr><td></td><td><input type='submit' value='valider'></td></tr></table>(Nota: le mot de passe administratif initial est 'admin', changez le dans la page d'administration.)</form>");}

function remoteFileSize ($phpFile) {
	global $gitUrl;
	$remoteCall = curl_init("$gitUrl/setup/$phpFile");
	curl_setopt($remoteCall, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($remoteCall, CURLOPT_HEADER, TRUE);
	curl_setopt($remoteCall, CURLOPT_NOBODY, TRUE);
   	$data = curl_exec($remoteCall);
	$remoteSize = curl_getinfo($remoteCall, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	curl_close($remoteCall);
	return $remoteSize;}

function imageUpdate($imgFolder,$imgId,$imgNom) {
	#Mise à jour des images du dossier
	global $gitUrl;
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
			if (!@copy("$gitUrl/updates/$imageFile",$imageFile)) {echo "<div class='error'>Copie échouée....<div class='subError'>".error_get_last()['message']."</div></div>";}
			echo '</td></tr>';}}
	if ($nothingToDo) echo "<tr><td>dossier $imgFolder</td><td>Images au complet</td></tr>";}

function updateSQLcontent($tableId) {
	#Mise à jour (ajout, modification et suppression) du contenu d'une table fixe.
	global $sqlConn,$gitUrl;
	$file = fopen ("$gitUrl/updates/$tableId", "r");
	if (!$file) {
		exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire un fichier distant (http get).</div></div>");}
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

function sqlUpdate() {
#Récupération des éléments de structure SQL depuis référence gitHub et mise à jour/création dans la base
	global $sqlConn,$gitUrl;
	$engine='ENGINE=InnoDB DEFAULT CHARSET=utf8';
	$file = fopen ("$gitUrl/updates/sqlTables", "r");
	if (!$file) {
		exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire un fichier distant (http get).</div></div>");}
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
echo "<div class='pannel'><div class='pannelTitle'>Mise à jour du script d'installation</div>";
#Récupération de la dernière version du présent script
$localSize=filesize('setup.php');
$remoteSize = remoteFileSize('setup.php');

if ($localSize<>$remoteSize) {
	echo "Nouvelle version du script de mise à jour.<br/>";
	if (@copy("$gitUrl/setup/setup.php",'setup.php')) exit("Mise à jour du script de mise à jour !<br><a href='' class='button'>Relancer la mise à jour</a>");
	else exit("<div class='error'>Copie échouée....<div class='subError'>".error_get_last()['message']."</div></div>");}
else echo "Script Déjà à jour";

echo "</div><div class='pannel'><div class='pannelTitle'>Vérification/mise à jour des tables SQL</div><table><tr><th>Table</th><th>Action</th></tr>";
sqlUpdate();
echo "</table></div><div class='pannel'><div class='pannelTitle'>Vérification/mise à jour du contenu fixe de la base SQL</div><table><tr><th>Table</th><th>Action</th></tr>";
updateSQLcontent('boites');
updateSQLcontent('mechants');
updateSQLcontent('ManigancesPrincipales');
updateSQLcontent('manigances');
updateSQLcontent('decks');
updateSQLcontent('heros');
echo "</table></div><div class='pannel'><div class='pannelTitle'>Ajout des images manquantes</div><table><tr><th>Image</th><th></th></tr>";
imageUpdate('mechants','mId','mNom');
imageUpdate('boites','bId','bNom');
imageUpdate('heros','hId','hNom');
echo "</table></div><div class='pannel'><div class='pannelTitle'>Vérification des fichiers PHP</div><table><tr><th>Fichier</th><th></th></tr>";
#Vérification des fichiers php par leur taille.
$phpFiles=array('admin.php','ajax.php','ecran.css','favicon.ico','include.php','index.php','joueur.php','mc.js','mechant.php','new.php','aide.md','img/amplification.png','img/counter.png','img/first.png','img/Menace+.png','img/MenaceAcceleration.png','img/MenaceCrise.png','img/MenaceRencontre.png','img/pointVert.png','img/refresh.png','img/save.png','img/smartphone.png','img/trash.png','img/link.png','img/bug.png','img/aide.png');
foreach ($phpFiles as $phpFile) {
	$localSize=@filesize($phpFile);
	$remoteSize = remoteFileSize($phpFile);
	echo "<tr><td>$phpFile</td><td>";
	if ($localSize<>$remoteSize) {
		echo "Mise à jour";
		if (!@copy("$gitUrl/setup/$phpFile",$phpFile)) echo "<div class='error'>Copie échouée....<div class='subError'>".error_get_last()['message']."</div></div>";}
	else echo "Déjà à jour";
	echo "</td></tr>";}
echo "</table></div><div class='pannel'><div class='pannelTitle'>Fin d'installation/mise à jour</div><a class='button' href='.'>Accéder au site</a></div>";
?>
</body>
</html>