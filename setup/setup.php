<?php
$title='Marvel Champions - installation';
if (!file_exists('./config.inc')) {
	#Le fichier de paramètrage n'existe pas
	$error='';
	if(!is_writable(__DIR__)) {$error="Ecriture impossible dans le répertoire '".__DIR__."', merci de vérifier!";}
	elseif (isset($_POST['serverName'])) {
		$configContent="<?php\nfunction sql_get(\$sqlQuery) {\n  global \$sqlConn;\n  \$sqlConn=mysqli_connect('".$_POST['serverName'].":".$_POST['serverPort']."','".$_POST['serverUser']."','".$_POST['serverPass']."','".$_POST['serverDb']."');\n  if(!\$sqlConn ) {die('Could not connect: '.mysql_error());}\n  \$sqlResult=mysqli_query(\$sqlConn,\$sqlQuery);\n  return \$sqlResult;}\n?>";
		if ($_POST['newDb']=='1') {
			#Création d'une nouvelle base de donnée
			$sqlConn=mysqli_connect($_POST['serverName'].':'.$_POST['serverPort'],$_POST['serverUser'],$_POST['serverPass']);
			if (!$sqlConn){$error="Erreur,Connection impossible, merci de vérifier";}
			else {
				$sqlQuery="CREATE DATABASE '".$_POST['newDb']."'";
				
				
				
				
			}}
		else {
			#Utilisation d'une base de donnée existante
			$sqlConn=mysqli_connect($_POST['serverName'].':'.$_POST['serverPort'],$_POST['serverUser'],$_POST['serverPass'],$_POST['serverDb']);
			if (!$sqlConn){$error="Erreur,Connection impossible, merci de vérifier";}
			else {
				
				file_put_contents ('config.inc',$configContent);
				header("Refresh:0");}}}
	if (!isset($_POST['serverPort'])) {$_POST['serverPort']='3306';}
	echo "<form action='' method='post'>Le site n'est pas installé, veuillez saisir les informations de connexion au serveur mySql:<br><b><font color='red' >$error</font></b><br/><table><tr><td>Nom/adresse du serveur :</td><td><input type='text' name='serverName' value='".$_POST['serverName']."'></td></tr><tr><td>Numéro de port du serveur :</td><td><input type='text' name='serverPort' value='".$_POST['serverPort']."'></td></tr><tr><td>Nom de connexion au serveur :</td><td><input type='text' name='serverUser' value='".$_POST['serverUser']."'></td></tr><tr><td>Mot de passe de connexion au serveur :</td><td><input type='password' name='serverPass' value='".$_POST['serverPass']."'></td></tr><tr><td><input type='radio' name='newDb' value='0'";
	if ($_POST['newDb']=='0') {echo ' checked';}
	echo ">Nom de la base existante<br><input type='radio' name='newDb' value='1'";
	if ($_POST['newDb']<>'0') {echo ' checked';}
	echo ">Nom de la base à créer :</td><td><input type='text' name='serverDb' value='".$_POST['serverDb']."'>(Non fonctionnel!)</td></tr><tr><td></td><td><input type='submit' value='valider'></td></tr></table></form>";
exit();}

function updateSQLcontent($tableId) {
	#Mise à jour (ajout et modification, pas de suppression) du contenu d'une table fixe.
	global $sqlConn;
	$file = fopen ("https://raw.githubusercontent.com/Fouyoufr/mc/main/updates/$tableId", "r");
	if (!$file) {exit("<p>Unable to open remote file '$tableId'.<br/>Update/Setup of mc needs your php engine to allow remote read.");}
	while (!feof ($file)) {
		$line=explode(',',fgets($file));
		if ($line[0][0]=='#') {
			$line[0]=substr($line[0],1);
			$cols=$line;}
		else {
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
					echo '<br/>';}}}}}

function sqlUpdate() {
#Récupération des éléments de structure SQL depuis référence gitHub et mise à jour/création dans la base
	global $sqlConn;
	$engine='ENGINE=InnoDB DEFAULT CHARSET=utf8';
	$file = fopen ("https://raw.githubusercontent.com/Fouyoufr/mc/main/updates/sqlTables", "r");
	if (!$file) {exit("<p>Unable to open remote file.<br/>Update/Setup of mc needs your php engine to allow remote read.");}
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
					echo "- Ajout de la colonne '$key' dans la table '$tableId'.";
					$columnAdd="ALTER TABLE $tableId ADD COLUMN `$key` $value;";
					sql_get($columnAdd);
					$sqlError=mysqli_error($sqlConn);
					if ($sqlError!='') {echo "<br/>$columnAdd<br/><b>$sqlError</b>";}
					echo "<br/>";}}}
		if ($addTab) {
			echo "- Création de la table '$tableId' dans la base de donnée.";
			$tableAdd=substr($tableAdd,0,-2).") $engine";
			sql_get($tableAdd);
			$sqlError=mysqli_error($sqlConn);
			if ($sqlError!='') {echo "<br/>$tableAdd<br/><b>$sqlError</b>";}
			echo "<br/>";}}
	fclose($file);}
	
	
	
echo "Installation / Mise à jour du site... patience...<br/>";
include 'config.inc';
$bodyClass='setup';
$currentVersion=mysqli_fetch_assoc(sql_get("SELECT `cfValue` FROM `config` WHERE `cfName`='version'"))['cfValue'];
#Récupération de la dernière version (le changement de version permet aussi la mise à jour du présent script)
$file=fopen("https://raw.githubusercontent.com/Fouyoufr/mc/main/updates/changelog.md","r");
$newVersion=rtrim(fgets($file));
fclose($file);
if ($currentVersion<>$newVersion) {
	#Mise à jour du script de mise à jour !
	echo "Mise à jour du script de mise à jour !<br><a href=''>Cliquer ici pour relancer la mise à jour avec le script en version '$newVersion'</a>";
    if (copy('https://raw.githubusercontent.com/Fouyoufr/mc/main/setup/setup.php','setup.php')) {
    	#Mise à jour de la version
		sql_get("REPLACE INTO `config` (`cfName`,`cfValue`) VALUES ('version','".$newVersion."')");}
    else {echo "<br/><b>Copie échouée....</b>";}
	exit();}
#Ajout/mise à jour des éléments initiaux dans les tables.		
sqlUpdate();
updateSQLcontent('mechants');
updateSQLcontent('ManigancesPrincipales');
updateSQLcontent('manigances');
updateSQLcontent('decks');
#Mise à jour des images des méchants
if (!file_exists('img/mechants')) {if(!mkdir('img/mechants',0777,true)) {
	echo "<br/><b>Création de sous-répertoire dans '/img' impossible...</b>";
	exit();}}
$mechants=sql_get("SELECT `mId`,`mNom` FROM `mechants`");
while ($mechant=mysqli_fetch_assoc($mechants)) {
	$mechantFile='img/mechants/'.$mechant['mId'].'.png';
	if (!file_exists($mechantFile)) {
		echo "Ajout de l'image de '".$mechant['mNom']."'.<br/>";
		if (!copy("https://raw.githubusercontent.com/Fouyoufr/mc/main/updates/$mechantFile",$mechantFile)) {echo "<br/><b>Copie échouée....</b>";}
		echo '<br/>';}}

echo "<hr/>Le site est prêt !<br/><a href='/'>Y accèder !</a>";
?>
</body>
</html>