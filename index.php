<?php
session_start();
echo "<!doctype html>
    <html lang='fr'>
    <head>
      <META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>
      <META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>
      <meta charset='UTF-8'>
      <link rel='stylesheet' href='setup/ecran.css' content-type='text/css; charset=utf-8'>
      <link rel='icon' type='image/x-icon' href='setup/favicon.ico'/>
      <title>Connexion a Remote Champions sur Azure</title>
      <style type='text/css' media='screen'>
      .pannel a {font-weight:bold;color:black;text-decoration:none;display:inline-block;margin:10px;padding:5px;}
      .pannel .redButton {color:white;background:red;border:solid 2px white;}
      .pannel .blackButton {color:white;background:black;border:solid 2px white;}
      </style>
    </head>
    <body class='index'>
    <div class='pannel'><div class='titleAdmin'>";
if (isset($_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_IDP'])) $auth=array('id'=>$_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_ID'],'name'=>$_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME'],'idp'=>$_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_IDP']); else $auth=array('id'=>0,'name'=>'','idp'=>'');
if ($auth['idp']<>'') {
    $_SESSION['azureAuth']=$auth;
    include_once 'setup/config.inc';
    $_SESSION['publicPass']=$publicPass;
    //header("Refresh:0; setup/index.php");
    if ($auth['idp']=='aad') $auth['idp']='Microsoft';
    echo "Déconnexion du site de test</div>
    Vous êtes connecté avec un compte \"".ucwords($auth['idp'])."\".<br/>
    <a href='/setup' class='blackButton'>Aller au site de test!</a> / <a href='/.auth/logout?post_logout_redirect_uri=/index.php' class='redButton'>Déconnexion</a><br/>
    </div>";
    // google aad facebook
}
else {
    unset($_SESSION['publicPass']);
    unset($_SESSION['azureAuth']);
    echo "Connexion au site de test</div>
    <a href='/.auth/login/aad?post_login_redirect_uri=/index.php'><img src='img/ms-symbollockup_signin_dark.png' title='Connexion MS'></a><br/>
    <a href='/.auth/login/facebook?post_login_redirect_uri=/index.php'><img src='img/facebook_connect2_fr.png' title='Connexion FB'></a><br/>
    <a href='/.auth/login/google?post_login_redirect_uri=/index.php'><img src='img/btn_google_signin_dark_normal_web.png' title='Connexion Google'></a><br/></div>";
}
//print_r($_SERVER);

?>