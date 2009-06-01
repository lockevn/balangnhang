<?php

/************************************************************************/
/* DOCEBO SCS - Synchronous Collaboration System							*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_scs'].'/setting.php');
require_once($GLOBALS['where_framework'].'/lib/lib.domxml5.php');

define("_TELESKILL_STREAM_TIMEOUT", "10");

define("TELESKILL_ROOMID", 		0);
define("TELESKILL_UID", 		1);
define("TELESKILL_ZONE", 		2);
define("TELESKILL_TITLE", 		3);
define("TELESKILL_START_DATE", 	4);
define("TELESKILL_END_DATE", 	5);
define("TELESKILL_BOOKABLE", 	6);
define("TELESKILL_CAPACITY", 	7);

class Teleskill_Management
{
	function Teleskill_Management() {

	}
	
	function _query($query) {

		$re = mysql_query($query);
		if(!$re) echo '<!-- Teleskill_Management - query : '.$query.' error : '.mysql_error().' -->';
		else echo '<!-- Teleskill_Management - query : '.$query.' -->';
		return $re;
	}
	
	function nextRow($re_room) {

		return mysql_fetch_array($re_room);
	}
	
	function _getRoomTable() {

		return $GLOBALS['prefix_scs'].'_teleskill';
	}

	function getRoomId($id_conference)
	{
		$query =	"SELECT roomid"
					." FROM ".$GLOBALS['prefix_scs']."_teleskill"
					." WHERE idConference = '".$id_conference."'";
		
		list($id_room) = mysql_fetch_row(mysql_query($query));
		
		return $id_room;
	}
	
	/**
	 * The only purpose of this function is to send the message to the server, read the server answer,
	 * discard the header and return the other content
	 *
	 * @param 	string	$xml_request 	is the xml request that will be sended to teleskill
	 *
	 * @return 	mixed 	the xml returned by teleskill or false if error
	 */
	function _sendXmlRequest($xml_request) {

		$xml_answer = false;

		$remote_url = $GLOBALS['scs']['url_checkin_teleskill'];
		$tmp_url = parse_url($remote_url);

		$post_data = urlencode('message').'='.urlencode($xml_request);

		$post_request = "POST $remote_url HTTP/1.0\r\n"
			."Host: ".$tmp_url['host']."\r\n"
			."User-Agent: PHP Script\r\n"
			."Content-type: application/x-www-form-urlencoded\r\n"
			."Content-length: ".strlen($post_data)."\r\n"
			."Connection: close\r\n\r\n"
			.$post_data."\r\n\r\n";

		$socket = fsockopen($tmp_url['host'], 80);

		if(!$socket) return false;
		socket_set_timeout($socket, _TELESKILL_STREAM_TIMEOUT);
		fputs($socket, $post_request);

		// discad header
		$head = fgets($socket);
		if(substr_count($head, "200 OK") > 0) {

			$hedaer_row = 0;
			while(!(fgets($socket) == "\r\n") && $hedaer_row < 100) { ++$hedaer_row; }
			if($hedaer_row == 100) return false;
		} else return false;
		while(!feof($socket)) {

			$xml_answer .= fgets($socket, 4096);
		}
		fclose($socket);

		return $xml_answer;
	}

	/**
	 * send a message type 5 to the server and read the answer
	 * @param 	datetime 	$start_time 	room start time
	 * @param 	datetime 	$end_time 		room end time
	 * @param	int			$capacity		room capacity
	 *
	 * @return	mixed 		true if the user can open the room, else return an array with
	 *						array( errorcode => 1, errormessage => string, 'roomid' => '')
	 */
	
	function canOpenRoom($start_time, $end_time, $capacity)
	{
		$bw_code = getBrowserCode();
		$pos = strpos($bw_code, ';');
		if($pos !== false) $bw_code = substr($bw_code, 0, $pos);

		$request = '<?xml version="1.0" encoding="utf-8"?'.'>
		<ews type="5" lang="'.$bw_code.'">
			<clientcode>'.$GLOBALS['scs']['code_teleskill'].'</clientcode>
			<startdate>'.$start_time.'</startdate>
			<enddate>'.$end_time.'</enddate>';
		if($capacity) 	$request .= '	<users>'.$capacity .'</users>';
		$request .= '</ews>';

		$xml_answer = $this->_sendXmlRequest($request);

		if($xml_answer === false) return array('errorcode' => -1, 'errormessage' => '', 'roomid' => '');

		$dom_answer = new DoceboDOMDocument();
		$dom_answer->loadXML( trim($xml_answer) );

		$dlist_code = $dom_answer->getElementsByTagName('errorcode');
		$dlist_msg 	= $dom_answer->getElementsByTagName('errormessage');
		$dnode_code = $dlist_code->item(0);
		$dnode_msg 	= $dlist_msg->item(0);

		$e_code 	= $dnode_code->textContent;
		$e_msg 		= $dnode_msg->textContent;

		if($e_code == 0) return true;

		return array('errorcode' => $e_code, 'errormessage' => $e_msg, 'roomid' => '');
	}

	/**
	 * send a message type 1 to the server and read the answer
	 * @param 	int 		$uid 			unique identifier for the zone
	 * @param 	string 		$lms 			the label of the zone
	 * @param 	string 		$title 			the room title
	 * @param 	datetime 	$start_date 	room start datetime (Y-m-d H:i:s)
	 * @param 	datetime 	$end_date 		room end datetime (Y-m-d H:i:s)
	 * @param 	string 		$descr 			the room description
	 * @param 	string 		$logo 			the absolute url of the logo
	 * @param 	int			$bookable		if set to 1 the room can be booked; just a flag for module frontend
	 * @param 	int			$capacity		highest number of users in chat if bookable.
	 *
	 * @return array	return an array
	 *					array( errorcode => int, errormessage => string, roomid => int )
	 */
	function openRoom($idConference,$title, $start_date, $end_date, $descr = false, $logo = false,$capacity='', $skin = '') {

		$bw_code = getBrowserCode();
		$pos = strpos($bw_code, ';');
		if($pos !== false) $bw_code = substr($bw_code, 0, $pos);

		$request = ''
		.'<?xml version="1.0" encoding="utf-8"?'.'>
		<ews type="1" lang="'.$bw_code.'">
			<clientcode>'.$GLOBALS['scs']['code_teleskill'].'</clientcode>
			<startdate>'.$start_date.'</startdate>
			<enddate>'.$end_date.'</enddate>
			<title>'.$title.'</title>';
		if($descr != false) $request .= '	<descr>'.$descr.'</descr>';
		if($logo != false) 	$request .= '	<logo>'.$logo .'</logo>';
		if($capacity) 	$request .= '	<users>'.$capacity .'</users>';
		if($skin)	$request .= '	<skin absolute="1">'.$skin .'</skin>';
		$request .= '</ews>';
		
		$can_open = $this->canOpenRoom($start_date, $end_date, $capacity);
		
		if($can_open !== true)
			return $can_open;
		
		$xml_answer = trim($this->_sendXmlRequest($request));

		if($xml_answer === false) return array('errorcode' => -1, 'errormessage' => '', 'roomid' => '');

		$dom_answer = new DoceboDOMDocument();
		$dom_answer->loadXML( $xml_answer );

		$dlist_code 	= $dom_answer->getElementsByTagName('errorcode');
		$dlist_msg 		= $dom_answer->getElementsByTagName('errormessage');
		$dlist_roomid 	= $dom_answer->getElementsByTagName('roomid');
		$dnode_code 	= $dlist_code->item(0);
		$dnode_msg 		= $dlist_msg->item(0);
		$dnode_roomid 	= $dlist_roomid->item(0);

		$e_code 	= $dnode_code->textContent;
		$e_msg 		= $dnode_msg->textContent;
		$roomid 	= $dnode_roomid->textContent;
		
		
						
		if($e_code == 0)
		{
			//save in database the roomid for user login

			$insert_room = "
			INSERT INTO ".$this->_getRoomTable()."
			( idConference, roomid ) VALUES (
				'".$idConference."',
				'".$roomid."'
			)";

			$re = $this->_query($insert_room);
			if(!$re) {
				//the room record isn't saved ...

			}
		}
		
		return array(	'errorcode' => $e_code,
						'errormessage' => $e_msg,
						'roomid' => $roomid );
	}
	
	/**
	 * send a message type 6 to the server and read the answer
	 * @param 	int 		$uid 			unique identifier for the zone
	 * @param 	string 		$lms 			the label of the zone
	 * @param 	string 		$title 			the room title
	 * @param 	datetime 	$start_date 	room start datetime (Y-m-d H:i:s)
	 * @param 	datetime 	$end_date 		room end datetime (Y-m-d H:i:s)
	 * @param 	string 		$descr 			the room description
	 * @param 	string 		$logo 			the absolute url of the logo
	 * @param 	int			$bookable		if set to 1 the room can be booked; just a flag for module frontend
	 * @param	int			$capacity		highest number of users in chat if bookable.
	 *
	 * @return array	return an array
	 *					array( errorcode => int, errormessage => string, roomid => int )
	 */
	
	function updateRoom($roomid, $uid, $title, $start_date, $end_date, $descr = false, $logo = false, $capacity, $bookable = 0) {

		$bw_code = getBrowserCode();
		$pos = strpos($bw_code, ';');
		if($pos !== false) $bw_code = substr($bw_code, 0, $pos);
		
		$teleskill_room_id = $this->getRoomId($roomid);
		
		$request = ''
		.'<?xml version="1.0" encoding="utf-8"?'.'>
		<ews type="6" lang="'.$bw_code.'">
			<clientcode>'.$GLOBALS['scs']['code_teleskill'].'</clientcode>
			<roomid>'.$teleskill_room_id.'</roomid>
			<startdate>'.$start_date.'</startdate>
			<enddate>'.$end_date.'</enddate>
			<title>'.$title.'</title>';
		if($descr != false) $request .= '	<descr>'.$descr.'</descr>';
		if($logo != false) 	$request .= '	<logo>'.$logo .'</logo>';
		$request .= '	<users>'.$capacity .'</users>';
		$request .= '</ews>';
		
		$xml_answer = trim($this->_sendXmlRequest($request));
			
		if($xml_answer === false) return array('errorcode' => -1, 'errormessage' => '', 'roomid' => '');

		$dom_answer = new DoceboDOMDocument();
		$dom_answer->loadXML( $xml_answer );

		$dlist_code 	= $dom_answer->getElementsByTagName('errorcode');
		$dlist_msg 		= $dom_answer->getElementsByTagName('errormessage');
		$dnode_code 	= $dlist_code->item(0);
		$dnode_msg 		= $dlist_msg->item(0);

		$e_code 	= $dnode_code->textContent;
		$e_msg 		= $dnode_msg->textContent;

		if($e_code == 0) {

			//save in database the roomid for user login
			$update_room = "
			UPDATE ".$GLOBALS['prefix_scs']."_room
			SET title = '".$title."',
				start_date = '".$start_date."',
				end_date = '".$end_date."',
				bookable = '".$bookable."',
				capacity = '".$capacity."'
			WHERE uid = '".$uid."'
				AND id = '".$roomid."'";

			$re = $this->_query($update_room);
			if(!$re) {
				//the room record isn't saved ...

			}
		}
		return array(	'errorcode' => $e_code,
						'errormessage' => $e_msg );
	}
	
	/**
	 * send a message type 1 to the server and read the answer
	 * @param 	int 	$roomid 	the room identifier
	 * @param 	int 	$role 		the role in the room 1 = normal, 2 = tutor
	 * @param 	string 	$userid 	the userid
	 * @param 	string 	$user_name 	the user real name
	 * @param 	string 	$email 		the user email
	 *
	 * @return	array	return an array
	 *					array( errorcode => int, errormessage => string, url => string, fullroom => int )
	 *					if fullroom == 1 the room is full
	 */
	function loginIntoRoom($roomid, $role, $userid, $user_name, $email = false) {

		$bw_code = getBrowserCode();
		$pos = strpos($bw_code, ';');
		if($pos !== false) $bw_code = substr($bw_code, 0, $pos);

		$request = ''
		.'<?xml version="1.0" encoding="utf-8"?'.'>
		<ews type="3" lang="'.$bw_code.'">
			<clientcode>'.$GLOBALS['scs']['code_teleskill'].'</clientcode>
			<roomid>'.$roomid.'</roomid>
			<lmsuserid>'.$userid.'</lmsuserid>
			<role>'.$role.'</role>
			<name>'.$user_name.'</name>';
		$request .= ' <email>'.$email.'</email >';
		$request .= '</ews>';

		$xml_answer = trim($this->_sendXmlRequest($request));

		if($xml_answer === false) return array('errorcode' => -1, 'errormessage' => '', 'url' => '', 'fullroom' => 0);

		$dom_answer = new DoceboDOMDocument();
		$dom_answer->loadXML( $xml_answer );
		$dlist_code 	= $dom_answer->getElementsByTagName('errorcode');
		$dlist_msg 		= $dom_answer->getElementsByTagName('errormessage');
		$dlist_url 		= $dom_answer->getElementsByTagName('url');
		$dlist_fullroom	= $dom_answer->getElementsByTagName('fullroom');
		$dnode_code 	= $dlist_code->item(0);
		$dnode_msg 		= $dlist_msg->item(0);
		$dnode_url 		= $dlist_url->item(0);
		$dnode_fullroom = $dlist_fullroom->item(0);

		$e_code 	= $dnode_code->textContent;
		$e_msg 		= $dnode_msg->textContent;
		$log_url 	= $dnode_url->textContent;
		$fullroom 	= $dnode_fullroom->textContent;

		return array('errorcode' => $e_code, 'errormessage' => $e_msg, 'url' => $log_url, 'fullroom' => $fullroom);
	}
	
	/**
	 * send a message type 2 to the server and read the answer
	 * @param 	int 	$roomid 	the room identifier
	 *
	 * @return	array 	return an array
	 *					array( errorcode => int, errormessage => string )
	 */
	function deleteRemoteRoom($idConference) {

		$room_open = "
		SELECT * FROM ".$this->_getRoomTable()."
		WHERE idConference = '".$idConference."'  ";
		$re_room=$this->_query($room_open);
		$conf=$this->nextRow($re_room);
		$roomid=$conf["roomid"];
			
		$bw_code = getBrowserCode();
		$pos = strpos($bw_code, ';');
		if($pos !== false) $bw_code = substr($bw_code, 0, $pos);

		$request = ''
		.'<?xml version="1.0" encoding="utf-8"?'.'>
		<ews type="2" lang="'.$bw_code.'">
			<clientcode>'.$GLOBALS['scs']['code_teleskill'].'</clientcode>
			<roomid>'.$roomid.'</roomid>';
		$request .= '</ews>';

		$xml_answer = trim($this->_sendXmlRequest($request));

		if($xml_answer === false) return array('errorcode' => -1, 'errormessage' => '');

		$dom_answer = new DoceboDOMDocument();
		$dom_answer->loadXML( $xml_answer );
		$dlist_code 	= $dom_answer->getElementsByTagName('errorcode');
		$dlist_msg 		= $dom_answer->getElementsByTagName('errormessage');
		$dnode_code 	= $dlist_code->item(0);
		$dnode_msg 		= $dlist_msg->item(0);

		$e_code 	= $dnode_code->textContent;
		$e_msg 		= $dnode_msg->textContent;
		
		
		$room_open = "
			DELETE FROM ".$this->_getRoomTable()."
			WHERE roomid = '".$roomid."'  ";
		$this->_query($room_open);
		
		
		return array('errorcode' => $e_code, 'errormessage' => $e_msg);
	}

	/**
	 * create a new room and log the user
	 * $uid, $zone,
	 * @param 	string 		$title 			the room title
	 * @param 	datetime 	$start_date 	room start datetime (Y-m-d H:i:s)
	 * @param 	datetime 	$end_date 		room end datetime (Y-m-d H:i:s)
	 * @param 	int 		$roomid 		the room identifier
	 * @param 	int 		$role 			the role in the room 1 = normal, 2 = tutor
	 * @param 	string 		$userid 		the userid
	 * @param 	string 		$user_name 		the user real name
	 * @param 	string 		$email 			the user email
	 * @param 	string 		$descr 			the room description
	 * @param 	string 		$logo 			the absolute url of the logo
	 *
	 * @return  mixed 		return an array
	 *						array( errorcode => int, errormessage => string, url => string ) or false
	 */
