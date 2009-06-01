<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

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
require_once($GLOBALS['where_framework']."/lib/lib.json.php");

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
require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

$GLOBALS['operation_result'] = '';
function docebo_cout($string) { $GLOBALS['operation_result'] .= $string; }

// here all the specific code ==========================================================
require_once($GLOBALS["where_scs"].'/lib/lib.html_chat_common.php');
$GLOBALS["chat_emo"]=new HtmlChatEmoticons();

$acl_man 	= $GLOBALS['current_user']->getAclManager();

$op = importVar('op');

switch ($op) {
	case "getUsersList":
		$im_platform =importVar('im_platform');
		$list="{".getOnlineUsers($im_platform)."}";
		docebo_cout($list);
		break;

	case "getContent":
		$wChat=importVar('wChat');
		$id_sender=importVar('id_sender');
		$id_receiver=importVar('id_receiver');
		$name_sender=importVar('name_sender');
		$name_receiver=importVar('name_receiver');

		/* delete lines older than 1 day */
		$query="DELETE FROM ".$GLOBALS['prefix_lms']."_instmsg WHERE ((id_receiver='".$id_receiver."' AND id_sender='".$id_sender."') OR (id_receiver='".$id_sender."' AND id_sender='".$id_receiver."')) AND DATE_ADD(data,INTERVAL 1 DAY) < NOW()";
		@mysql_query($query);


		/* extract lines */
		$query="SELECT * FROM ".$GLOBALS['prefix_lms']."_instmsg WHERE (id_receiver='".$id_receiver."' AND id_sender='".$id_sender."') OR (id_receiver='".$id_sender."' AND id_sender='".$id_receiver."') ORDER BY data";
		$result=mysql_query($query);
		$msgs="";
		while ($row=mysql_fetch_array($result)) {
			ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$row["data"],$parts);
			$hour=$parts[4];
			$min=$parts[5];
			$sec=$parts[6];
			$userClass="userB";
			$userName=$name_sender;
			if ($row["id_sender"]==$id_receiver) {
				$userClass="userA";
				$userName=$name_receiver;
			}
			$lineStatus="new";
			if ($row["status"]=='1') $lineStatus="old";

			$m=stripslashes($row["msg"]);
			$line='{"timestamp":"'.$hour.':'.$min.':'.$sec.'","userClass":"'.$userClass.'","userName":"'.$userName.'","lineStatus":"'.$lineStatus.'","msg":"'.$m.'"},';
			$msgs.=$line;
		};

		/* set extracted lines status to: "old" */
		$query="UPDATE ".$GLOBALS['prefix_lms']."_instmsg SET status='1' WHERE (id_receiver='".$id_receiver."' AND id_sender='".$id_sender."') OR (id_receiver='".$id_sender."' AND id_sender='".$id_receiver."')";
		@mysql_query($query);

		if ($msgs) $msgs=substr($msgs,0,strlen($msgs)-1);
		$msgs="[".$msgs."]";

		$list=
		$content='{
		"wChat":"'.$wChat.'",
		"content":'.$msgs.',
		"name_sender":"'.$name_sender.'",
		"id_sender":"'.$id_sender.'"
		}';
		docebo_cout($content);
		break;

	case "sendLine":
		$wChat=importVar('wChat');
		$id_sender=importVar('id_sender');
		$id_receiver=importVar('id_receiver');
		$msg=importVar('msg');
		$msg=htmlentities(addslashes(trim($msg)));
		//$msg=$GLOBALS["chat_emo"]->drawEmoticon($msg);

		$query="INSERT INTO ".$GLOBALS['prefix_lms']."_instmsg SET "
		." id_sender='$id_sender',"
		." id_receiver='$id_receiver',"
		." msg='$msg',"
		." status='0',"
		." data=now()";
		@mysql_query($query);
		docebo_cout("1");

		break;

	case "ping":

		$im_platform =importVar('im_platform');
		$id_receiver=importVar('id_receiver');
		$name_receiver=importVar('name_receiver');
		/*
		$query="SELECT * FROM ".$GLOBALS['prefix_fw']."_user WHERE  userid='/".$id_receiver."'";
		$result=mysql_query($query);
		$row=mysql_fetch_array($result);
		$id_user=$row["idst"];

		$now = date("Y-m-d H:i:s");

		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_tracksession
		SET lastTime = '".$now."',
			ip_address = '".$_SERVER['REMOTE_ADDR']."'
		WHERE idEnter = '".$_SESSION['id_enter_course']."' "
			."AND idCourse = '".$_SESSION['idCourse']."' AND idUser = '".$id_user."'");
		*/

		/* extract lines */
		$query="SELECT * FROM ".$GLOBALS['prefix_lms']."_instmsg WHERE id_receiver='".$id_receiver."' AND status='0' ORDER BY data";
		$result=mysql_query($query);
		$msgs="";
		while ($row=mysql_fetch_array($result)) {
			ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$row["data"],$parts);
			$hour=$parts[4];
			$min=$parts[5];
			$sec=$parts[6];

			$id_sender=$row["id_sender"];
			$query2="SELECT * FROM ".$GLOBALS['prefix_fw']."_user WHERE  userid='/".$id_sender."'";
			$result2=mysql_query($query2);
			$row2=mysql_fetch_array($result2);
			$idSt=$row2["idst"];

			$userInfo=$acl_man->getUser($idSt,'');
			$name_sender=substr($userInfo[ACL_INFO_FIRSTNAME],0,1).".".$userInfo[ACL_INFO_LASTNAME];

			$m=stripslashes($row["msg"]);
			$line='{"timestamp":"'.$hour.':'.$min.':'.$sec.'","id_sender":"'.$id_sender.'","name_sender":"'.$name_sender.'","msg":"'.$m.'"},';
			$msgs.=$line;
		};

		/* set extracted lines status to: "old" */
		$query3="UPDATE ".$GLOBALS['prefix_lms']."_instmsg SET status='1' WHERE id_receiver='".$id_receiver."' AND status='0'";
		@mysql_query($query3);

		if ($msgs) $msgs=substr($msgs,0,strlen($msgs)-1);
		$msgs="[".$msgs."]";

		$list=getOnlineUsers($im_platform);
		$content='{
		"content":'.$msgs.',
		'.$list.'
		}';
		docebo_cout($content);

		break;

	case "getLang":
		$lang =& DoceboLanguage::createInstance( 'instmsg', 'lms');
		$lang_obj='{
		"_CHAT":"'.$lang->def('_CHAT').'",
		"_SEND":"'.$lang->def('_SEND').'"
		}';
		docebo_cout($lang_obj);

		break;

	default:
		docebo_cout("default: $op");
		break;
}


