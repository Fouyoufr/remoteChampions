<?php
$startTime=time();
if (ini_get('max_execution_time')<60) $maxTime=$startTime+ini_get('max_execution_time')*.8; else $maxTime=$startTime+50;
header('Content-Type: application/xml; charset=utf-8');
session_write_close();
ignore_user_abort(false);


// Récupération de la partie sollicitée
if (isset($_GET['p'])) $partieFile='ajax/'.strtoupper(htmlspecialchars($_GET['p'])).'.xml';
if ((isset($partieFile) and !file_exists($partieFile)) or !isset($partieFile)) {
  echo '<?xml version="1.0" encoding="UTF-8"?><erreur>Le fichier "'.$partieFile.'" n\'existe pas...</erreur>';
  while (true);}
while (time()<$maxTime) {
  //renvoi du fichier XML si modifié depuis le début
  if (filemtime($partieFile)>$startTime) exit(file_get_contents($partieFile));
	clearstatcache();
	sleep(1);}
echo file_get_contents($partieFile); 
?>