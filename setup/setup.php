<!doctype html>
<html lang='fr'>
<head>
  <META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>
  <META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>
  <meta charset='UTF-8'>
  <link rel='icon' href='favicon.ico'/>
  <title>Remote Champions - Installation</title>
  <script language='JavaScript'>
  </script>
  <style>
	body {background-color:#7e696a;color:white;}
	.pannel {position:relative;border:solid 2px white;padding:2px;color:white;font-family:"Arial", Gadget, sans-serif;background:lightgray;margin-top:2px;border-color:black;color:black;font-weight:bold;font-size:1em;}
	.pannel table,.pannel tr {border-top: 1px solid #555;border-collapse: collapse;}
	.pannel tr:nth-child(odd) {background-color:bisque;}
	.pannel th {background-color: #555;color: #e5d2e5;}
	.pannelTitle {background-color:black;color:white;font-weight:bold;font-size:2em;margin:-2px;margin-bottom:2px;padding-left:10px;text-align:left;}
	.error {background-color:red;color:white;font-weight:bold;width:100%;text-align:center;font-size:1.5em;margin:-2px;padding:3px;}
	.subError {text-align:left;font-size:.5em;font-style:italic;color:lightgray;font-weight:normal;}
	input {background:transparent;}
	input[type=submit] {font-size:1.5em;font-weight:bold;color:white;background-color:black;margin-top:10px;}
  </style>
</head>
<body>
<?php
$gitUrl='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main';
if (!file_exists('./config.inc')) {
	#Le fichier de paramètrage n'existe pas
	echo "<div class='pannel'><div class='pannelTitle'>Installation initiale</div>";
	$error='';
	if(!is_writable(__DIR__)) {$error="Ecriture impossible dans le répertoire '".__DIR__."', merci de vérifier!";}
	elseif (isset($_POST['serverName'])) {
		$configContent="<?php\nfunction sql_get(\$sqlQuery) {\n  global \$sqlConn;\n  \$sqlConn=mysqli_connect('".$_POST['serverName'].":".$_POST['serverPort']."','".$_POST['serverUser']."','".$_POST['serverPass']."','".$_POST['serverDb']."');\n  if(!\$sqlConn ) {die('Could not connect: '.mysqli_error());}\n  \$sqlResult=mysqli_query(\$sqlConn,\$sqlQuery);\n  return \$sqlResult;}\n?>";
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
					header("Refresh:0");}}}
		else {
			#Utilisation d'une base de donnée existante
			$sqlConn=@mysqli_connect($_POST['serverName'].':'.$_POST['serverPort'],$_POST['serverUser'],$_POST['serverPass'],$_POST['serverDb']);
			if (!$sqlConn){$error="Erreur,Connection impossible, merci de vérifier.<br/><div class='subError'>".mysqli_connect_error()."</div>";}
			else {
				file_put_contents ('config.inc',$configContent);
				header("Refresh:0");}}}
	if (!isset($_POST['serverPort'])) {$_POST['serverPort']='3306';}
	echo "<form action='' method='post'>";
	if ($error<>'') echo"<div class='error'>$error</div>";
	echo "Le site n'est pas installé, veuillez saisir les informations de connexion au serveur mySql:<br/><table><tr><td>Nom/adresse du serveur :</td><td><input type='text' name='serverName' value='".$_POST['serverName']."'></td></tr><tr><td>Numéro de port du serveur :</td><td><input type='text' name='serverPort' value='".$_POST['serverPort']."'></td></tr><tr><td>Nom de connexion au serveur :</td><td><input type='text' name='serverUser' value='".$_POST['serverUser']."'></td></tr><tr><td>Mot de passe de connexion au serveur :</td><td><input type='password' name='serverPass' value='".$_POST['serverPass']."'></td></tr><tr><td><input type='radio' name='newDb' value='0'";
	if ($_POST['newDb']=='0') {echo ' checked';}
	echo ">Nom de la base existante<br><input type='radio' name='newDb' value='1'";
	if ($_POST['newDb']<>'0') {echo ' checked';}
	echo ">Nom de la base à créer :</td><td><input type='text' name='serverDb' value='".$_POST['serverDb']."'></td></tr><tr><td></td><td><input type='submit' value='valider'></td></tr></table></form>";
exit();}

