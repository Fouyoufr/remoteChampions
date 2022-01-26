<?php
header('Content-Type: application/xml; charset=utf-8');
$startTime=time();
session_write_close();
ignore_user_abort(false);
set_time_limit(60);

// Récupération de la partie sollicitée
if (isset($_GET['p'])) $partieFile='ajax/'.strtoupper(htmlspecialchars($_GET['p'])).'.xml';
if ((isset($partieFile) and !file_exists($partieFile)) or !isset($partieFile)) {
  echo '<?xml version="1.0" encoding="UTF-8"?><erreur>Le fichier "'.$partieFile.'" n\'existe pas...</erreur>';
  while (true);}
$index=0;
while ($index<30 and !isset($_GET['init'])) {
  //renvoi du fichier XML si modifié depuis le début
  if (filemtime($partieFile)>$startTime) {
    echo file_get_contents($partieFile);}
	clearstatcache();
	sleep(1);
   $index++;}
echo file_get_contents($partieFile); 
?>