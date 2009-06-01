<?php
/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2007 by Emanuele Sandri (emanuele@docebo.com)   		 */
/*																		 */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @module scormXmlTree.php
 *
 * @version $Id: scorm_page_tree.php 212 2006-04-06 14:57:52Z fabio $
 * @copyright 2007
 **/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

ob_start();

require_once(dirname(__FILE__) . '/../../config.php' );
require($GLOBALS['where_config'].'/config.php');
require_once(dirname(__FILE__) . '/config.scorm.php');

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] ) 
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($GLOBALS['dbname'], $GLOBALS['dbConn']) ) 
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_lms'].'/setting.php');

// activate debug if needed
if($GLOBALS['framework']['do_debug'] == 'on') {
	@error_reporting(E_ALL);
	@ini_set('display_errors', 1);
} else {
	@error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
}

//cookie lifetime
session_set_cookie_params( 0 );
//session lifetime ( max inactivity time )
ini_set('session.gc_maxlifetime', $GLOBALS['lms']['ttlSession']);

session_name("docebo_session");
session_start();

// load regional setting
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

															
$prefix = $GLOBALS['prefix_lms'];

//$GLOBALS['current_user']->loadUserSectionST('/lms/course/public/');

// Utils and so on
require_once($GLOBALS['where_lms'].'/lib/lib.php');


require_once($GLOBALS['where_lms'].'/lib/lib.preoperation.php');


if(!$GLOBALS['current_user']->isLoggedIn())// || !isset($_SESSION['idCourse'])) 
	die( "Malformed request" ); 

require_once(dirname(__FILE__) . '/scorm_utils.php'); 
require_once(dirname(__FILE__) . '/scorm_items_track.php');
require_once(dirname(__FILE__) . '/CPManagerDb.php'); 
require_once(dirname(__FILE__) . '/RendererXML.php'); 
 
$idscorm_organization = (int)$_GET['idscorm_organization'];
$idReference = (int)$_GET['idReference'];

$query = "SELECT ".$GLOBALS['prefix_lms']."_scorm_package.idscorm_package, path, org_identifier, scormVersion"
		." FROM ".$GLOBALS['prefix_lms']."_scorm_organizations, ".$GLOBALS['prefix_lms']."_scorm_package "
		." WHERE ".$GLOBALS['prefix_lms']."_scorm_organizations.idscorm_package = ".$GLOBALS['prefix_lms']."_scorm_package.idscorm_package"
		."   AND idscorm_organization = '".$idscorm_organization."'";

$resultProg=mysql_query( $query, $GLOBALS['dbConn'] );
if( !$resultProg ) die( "Error in query ". $query );

list($idscorm_package, $filepath, $organization, $scormVersion) = mysql_fetch_row($resultProg); 

ob_clean();

$it = new Scorm_ItemsTrack( $GLOBALS['dbConn'], $GLOBALS['prefix_lms'] );
$rb = new RendererXML();
$rb->idUser = getLogUserId();
$rb->itemtrack = $it;
$cpm = new CPManagerDb();

$filepath = dirname(__FILE__) . '/../../' . $filepath;
			
if( !$cpm->Open( $idReference, $idscorm_package, $GLOBALS['dbConn'], $GLOBALS['prefix_lms'] ) ) {
	echo 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
}
if( !$cpm->ParseManifest() ) {
    echo 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
}

$idUser = (int)getLogUserId();

$rb->resBase = $filepath . "/";
$cpm->RenderOrganization( $organization, $rb );

header("Content-Type: text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';


echo $rb->getOut();

if( $cpm->errCode != 0 )
	echo 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';

ob_end_flush();

exit;	// to avoid index.php to add additional and unuseful html

?>
