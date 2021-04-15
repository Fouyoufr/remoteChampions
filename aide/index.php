<!doctype html>
<html lang='fr'>
<head>
  <META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>
  <META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>
  <meta charset='UTF-8'>
  <script type='text/javascript' src='index.js'></script>
  <link rel='stylesheet' href='index.css'>
  <link rel='icon' href='../favicon.ico'/>
  <title>Remote Champions - Aide</title>
</head>
<body>
<div id="TDMUp"></div>
<?php
#Récupération des éléments d'aide depuis référence gitHub avant création de la page
$gitUrl='https://raw.githubusercontent.com/Fouyoufr/remoteChampions/main/aide';
$gitUrl='.';
$file = @fopen ("$gitUrl/readme.md", "r");
if (!$file) exit("<div class='error'>Ouverture de fichier ipossible.<div class='subError'>La mise en forme de l'aide a besoin que le moteur php puisse lire un fichier distant (http get).</div></div>");
$luEncours=false;
$entryId=0;
$page='';
$numbEncours=false;
while (!feof ($file)) {
  $line=str_replace(["\r","\n"],'',fgets($file));
  $tableAdd=substr($tableAdd,0,-2).") $engine";
  if (substr($line,0,2)=='# ') {
    if ($luEncours) {
      $table.="</ul>\n";
      $luEncours=false;}
    if ($numbEncours) {
      $table.="</ol>";
      $numbEncours=false;}
      $entryId++;
      $table.="</div></div>\n<div id='aide$entryId' class='aideChapter'><div class='title' onclick='contentStyle=document.getElementById(this.parentElement.getElementsByClassName(\"content\")[0].id).style;if (contentStyle.display==\"block\") contentStyle.display=\"none\"; else contentStyle.display=\"block\";'>".substr($line,2)."</div>\n<div id='content$entryId' class='content'>\n";}
  elseif (substr($line,0,2)=='- ' or substr($line,0,5)=='   - ') {
    if (!$luEncours) {
        $luEncours=true;
        $table.="<ul>\n";}
    if (substr($line,0,5)=='   - ') $line=substr($line,3); elseif ($numbEncours) $table.="</ol>\n";
    $table.='<li>'.substr($line,2)."</li>\n";}
  elseif (substr($line,0,3)=='1. ') {
    if (!$numbEncours) {
        $numbEncours=true;
        $table.="<ol>\n";}
    if ($luEncours) {
      $luEncours=false;
      $table.="</ul>\n";}
      $table.='<li>'.substr($line,3)."</li>\n";}
  else {
    if ($luEncours) {
      $table.="</ul>\n";
      $luEncours=false;}
    if ($numbEncours) {
      $table.="</ol>\n";
      $numbEncours=false;}
    $table.=$line;
    if (substr($line,-2)=='  ') $table.="<br/>\n";}}
fclose($file);
$table=substr($table,13);
echo $table;
?>
</div>
    </div>
<div id="TDMDown"></div>
<a href="#" id="collapse" onclick="Array.from(document.getElementsByClassName('content')).forEach(content => content.style.display='block');">+</a>
<a href="#" id="moveUp">&#10148;</a>
</body>
</html>