function imageUpdate($imgFolder,$imgId,$imgNom) {
	#Mise à jour des images du dossier
	if (!file_exists("img/$imgFolder")) {if(!mkdir("img/$imgFolder",0777,true)) {
		echo "<br/><b>Création de sous-répertoire dans '/img' impossible...</b>";
		exit();}}
	$images=sql_get("SELECT `$imgId`,`$imgNom` FROM `$imgFolder`");
	while ($image=mysqli_fetch_assoc($images)) {
		$imageFile="img/$imgFolder/".$image[$imgId].'.png';
		if (!file_exists($imageFile)) {
			echo "Ajout de l'image de '".$image[$imgNom]."'.<br/>";
			if (!copy("$gitUrl/updates/$imageFile",$imageFile)) {echo "<br/><b>Copie échouée....</b>";}
			echo '<br/>';}}}

function updateSQLcontent($tableId) {
	#Mise à jour (ajout, modification et suppression) du contenu d'une table fixe.
	global $sqlConn,$gitUrl;
	$file = fopen ("$gitUrl/updates/$tableId", "r");
	if (!$file) exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire un fichier distant (http get).</div></div>");
	$newTable=array();
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
				echo "- Ajout de l'entrée `$cols[0]`='$line[0]' dans la table `$tableId`";
				$sqlQuery1='';
				$sqlQuery2=") VALUES (";
				foreach ($cols as $key=>$value) {
					$value=rtrim($value);
					$sqlQuery1.="`$value`, ";
					$sqlQuery2.="'".mysqli_real_escape_string($sqlConn,rtrim($line[$key]))."', ";}
				$sqlQuery="INSERT INTO `$tableId` (".substr($sqlQuery1,0,-2).substr($sqlQuery2,0,-2).')';
				sql_get($sqlQuery);
				$sqlError=mysqli_error($sqlConn);
				if ($sqlError!='') {echo "<br/>$sqlQuery<br/><b>$sqlError</b>";}
				echo "<br/>";}
			else {
				$news=false;
				$entry=mysqli_fetch_assoc($entry);
				foreach ($cols as $key=> $value) {if ($entry[rtrim($value)]<>rtrim($line[$key])) {$news=true;}}
				if ($news) {
					echo"- Modification de l'entrée `$cols[0]`='$line[0]' dans la table `$tableId`";
					$sqlQuery="UPDATE `$tableId` SET ";
					foreach ($cols as $key=>$value) {if ($key<>0) {$sqlQuery.="`".rtrim($value)."`='".mysqli_real_escape_string($sqlConn,rtrim($line[$key]))."', ";}}
					$sqlQuery=substr($sqlQuery,0,-2)." WHERE `$cols[0]`='".mysqli_real_escape_string($sqlConn,rtrim($line[0]))."'";
					sql_get($sqlQuery);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<br/>$sqlQuery<br/><b>$sqlError</b>";}
					echo '<br/>';}}}}
	#Supression des enregistrements dipsarus.
	$oldTable=sql_get("SELECT `$cols[0]`, `$cols[1]` FROM $tableId");
	while($oldLine=mysqli_fetch_assoc($oldTable)) {
		if(!isset($newTable[$oldLine[$cols[0]]])) {
			sql_get ("DELETE FROM `$tableId` WHERE `$cols[0]`='".$oldLine[$cols[0]]."' AND `$cols[1]`='".$oldLine[$cols[1]]."'");
			echo "Supression de l'entrée '".$oldLine[$cols[1]]."' de la table '$tableId'.<br/>";}}}