function getOnlineUsers($im_platform) {
	global $acl_man;

	switch($im_platform) {

		case "lms": {
			$whoIsOnlineList=TrackUser::getListWhoIsOnline($_SESSION['idCourse']);
			$first=true;

			$list='"list":[';
			for ($i=0;$i<count($whoIsOnlineList);$i++) {

					$idSt=$whoIsOnlineList[$i];
					$userInfo=$acl_man->getUser($idSt,'');
					$idUser=$acl_man->relativeId($userInfo[ACL_INFO_USERID]);
					$userName=substr($userInfo[ACL_INFO_FIRSTNAME],0,1).".".$userInfo[ACL_INFO_LASTNAME];
					$list.='{"idSt":"'.$idSt.'","idUser":"'.$idUser.'","userName":"'.$userName.'"},';

			};
			if (count($whoIsOnlineList)) $list=substr($list,0,strlen($list)-1);
			$list.=']';
		} break;

		case "cms": {

			require_once($GLOBALS["where_framework"]."/lib/lib.myfriends.php");
			$friends = new MyFriends(getLogUserId());
			$arr_friends =$friends->getFriendsList();
			$arr_id_friends =array_keys($arr_friends);
			$data = new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
			$data->resetFieldFilter();
			$data->addFieldFilter('userid', '/Anonymous', '<>');
			$data->addFieldFilter('lastenter', date("Y-m-d H:i:s", time() - REFRESH_LAST_ENTER), '>');
			$data->setUserFilter($arr_id_friends);

			$q =$data->getRows();
			$list_arr =array();
			if (($q) && (mysql_num_rows($q))) {
				while($userInfo=mysql_fetch_row($q)) {

					$idUser=$acl_man->relativeId($userInfo[ACL_INFO_USERID]);
					$userName=substr($userInfo[ACL_INFO_FIRSTNAME],0,1).".".$userInfo[ACL_INFO_LASTNAME];
					$list_arr[]='{"idSt":"'.$idSt.'","idUser":"'.$idUser.'","userName":"'.$userName.'"}';
				}
			}
			$list ='"list":['.implode(",", $list_arr).']';
		} break;

	}
	return $list;

}
// =====================================================================================

// close database connection

mysql_close($GLOBALS['dbConn']);

ob_clean();
print($GLOBALS['operation_result']);
ob_end_flush();

?>