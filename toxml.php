<?php
$title='Remote Champions - convert SQL to XML';
include 'include.php';
include_once 'maniganceInfo.php';
global $str;
$error='';
$mainXML=<<<XML
		<?xml version='1.0' encoding='UTF-8'?>
		<remoteChampions></remoteChampions>
		XML;
  $xml=new SimpleXMLElement($mainXML);
$boxes=sql_get('SELECT * FROM `boites`');
while ($box=mysqli_fetch_assoc($boxes)) {
  $xmlBox=$xml->addChild('box');
  xmlAttr($xmlBox,array('id'=>$box['bId'],'name'=>$box['bNom'],'type'=>$box['bType'],'own'=>0));
  $mechants=sql_get('SELECT * FROM `mechants` WHERE `mBoite`=\''.$box['bId'].'\'');
  while($mechant=mysqli_fetch_assoc($mechants)) {
    $xmlMechant=$xmlBox->addChild('mechant');
    xmlAttr($xmlMechant,array('id'=>$mechant['mId'],'name'=>$mechant['mNom'],'vie1'=>$mechant['mVieMax1'],'vie2'=>$mechant['mVieMax2'],'vie3'=>$mechant['mVieMax3']));}

  $principales=sql_get('SELECT * FROM `ManigancesPrincipales` WHERE `mpBoite`=\''.$box['bId'].'\'');
  while($principale=mysqli_fetch_assoc($principales)) {
    $xmlPrincipale=$xmlBox->addChild('principale');
    xmlAttr($xmlPrincipale,array('id'=>$principale['mpId'],'name'=>$principale['mpNom'],'init'=>$principale['mpInit'],'initX'=>$principale['mpInit'],'max'=>$principale['mpMax'],'maxX'=>$principale['mpMaxMultiplie']));}

  $heros=sql_get('SELECT * FROM `heros` WHERE `hBoite`=\''.$box['bId'].'\'');
  while($hero=mysqli_fetch_assoc($heros)) {
    $xmlHeros=$xmlBox->addChild('heros');
    xmlAttr($xmlHeros,array('id'=>$hero['hId'],'name'=>$hero['hNom'],'vie'=>$hero['hVie']));
    $manigances=sql_get('SELECT * FROM `manigances` WHERE `maDeck`=0 AND `maNumero`=\''.$hero['hId'].'\'');
    while($manigance=mysqli_fetch_assoc($manigances)) {
      $xmlManigance=$xmlHeros->addChild('scheme');
      xmlAttr($xmlManigance,array('id'=>$manigance['maId'],'card'=>$hero['hId'],'name'=>$manigance['maNom'],'init'=>$manigance['maInit'],'initX'=>$manigance['maMultiplie'],'crise'=>$manigance['maCrise'],'rencontre'=>$manigance['maRencontre'],'accel'=>$manigance['maAcceleration'],'ampli'=>$manigance['maAmplification'],'entrave'=>$manigance['maEntrave'],'revele'=>$maniTxt[$manigance['maRevele']],'dejoue'=>$maniTxt[$manigance['maDejoue']],'info'=>$maniTxt[$manigance['maInfo']]));}}

  $decks=sql_get('SELECT * FROM `decks` WHERE `dBoite`=\''.$box['bId'].'\'');
  while($deck=mysqli_fetch_assoc($decks)) {
    $xmlDeck=$xmlBox->addChild('deck');
    xmlAttr($xmlDeck,array('id'=>$deck['dId'],'name'=>$deck['dNom']));
    $manigances=sql_get('SELECT * FROM `manigances` WHERE `maDeck`=\''.$deck['dId'].'\'');
    while($manigance=mysqli_fetch_assoc($manigances)) {
      $xmlManigance=$xmlDeck->addChild('scheme');
      xmlAttr($xmlManigance,array('id'=>$manigance['maId'],'card'=>$manigance['maNumero'],'name'=>$manigance['maNom'],'init'=>$manigance['maInit'],'initX'=>$manigance['maMultiplie'],'crise'=>$manigance['maCrise'],'rencontre'=>$manigance['maRencontre'],'accel'=>$manigance['maAcceleration'],'ampli'=>$manigance['maAmplification'],'entrave'=>$manigance['maEntrave'],'revele'=>$maniTxt[$manigance['maRevele']],'dejoue'=>$maniTxt[$manigance['maDejoue']],'info'=>$maniTxt[$manigance['maInfo']]));}}}

  $dom = dom_import_simplexml($xml)->ownerDocument;
  $dom->formatOutput = TRUE;
  $dom->save('boxes.xml');
?>
</body>
</html>