<?php
/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2008                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

define("IN_DOCEBO", true);

/**
 * @module scorm_frameset.php
 *
 * @author Emanuele Sandri
 * @version $Id$
 * @copyright 2008
 **/
 
/*Start buffer************************************************************/

ob_end_clean();
ob_start();

if($GLOBALS['current_user']->isLoggedIn() && isset($_SESSION['idCourse'])) {

require_once(dirname(__FILE__) . '/config.scorm.php');
require_once(dirname(__FILE__) . '/scorm_utils.php');
require_once(dirname(__FILE__) . '/scorm_items_track.php');

$idReference 	= $GLOBALS['idReference']; 
$idResource 	= $GLOBALS['idResource'];
$back_url 		= $GLOBALS['back_url'];
$autoplay 		= $GLOBALS['autoplay'];
$playertemplate = $GLOBALS['playertemplate'];

if($autoplay == '') $autoplay = '1';
if($playertemplate == '') $playertemplate = 'default';

if($playertemplate != '') {
	if(!file_exists(getPathTemplate().'player_scorm/'.$playertemplate.'/def_style.css')) {
		$playertemplate = 'default';
	}
} else {
	$playertemplate = 'default';
}

$idscorm_organization = $idResource;
$idUser = (int)getLogUserId();

/*Start database connection***********************************************/

$dbconn = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
mysql_select_db($GLOBALS['dbname'],$dbconn);

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $dbconn);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $dbconn);

/* get scorm version */
$scormVersion = getScormVersion( 'idscorm_organization', $idscorm_organization);

/* get object title */
list($lo_title) = mysql_fetch_row(mysql_query(	"SELECT title"
												." FROM ".$GLOBALS['prefix_lms']."_organization"
											  	." WHERE idResource = '$idResource'" 
											  	."   AND objectType = 'scormorg'"));

$itemtrack = new Scorm_ItemsTrack($dbconn, $GLOBALS['prefix_lms']);
$rsItemTrack = $itemtrack->getItemTrack($idUser,$idReference, NULL, $idscorm_organization);
if( $rsItemTrack === FALSE ) {
	// The first time for this user in this organization
	$itemtrack->createItemsTrack( $idUser, $idReference, $idscorm_organization );
	// Now should be present
	$rsItemTrack = $itemtrack->getItemTrack( $idUser, $idReference, NULL, $idscorm_organization );
}

$arrItemTrack = mysql_fetch_assoc( $rsItemTrack );
// with id_item_track of organization|user|reference create an entry in commontrack table
require_once( dirname(__FILE__) . '/../../class.module/track.object.php' );
require_once( dirname(__FILE__) . '/../../class.module/track.scorm.php' );
$track_so = new Track_ScormOrg( $arrItemTrack['idscorm_item_track'] );
if( $track_so->idReference === NULL )
	$track_so->createTrack( $idReference, $arrItemTrack['idscorm_item_track'], $idUser, date("Y-m-d H:i:s"), 'ab-initio', 'scormorg' );


/* info on number of items and setting of variables for tree hide/show */
$nItem = $arrItemTrack['nDescendant'];
$isshow_tree = ($nItem > 1) ? 'true':'false';
$class_extension = ($nItem > 1) ? '':'_hiddentree';

$lms_base_url = preg_replace("/:\/\/([A-Za-z0-9_:.]+)\//","://".$_SERVER['HTTP_HOST']."/",$GLOBALS['lms']['url']);
$lms_url = $lms_base_url.$scormws;
$xmlTreeUrl = $lms_base_url.$scormxmltree.'?idscorm_organization='.$idscorm_organization.'&idReference='.$idReference;
$imagesPath = getPathImage().'treeview/';

header("Content-Type: text/html; charset=utf-8");

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"'."\n";
echo '    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml">'."\n";
echo '<head>';
echo '	<title>'.$lo_title.'</title>';
// TODO: verificare se la prossima riga un problema con IIS
// echo '	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
//echo '	<link href="'.getPathTemplate().'/style/style.css" rel="stylesheet" type="text/css" />';
echo '	<link href="'.getPathTemplate().'/style/style_scormplayer.css" rel="stylesheet" type="text/css" />';

if(trim($playertemplate) != '') echo '	<link href="'.getPathTemplate().'/player_scorm/'.$playertemplate.'/def_style.css" rel="stylesheet" type="text/css" />';



	echo '<SCRIPT type="text/javascript" src="'.$GLOBALS['where_framework_relative'].'/addons/scriptaculous/lib/prototype.js"></SCRIPT>'."\n";

	echo '<SCRIPT type="text/javascript" src="'.$GLOBALS['where_lms_relative'].'/modules/scorm/ScormTypes.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.$GLOBALS['where_lms_relative'].'/modules/scorm/ScormCache.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.$GLOBALS['where_lms_relative'].'/modules/scorm/ScormApi.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.$GLOBALS['where_lms_relative'].'/modules/scorm/player.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.$GLOBALS['where_lms_relative'].'/modules/scorm/StdPlayer.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" >'."\n";
	echo '<!--'."\n";

	echo "var playerConfig = {\n";
	echo " autoplay: '$autoplay',\n";
	echo " backurl: '$back_url',\n";
	echo " xmlTreeUrl: '$xmlTreeUrl',\n";
	echo " host: '{$_SERVER['HTTP_HOST']}',\n";
	echo " lms_url: '$lms_url',\n";
	echo " lms_base_url: '$lms_base_url',\n";
	echo " scormserviceid: '$scormserviceid',\n";
	echo " scormVersion: '$scormVersion',\n";
	echo " idUser: '$idUser',\n";
	echo " idReference: '$idReference',\n";
	echo " idscorm_organization: '$idscorm_organization',\n";
	echo " imagesPath: '$imagesPath',\n";
	echo " idElemTree: 'treecontent',\n";
	//echo " idElemTitleCP: 'headtitle',\n";
	echo " idElemSco: 'scormbody',\n";
	echo " idElemScoContent: 'scocontent',\n";	
	//echo " idElemNavigation: 'headnav',\n";
	//echo " idElemProgress: 'statistics',\n";
	echo " idElemSeparator: 'separator',\n ";
	echo " showTree: '$isshow_tree',\n ";
	echo " playertemplate: '$playertemplate',\n";
	echo " useWaitDialog: '". (isset($GLOBALS['lms']['use_wait_dialog'])?$GLOBALS['lms']['use_wait_dialog']:"off") ."'\n";
	echo "};\n";
		
	echo 'window.onload = StdUIPlayer.initialize;'."\n";	
	echo ' // -->'."\n";
	echo '</SCRIPT>'."\n";
	
echo '</head>'."\n";

echo '<body id="page_head" class="'.$playertemplate.'" onunload="trackUnloadOnLms()">
	<div id="treecontent" class="treecontent'.$class_extension.' '.$playertemplate.'_menu" style="z-index: 4000;">
		<div class="menubox">Menu</div>
		<br />
	</div>
	<div id="separator" class="separator'.$class_extension.'" >
		<a id="sep_command" href="#" onclick="showhidetree();">
			<img src="'.$imagesPath.'../scorm/'.( ($nItem > 1) ? 'bt_sx' : 'bt_dx' ).'.png" alt="Expand/Collapse" />
		</a>
	</div>
	<div id="scocontent" class="scocontent'.$class_extension.'">
		<iframe id="scormbody" name="scormbody" frameborder="0" marginwidth="0" marginheight="0" framespacing="0">
		</iframe>
	</div>
</body>
</html>';

ob_end_flush();
exit;	// to avoid index.php to add additional and unuseful html
} 
?>
