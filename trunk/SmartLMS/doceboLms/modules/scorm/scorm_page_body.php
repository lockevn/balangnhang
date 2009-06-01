<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2007 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package course management
 * @subpackage course catalogue
 * @category ajax server
 * @version $Id:$
 *
 */

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

$path_to_root = '../..';

// prepare refer ------------------------------------------------------------------

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

if ($GLOBALS["where_cms_relative"] != false)
	$GLOBALS["where_cms_relative"]=$path_to_root.'/'.$GLOBALS["where_cms_relative"];

if ($GLOBALS["where_kms_relative"] != false)
	$GLOBALS["where_kms_relative"]=$path_to_root.'/'.$GLOBALS["where_kms_relative"];

if ($GLOBALS["where_lms_relative"] != false)
	$GLOBALS["where_lms_relative"]=$path_to_root.'/'.$GLOBALS["where_lms_relative"];

if ($GLOBALS["where_framework_relative"] != false)
	$GLOBALS["where_framework_relative"]=$path_to_root.'/'.$GLOBALS["where_framework_relative"];


if ($GLOBALS["where_files_relative"] != false) {
	$GLOBALS["where_files_relative"]=$path_to_root.'/'.$GLOBALS["where_files_relative"];
}
ob_start();

// connect to database -------------------------------------------------------------------

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] )
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) )
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

// load lms setting ------------------------------------------------------------------
require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_lms'].'/setting.php');

session_name("docebo_session");
session_start();

// load regional setting --------------------------------------------------------------
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session -----------------------------------------------------
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
require_once($GLOBALS['where_framework'].'/lib/lib.template.php');
require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

// language --- use organization module
$lang =& DoceboLanguage::createInstance('organization', 'lms');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"    
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
	
	<link href="<?php echo getPathTemplate(); ?>/style/style.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo getPathTemplate(); ?>/style/style_scormplayer.css" rel="stylesheet" type="text/css" />

	<link href="<?php echo getPathTemplate().'/player_scorm/'.$playertemplate; ?>/def_style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		function msgPrereqNotSatisfied( text ) {
			var elem = document.getElementById('prerequisites');
			elem.appendChild(document.createTextNode(text))
			elem.style.visibility = 'visible';
		}
		// inform the player 
		window.onload = function() {
			parent.scormPlayer.blankPageLoaded();
		}
	</script>
	</head>
	<body>
		<div id="bodynav">
			<div id="prerequisites" style="visibility: hidden" >
				<b><?php echo $lang->getLangText('_ORGLOCKEDTITLE') ?></b>
			</div>
			<br />
			<div id="prevblocklink">
				<a id="prevsco" href="#" onClick="parent.playprevclick(); return false;">
				<!--	<img src="<?php echo getPathImage(); ?>scorm/bt_sx.png" alt="prev" /> -->
					<span id="prevlink">
					</span>
				</a>
			</div>
			<div id="nextblocklink">
				<a id="nextsco" href="#" onClick="parent.playnextclick(); return false;">
					<span id="nextlink">
					</span>
				<!--	<img src="<?php echo getPathImage(); ?>scorm/bt_dx.png" alt="next" /> -->
				</a>
			</div>
		</div>
		<script type="text/javascript">
			if(parent.prevExist()) {
				var prev = document.getElementById('prevlink');
				prev.innerHTML = parent.scormPlayer.getPrevScoName();
			} else {
				var prev = document.getElementById('prevblocklink');
				prev.style.visibility = 'hidden';
			}
			if(parent.nextExist()) {
				var next = document.getElementById('nextlink');
				next.innerHTML = parent.scormPlayer.getNextScoName();
			} else {
				var next = document.getElementById('nextblocklink');
				next.style.visibility = 'hidden';
			}
		</script>
	</body>
</html>
<?php

// close database connection

mysql_close($GLOBALS['dbConn']);

ob_end_flush();

?>