function sqlUpdate() {
#Récupération des éléments de structure SQL depuis référence gitHub et mise à jour/création dans la base
	global $sqlConn,$gitUrl;
	$engine='ENGINE=InnoDB DEFAULT CHARSET=utf8';
	$file = fopen ("$gitUrl/updates/sqlTables", "r");
	if (!$file) exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire un fichier distant (http get).</div></div>");
	while (!feof ($file)) {
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
					echo "<tr><td>Table <b>$tableId</b></td><td>Ajout de la colonne '$key'";
					$columnAdd="ALTER TABLE $tableId ADD COLUMN `$key` $value;";
					sql_get($columnAdd);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$columnAdd<br/><b>$sqlError</b></div></div>";}
					echo "</td></tr>";}}}
		if ($addTab) {
			echo "<tr><td>Table <b>$tableId</td><td>Création de la table dans la base de donnée.";
			$tableAdd=substr($tableAdd,0,-2).") $engine";
			sql_get($tableAdd);
			$sqlError=mysqli_error($sqlConn);
			if ($sqlError!='') {echo "<div class='error'>Erreur<div class='subError'>$tableAdd<br/><b>$sqlError</b></div></div>";}
			echo "</td></tr>";}}
	fclose($file);}	
	
include 'config.inc';
echo "<div class='pannel'><div class='pannelTitle'>Comparaison des versions</div>";
$currentVersion=sql_get("SELECT `cfValue` FROM `config` WHERE `cfName`='version'");
if ($currentVersion) {
	$allreadySetup=true;
	$currentVersion=mysqli_fetch_assoc($currentVersion)['cfValue'];}
else {$allreadySetup=false;}
#Récupération de la dernière version (le changement de version permet aussi la mise à jour du présent script)
$file=fopen("$gitUrl/updates/changelog.md","r");
if (!$file) exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>L'installation/Mise à jour de remoteChampions a besoin que le moteur php puisse lire un fichier distant (http get).</div></div>");
$newVersion=rtrim(fgets($file));
fclose($file);
echo "<ul><li>Version locale: $currentVersion<li>Version de référence: $newVersion</ul>";
if ($allreadySetup and $currentVersion<>$newVersion) {
	#Mise à jour du script de mise à jour !
    if (copy('$gitUrl/setup/setup.php','setup.php')) {
    	#Mise à jour de la version
		sql_get("REPLACE INTO `config` (`cfName`,`cfValue`) VALUES ('version','".$newVersion."')");
		echo "Mise à jour du script de mise à jour !<br><a href=''>Cliquer ici pour relancer la mise à jour avec le script en version '$newVersion'</a>";}
    else {echo "<div class='error'>Mise à jour du script '<i>setup.php</i>' impossible...</div>";}
	exit();}
echo "</div><div class='pannel'><div class='pannelTitle'>Vérification/mise à jour des tables SQL</div><table>";
sqlUpdate();
if (!$allreadySetup) {
	sql_get("INSERT INTO `config` (`cfName`,`cfValue`) VALUES ('version','".$newVersion."')");}
exit();
updateSQLcontent('boites');
updateSQLcontent('mechants');
updateSQLcontent('ManigancesPrincipales');
updateSQLcontent('manigances');
updateSQLcontent('decks');
updateSQLcontent('heros');

imageUpdate('mechants','mId','mNom');
imageUpdate('boites','bId','bNom');
imageUpdate('heros','hId','hNom');

#Vérification des fichiers php par leur taille.
$phpFiles=array('admin.php','ajax.php','ecran.css','favicon.ico','include.php','index.php','joueur.php','mc.js','mechant.php','img/amplification.png','img/counter.png','img/first.png','img/Menace+.png','img/MenaceAcceleration1.png','img/MenaceAcceleration2.png','img/MenaceCrise.png','img/MenaceRencontre.png','img/pointVert.png','img/refresh.png','img/save.png','img/smartphone.png');
foreach ($phpFiles as $phpFile) {
	$localSize=filesize($phpFile);
	$remoteCall = curl_init("$gitUrl/setup/$phpFile");
	curl_setopt($remoteCall, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($remoteCall, CURLOPT_HEADER, TRUE);
	curl_setopt($remoteCall, CURLOPT_NOBODY, TRUE);
   	$data = curl_exec($remoteCall);
	$remoteSize = curl_getinfo($remoteCall, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	curl_close($remoteCall);
	if ($localSize<>$remoteSize) {
		echo "Changement de taille du fichier '$phpFile' : remplacement de la version locale.";
		if (!copy("$gitUrl/setup/$phpFile",$phpFile)) {echo "<br/><b>Copie échouée....</b>";}
		echo '<br/>';
	}
}

echo "<hr/>Le site est prêt !<br/><a href='/'>Y accèder !</a>";
?>
</body>
</html>