/*
	function createRoomAndLogin($uid, $zone, $title, $start_date, $end_date, $userid, $user_name, $role, $email = false, $descr = false, $logo = false) {

		$re_room = $this->openRoom($uid, $zone, $title, $start_date, $end_date, $descr, $logo);

		if($re_room === false) return false;
		if($re_room['errorcode'] != 0) {

			unset($re_room['roomid']);
			$re_room['url'] = '';
			return $re_room;
		}

		$re_login = $this->loginIntoRoom($re_room['roomid'], $role, $userid, $user_name, $email);

		return $re_login;
	}
	

	function roomInfo($uid, $zone, $room_id) {

		$room_open = "
		SELECT roomid, uid, zone, title, start_date, end_date, bookable, capacity
		FROM ".$this->_getRoomTable()."
		WHERE uid = '".$uid."' AND zone = '".$zone."' AND roomid = '".$room_id."'";
		$re_room = $this->_query($room_open);

		return $this->nextRow($re_room);
	}

	function roomActive($uid, $zone, $at_date = false) {

		$room_open = "
		SELECT roomid, uid, zone, title, start_date, end_date, bookable, capacity 
		FROM ".$this->_getRoomTable()."
		WHERE uid = '".$uid."' AND zone = '".$zone."'";
		if($at_date !== false) {

			$room_open .= " AND start_date <= '".$at_date."' AND '".$at_date."' <= end_date ";
		}
		$re_room = $this->_query($room_open);

		return $re_room;
	}

	function roomPlanned($uid, $zone, $at_date = false) {

		$room_open = "
		SELECT roomid, uid, zone, title, start_date, end_date, bookable, capacity
		FROM ".$this->_getRoomTable()."
		WHERE uid = '".$uid."'
			AND zone = '".$zone."'
			AND start_date > '".$at_date."'  ";
		$re_room = $this->_query($room_open);

		return $re_room;
	}
	

*/

	function getUrl($idConference,$room_type) {
		$lang =& DoceboLanguage::createInstance('conference', 'lms');
		
		$conf=new Conference_Manager();
		
		$conference = $conf->roomInfo($idConference);
		
		$room_open = "
		SELECT * FROM ".$this->_getRoomTable()."
		WHERE idConference = '".$idConference."'  ";
		$re_room=$this->_query($room_open);
		$teleskill_room=$this->nextRow($re_room);
		$room_id=$teleskill_room["roomid"];
		
		if (getLogUserId()==$conference["idSt"]) { 
			$role=2;
		} else {
			$role=1;
		}
		
		$login_info = $this->loginIntoRoom($room_id,
										$role,
										getLogUserId(),
										$GLOBALS['current_user']->getUserName() );
		
		if ($login_info['errorcode']) {
			$url=$login_info['errormessage'];
		} else {
			$url='<a href="'.$login_info['url'].'"
										onclick="window.open(\''.$login_info['url'].'\', \'TeleSkill\', \'location=0,status=1,menubar=0,toolbar=0,resizable=1,scrollbars=1,width=1000,height=700\'); return false;"
										onkeypress="window.open(\''.$login_info['url'].'\', \'TeleSkill\', \'location=0,status=1,menubar=0,toolbar=0,resizable=1,scrollbars=1,width=1000,height=700\'); return false;">'
									.$lang->def('_ACCESS_TO_CONFERENCE')
									.'</a>';
		};
		
		return $url;
	}

	

	
}

?>