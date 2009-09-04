<?php  /// Moodle Configuration File 

unset($CFG);

$CFG->dbtype    = 'mysql';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'smartlms';
$CFG->dbuser    = 'smartlms';
$CFG->dbpass    = 'guruunited2008';
$CFG->dbpersist =  true;
$CFG->prefix    = 'mdl_';

//$CFG->wwwroot   = 'http://127.0.0.1/smartlms';
//$CFG->dirroot   = 'D:\working\smartcom\eclipse_workspace\SmartLMS';
//$CFG->dataroot  = 'D:\working\smartcom\eclipse_workspace\SmartLMSData';

//$CFG->wwwroot   = 'http://192.168.2.198';
//$CFG->dirroot   = 'D:\code_workspace\SmartLMS';
//$CFG->dataroot  = 'D:\code_workspace\SmartLMSData';

$CFG->wwwroot   = 'http://smartlms.gurucore.com:8080';
$CFG->dirroot   = '/var/www/smartlms';
$CFG->dataroot  = '/var/www/smartlmsdata';


$CFG->admin     = 'admin';
$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

require_once($CFG->dirroot . "/lib/setup.php");

//error_reporting(E_ALL | E_NOTICE);

// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
?>