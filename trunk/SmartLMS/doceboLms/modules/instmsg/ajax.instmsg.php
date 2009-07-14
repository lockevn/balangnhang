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

if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

// here all the specific code ==========================================================

require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');
require_once($GLOBALS["where_scs"].'/lib/lib.html_chat_common.php');
$GLOBALS["chat_emo"] = new HtmlChatEmoticons();

$acl_man 	= $GLOBALS['current_user']->getAclManager();

$op = get_req('op', DOTY_ALPHANUM, '');
switch ($op) {
	case "getLang": {
		$lang =& DoceboLanguage::createInstance( 'course', 'lms');
		$lang =& DoceboLanguage::createInstance( 'instmsg', 'lms');
		$lang_obj='{'
			.'"_CHAT":"'.$lang->def('_CHAT').'",'
			.'"_SEND":"'.$lang->def('_SEND').'",'
			.'"_WHOIS_ONLINE":"'.$lang->def('_WHOIS_ONLINE', 'course', 'lms').'"'
		.'}';
		docebo_cout($lang_obj);
	};break;
	case "ping": {

		$id_receiver = get_req('id_receiver', DOTY_INT, 0);
		$name_receiver = get_req('name_receiver', DOTY_ALPHANUM, '');	

		$now = date("Y-m-d H:i:s");
					
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_tracksession 
		SET lastTime = '".$now."',
			ip_address = '".$_SERVER['REMOTE_ADDR']."'
		WHERE idEnter = '".$_SESSION['id_enter_course']."' "
			." AND idCourse = '".$_SESSION['idCourse']."' "
			." AND idUser = '".$id_receiver."'");
	
		
		/* extract lines */
		$query="SELECT * FROM ".$GLOBALS['prefix_lms']."_instmsg WHERE id_receiver='".$id_receiver."' AND status='0' ORDER BY data";
		$result = mysql_query($query);
		$msgs="";
		while ($row=mysql_fetch_array($result)) {
			ereg("^(.+)-(.+)-(.+) (.+):(.+):(.+)$",$row["data"],$parts);
			$hour=$parts[4];
			$min=$parts[5];
			$sec=$parts[6];

			$id_sender=$row["id_sender"];
			$userInfo=$acl_man->getUser($id_sender,'');
			$ids=split("/",$userInfo[1]);
			$name_sender=substr($userInfo[2],0,1).".".$userInfo[3];
						
			$m=stripslashes($row["msg"]);
			$line='{"timestamp":"'.$hour.':'.$min.':'.$sec.'","id_sender":"'.$id_sender.'","name_sender":"'.$ids[1].'","msg":"'.$m.'"},';
			$msgs.=$line;
		};
		
		/* set extracted lines status to: "old" */
		$query3="UPDATE ".$GLOBALS['prefix_lms']."_instmsg SET status='1' WHERE id_receiver='".$id_receiver."' AND status='0'";
		@mysql_query($query3);
		
		if ($msgs) $msgs=substr($msgs,0,strlen($msgs)-1);
		$msgs="[".$msgs."]";
		
		$list=getOnlineUsers();
		$content='{"content":'.$msgs.','.$list.'}';
		docebo_cout($content);
	};break;
	case "getUsersList": {
		$list="{".getOnlineUsers()."}";
		docebo_cout($list);
	};break;
	case "getContent": {
		$wChat 			= get_req('wChat', DOTY_ALPHANUM, '');
		$id_sender 		= get_req('id_sender', DOTY_INT, '');
		$id_receiver 	= get_req('id_receiver', DOTY_INT, '');
		$name_sender 	= get_req('name_sender', DOTY_ALPHANUM, '');
		$name_receiver 	= get_req('name_receiver', DOTY_ALPHANUM, '');
		
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
	};break;
	
	case "sendLine":
		$wChat=get_req('wChat', DOTY_ALPHANUM, '');
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
}


function getOnlineUsers() {
	global $acl_man;
	$whoIsOnlineList=TrackUser::getListWhoIsOnline($_SESSION['idCourse']);
	$first=true;

	$list='"list":[';	
	$emptylist=true;	
	for ($i=0;$i<count($whoIsOnlineList);$i++) {
			
			$idSt=$whoIsOnlineList[$i];
			
			//if ($GLOBALS["current_user"]->getIdSt()<>$idSt) {
			$emptylist=false;
			$userInfo=$acl_man->getUser($idSt,'');
			$ids=split("/",$userInfo[1]);
			$idUser=$ids[1];
			$userName=substr($userInfo[2],0,1).".".$userInfo[3];
			$list.='{"idSt":"'.$idSt.'","idUser":"'.$idUser.'","userName":"'.$userName.'"},';

			//};
	};
	if (!$emptylist) $list=substr($list,0,strlen($list)-1);
	$list.=']';
	return $list;
	
}
// =====================================================================================

?>