<?php

/*
 * SYSTEM
 */

//
//	Adresse du web service
//
$_CONFIG['serveur_webservice'] = "http://localhost:8080/suiviprod/services/SuiviProdPort";

if(getenv("WEBSERVICE_IP")){
    $_CONFIG['serveur_webservice'] = "http://".getenv("WEBSERVICE_IP").(getenv("WEBSERVICE_PORT")?":".getenv("WEBSERVICE_PORT"):"")."/suiviprod/services/SuiviProdPort";
}

//
// The starting directory. Normally no need to change this.
// Use only relative subdirectories!
// For example: $_CONFIG['starting_dir'] = "./mysubdir/";
// Default: $_CONFIG['starting_dir'] = ".";
//
$_CONFIG['starting_dir'] = ".";

if(getenv("ROOT_DIR")){
    $_CONFIG['starting_dir'] = getenv("ROOT_DIR");
}


//
// Location in the server. Usually this does not have to be set manually.
// Default: $_CONFIG['basedir'] = "";
//
$_CONFIG['basedir'] = $_CONFIG['serveur_webservice']."?method=getDossier&identite=";



//
// Big files. If you have some very big files (>4GB), enable this for correct
// file size calculation.
// Default: $_CONFIG['large_files'] = false;
//
$_CONFIG['large_files'] = true;

//
// The session name, which is used as a cookie name.
// Change this to something original if you have multiple copies in the same space
// and wish to keep their authentication separate.
// The value can contain only letters and numbers. For example: MYSESSION1
// More info at: http://www.php.net/manual/en/function.session-name.php
// Default: $_CONFIG['session_name'] = "";
//
$_CONFIG['session_name'] = "";

?>