<?php

/************************************************************************/
/* DOCEBO SCS - Synchronous Collaboration System						*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2006                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package doceboScs
 * @category management library
 * @version $id$
 */

/* videoconference user index =============================== */

define("INTE_USER_ID", 			0);
define("INTE_USER_USERID", 		1);
define("INTE_USER_FIRSTNAME",		2);
define("INTE_USER_LASTNAME", 		3);
define("INTE_USER_ROLE", 			4);
define("INTE_USER_IN_ROOM", 		5);

/* videoconference user index =============================== */

define("INTE_ROOM_ID", 			0);
define("INTE_ROOM_EXT_KEY", 		1); 
define("INTE_ROOM_NAME", 			2);
define("INTE_ROOM_DESCRIPTION", 	3);
define("INTE_ROOM_ROOM_PARENT", 	4);
define("INTE_ROOM_ROOM_PATH", 	5);
define("INTE_ROOM_TYPE", 			6);
define("INTE_ROOM_MAX_USER", 		7);
define("INTE_ROOM_OWNER", 		8);
define("INTE_ROOM_BOOKABLE", 		9);
define("INTE_ROOM_PERM", 			10);
define("INTE_ROOM_ZONE", 			11);
define("INTE_ROOM_START_DATE", 	12);
define("INTE_ROOM_END_DATE", 		13);
define("INTE_ROOM_LOGO",			14);

/* require the main setting of the scs ===================== */

require_once($GLOBALS['where_scs'].'/setting.php');

/**
 * This class will be used to abstarct the real implementation of the user and room to the function that need 
 * to communicate with the conference system.
 * When a entity need to know something about the room or the user must perfomr the actions trough this class and not
 * directly on the database.
 * 
 * @author Fabio Pirovano
 */
class IntelligereManager {
	
	var $db_conn;
	
	var $prefix;
	
	var $user_field = array(
		INTE_USER_ID 				=> 'id_user',
		INTE_USER_USERID 			=> 'userid',
		INTE_USER_FIRSTNAME 		=> 'firstname',
		INTE_USER_LASTNAME 			=> 'lastname',
		INTE_USER_ROLE 				=> 'role',
		INTE_USER_IN_ROOM 			=> 'in_room'
	);
	
	var $room_field = array(
		INTE_ROOM_ID			=> 'id_room',
		INTE_ROOM_EXT_KEY 		=> 'ext_key',
		INTE_ROOM_NAME			=> 'name',
		INTE_ROOM_DESCRIPTION	=> 'description',
		INTE_ROOM_ROOM_PARENT	=> 'room_parent',
		INTE_ROOM_ROOM_PATH		=> 'room_path',
		INTE_ROOM_TYPE			=> 'type',
		INTE_ROOM_MAX_USER		=> 'max_user',
		INTE_ROOM_OWNER			=> 'owner',
		INTE_ROOM_BOOKABLE		=> 'bookable',
		INTE_ROOM_PERM			=> 'room_perm',
		INTE_ROOM_ZONE			=> 'zone',
		INTE_ROOM_START_DATE	=> 'start_date',
		INTE_ROOM_END_DATE		=> 'end_date',
		INTE_ROOM_LOGO			=> 'logo'
	);
	
	function _getUserTable() { return 'conference_inte_user'; }
	function _getRoomTable() { return 'conference_inte_room'; }
	function _getTokenTable() { return 'conference_inte_token'; }
	
	function _query($query) {

		$re = mysql_query($query);
		if(!$re) echo '<!-- Openconference_Management - query : '.$query.' error : '.mysql_error().' -->';
		else echo '<!-- Openconference_Management - query : '.$query.' -->';
		return $re;
	}
	
	function IntelligereManager($prefix = false, $db_conn = false) {

		ksort($this->user_field);
		reset($this->user_field);
		
		ksort($this->room_field);
		reset($this->room_field);
		
		$this->prefix 	= ( $this->prefix ? $GLOBALS['prefix_scs'] : $prefix );
		$this->db_conn 	= $db_conn;
	}

	/**
	 * send a message type 5 to the server and read the answer
	 * @param 	datetime 	$start_time 	room start time
	 * @param 	datetime 	$end_time 		room end time
	 *
	 * @return mixed 	true if the user can open the room, else return an array with
	 *					array( errorcode => 1, errormessage => string )
	 */
	function canOpenRoom($start_time, $end_time) {

		return array('errorcode' => 0, 'errormessage' => '');
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
	 * @param 	int				$bookable		if set to 1 the room can be booked; just a flag for module frontend
	 * @param int				$capacity		highest number of users in chat if bookable.
	 *
	 * @return array	return an array
	 *					array( errorcode => int, errormessage => string, roomid => int )
	 */
	function openRoom($uid, $zone, $title, $start_date, $end_date, $descr = false, $logo = false, $bookable=0, $capacity='', $owner) {

		$query = "INSERT INTO ".$this->_getRoomTable()."" .
				" (ext_key, zone, name, start_date, end_date, description, logo, bookable, max_user, owner)" .
				" VALUES ('".$uid."', '".$zone."', '".$title."', '".$start_date."', '".$end_date."', '".$descr."', '".$logo."', '".$bookable."', '".$capacity."', '".$owner."')";
		
		$result = $this->_query($query);
		
		if ($result) {
			
			$e_code = 1;
			$e_msg = 'Ok ';
			$roomid = 0;
		} else {
			
			$e_code = 0;
			$e_msg = 'Error';
			$roomid = 0;
		}
						
		return array(	'errorcode' 	=> $e_code,
						'errormessage' 	=> $e_msg,
						'roomid' 		=> $roomid );
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
	 * @param 	int				$bookable		if set to 1 the room can be booked; just a flag for module frontend
	 * @param int				$capacity		highest number of users in chat if bookable.
	 *
	 * @return array	return an array
	 *					array( errorcode => int, errormessage => string, roomid => int )
	 */
	function updateRoom($roomid, $uid, $zone, $title, $start_date, $end_date, $descr = false, $logo = false, $bookable=0, $capacity='') {
		
		$query = "UPDATE ".$this->_getRoomTable()."" .
				" SET ext_key = '".$uid."'," .
				" zone = '".$zone."'," .
				" title = '".$title."'," .
				" start_date = '".$start_date."'," .
				" end_date = '".$end_date."'," .
				" bookable = '".$bookable."'," .
				" max_user = '".$capacity."'";
		
		if ($descr)
			$query .= "description = '".$descr."'";
		if($logo)
			$que8ry .= "logo = '".$logo."'";
		
		$query .= " WHERE id_room = '".$roomid."'";
		
		$result = mysql_query($query);
		
		$e_code = 0;
		$e_msg = '';
		$roomid = 0;
						
		return array(	'errorcode' 	=> $e_code,
						'errormessage' 	=> $e_msg,
						'roomid' 		=> $roomid );
	}
	
	/**
	 * send a message type 1 to the server and read the answer
	 * @param 	int 	$roomid 	the room identifier
	 * @param 	int 	$role 		the role in the room 1 = normal, 2 = tutor
	 * @param 	string 	$userid 	the userid
	 * @param 	string 	$user_name 	the user real name
	 * @param 	string 	$email 		the user email
	 *
	 * @return array 	return an array
	 *					array( errorcode => int, errormessage => string, url => string, fullroom => int )
	 *					if fullroom == 1 the room is full
	 */
	function loginIntoRoom($roomid, $role, $userid, $user_name, $email = false) {

		$e_code = 0;
		$e_msg = '';
		$log_url = 0;
		$fullroom = 0;
		
		return array('errorcode' => $e_code, 'errormessage' => $e_msg, 'url' => $log_url, 'fullroom' => $fullroom);
	}
	
	/**
	 * send a message type 2 to the server and read the answer
	 * @param 	int 	$roomid 	the room identifier
	 *
	 * @return array 	return an array
	 *					array( errorcode => int, errormessage => string )
	 */
	function deleteRemoteRoom($uid, $zone, $roomid) {
		
		$e_code 	= 0;
		$e_msg 		= '';
		
		require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");

		$cb = new ChatBooking($zone);
		$cb->deleteByRoom(array($roomid));
		
		$room_open = "
		DELETE FROM ".$this->_getRoomTable()."
		WHERE uid = '".$uid."'
			AND zone = '".$zone."'
			AND id_room = '".$roomid."'";
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
	
	function roomInfo($zone, $room_id) {

		$room_open = "
		SELECT ".implode(",", $this->room_field)."
		FROM ".$this->_getRoomTable()."
		WHERE zone = '".$zone."' AND id_room = '".$room_id."'";
		$re_room = $this->_query($room_open);

		return $this->nextRow($re_room);
	}

	function roomActive($uid, $zone, $at_date = false) {

		$room_open = "
		SELECT  ".implode(', ', $this->room_field)." 
		FROM ".$this->_getRoomTable()."
		WHERE ext_key = '".$uid."' AND zone = '".$zone."'";
		if($at_date !== false) {

			$room_open .= " AND '".$at_date."' <= end_date ";
		}
		
		$room_open .= " ORDER BY starttime";
		
		$re_room = $this->_query($room_open);

		return $re_room;
	}

	function roomPlanned($uid, $zone, $at_date = false) {

		$room_open = "
		SELECT ".implode(', ', $this->room_field)."
		FROM ".$this->_getRoomTable()."
		WHERE ext_key = '".$uid."'
			AND zone = '".$zone."'
			AND start_date > '".$at_date."'  ";
		$re_room = $this->_query($room_open);

		return $re_room;
	}

	function totalRoom($re_room) {
		
		return mysql_num_rows($re_room);
	}

	function nextRow($re_room) {

		return mysql_fetch_row($re_room);
	}

	function deleteOldRoom($uid, $zone, $at_date = false) {

		$qtxt ="SELECT roomid FROM ".$this->_getRoomTable()." WHERE uid='".$uid."' ";
		$qtxt.="AND zone = '".$zone."' AND end_date < '".$at_date."'";
		$q=$this->_query($qtxt);

		$room_to_del=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$room_id=$row["roomid"];
				$room_to_del[$room_id]=$room_id;
			}
		}

		if (count($room_to_del) > 0) {
			require_once($GLOBALS["where_scs"]."/lib/lib.booking.php");

			$cb = new ChatBooking("teleskill");
			$cb->deleteByRoom($room_to_del);
		}

		$room_open = "
		DELETE FROM ".$this->_getRoomTable()."
		WHERE uid = '".$uid."'
			AND zone = '".$zone."'
			AND end_date < '".$at_date."'  ";
		$re_room = $this->_query($room_open);
		
		if ($re_room && count($room_to_del) > 0)
			foreach ($room_to_del as $romm_id_del)
				$re_room = $this->deleteConferenceUserForRoom($romm_id_del);

		return $re_room;
	}
	
	function deleteConferenceUserForRoom($room_id)
	{
		$query = "
		DELETE FROM ".$this->_getUserTable()."
		WHERE ".$this->room_field[INTE_ROOM_ID]." = '".$room_id."' ";
		
		if(!$this->_query($query))
			return false;
		else
			return true;
	}
	
	function getIfRoomIsFull($id_room)
	{
		$query = "SELECT ".$this->room_field[INTE_ROOM_MAX_USER]."" .
				" FROM ".$this->_getRoomTable()."" .
				" WHERE ".$this->room_field[INTE_ROOM_ID]." = '".$id_room."'";
		
		$result = mysql_query($query);
		
		list($max_user) = mysql_fetch_row($result);
		
		$control_query = "SELECT COUNT(*)" .
						" FROM ".$this->_getUserTable()."" .
						" WHERE ".$this->user_field[INTE_USER_IN_ROOM]." = '".$id_room."'";
		
		$control_result = mysql_query($control_query);
		
		list($user_in_rom) = mysql_fetch_row($control_result);
		
		if ($max_user > $user_in_rom)
			return false;
		return true;
	}
	
	function getToken()
	{
		$str = '';
		for($i = 0; $i < 64; $i++)
		{
			$seed = mt_rand(0, 10);
			if($seed > 6)
				$str .= mt_rand(0, 9);
			elseif($seed > 3)
				$str .= chr(mt_rand(65, 90));
			else
				$str .= chr(mt_rand(97, 122));
	
		}
		
		return $str;
	}
	
	function saveToken($id_user, $token, $role)
	{
		$query = "INSERT INTO ".$this->_getTokenTable()."" .
				" (id_user, token, role)" .
				"VALUES ('".$id_user."', '".$token."', '".$role."')";
		
		$result = mysql_query($query);
		
		return $result;
	}
	
	function controllToken($id_user, $token)
	{
		$query = "SELECT COUNT(*)" .
				" FROM ".$this->_getTokenTable()."" .
				" WHERE token = '".$token."'" .
				" AND id_user = '".$id_user."'";
		
		$result = mysql_query($query);
		
		list ($control) = mysql_fetch_row($result);
		
		if ($control == 1)
			return true;
		return false;
	}
	
	function readRole($id_user, $token)
	{
		$query = "SELECT role" .
				" FROM ".$this->_getTokenTable()."" .
				" WHERE token = '".$token."'" .
				" AND id_user = '".$id_user."'";
		
		$result = mysql_query($query);
		
		list ($role) = mysql_fetch_row($result);
		
		return $role;
	}
}


class IntelligereConferenceManager {
	
	var $db_conn;
	
	var $prefix;
	
	var $user_field = array(
		INTE_USER_ID 				=> 'id_user',
		INTE_USER_USERID 			=> 'userid',
		INTE_USER_FIRSTNAME 		=> 'firstname',
		INTE_USER_LASTNAME 			=> 'lastname',
		INTE_USER_ROLE 				=> 'role',
		INTE_USER_IN_ROOM 			=> 'in_room'
	);
	
	var $room_field = array(
		INTE_ROOM_ID			=> 'id_room',
		INTE_ROOM_EXT_KEY 		=> 'ext_key',
		INTE_ROOM_NAME			=> 'name',
		INTE_ROOM_DESCRIPTION	=> 'description',
		INTE_ROOM_ROOM_PARENT	=> 'room_parent',
		INTE_ROOM_ROOM_PATH		=> 'room_path',
		INTE_ROOM_TYPE			=> 'type',
		INTE_ROOM_MAX_USER		=> 'max_user',
		INTE_ROOM_OWNER			=> 'owner',
		INTE_ROOM_BOOKABLE		=> 'bookable',
		INTE_ROOM_PERM			=> 'room_perm',
		INTE_ROOM_ZONE			=> 'zone',
		INTE_ROOM_START_DATE	=> 'start_date',
		INTE_ROOM_END_DATE		=> 'end_date',
		INTE_ROOM_LOGO			=> 'logo'
	);
	
	function _getUserTable() { return 'conference_inte_user'; }
	
	function _getRoomTable() { return 'conference_inte_room'; }
	
	/**
	 * class constructor
	 * @param string the prefix of the videoconf table
	 * @param resource_id $db_conn the identifier of the database connection
	 */
	function IntelligereConferenceManager($prefix = false, $db_conn = false) {
		
		ksort($this->user_field);
		reset($this->user_field);
		
		ksort($this->room_field);
		reset($this->room_field);
		
		$this->prefix 	= ( $this->prefix ? $GLOBALS['prefix_scs'] : $prefix );
		$this->db_conn 	= $db_conn;
	}
	
	function _query($query) {
		
		if($this->db_conn == false) $re_query = mysql_query($query);
		else $re_query = mysql_query($query, $this->db_conn);
		
		if($GLOBALS['framework']['do_debug'] == 'on') 
			echo '<!-- class '.__CLASS__.' query : '.$query.' '.( !$re_query ? ' with error : '.mysql_error() : '' ).'-->';
		return $re_query;
	}
	
	function _last_id() {
		
		if(!$re = $this->_query("SELECT LAST_INSERT_ID()")) return false;
		
		list($id) = mysql_fetch_row($re);
		return $id;
	}
	
	function num_rows($resource) {
		
		return mysql_num_rows($resource);
	}
	
	function fetch_row($resource) {
		
		return mysql_fetch_row($resource);
	}
	
	function fetch_array($resource) {
		
		return mysql_fetch_array($resource);
	}
	
	/**
	 * Create a user in the videoconference
	 * @param int 		$id_user 	an external unique identifier for the user
	 * @param string 	$userid 	the userid
	 * @param string 	$firstname 	the firstname of the user
	 * @param string 	$lastname 	the lastname of the user
	 * @param int 		$role 		the role of the user in the videoconf, admin=admin, guest=user
	 * @param array 	$room_id	the id of the room where the user enter
	 * 
	 * @return int return the id of the user created or false if the creation fail
	 */
	function createConferenceUser($id_user, $userid, $firstname, $lastname, $role, $room_id) {
		
		$query = "
		INSERT INTO ".$this->_getUserTable()."
		( id_user, userid, firstname, lastname, role, in_room )" .
		" VALUES (
			'".$id_user."',
			'".$userid."', 
			'".$firstname."', 
			'".$lastname."', 
			'".$role."',
			'".$room_id."'
		) ";
		if(!$this->_query($query)) return false;
		else return $id_user;
	}
	
	/**
	 * Delete a user in the videoconference
	 * @param int $id an external unique identifier for the user
	 * 
	 * @return bool return true if the operation succeeding  or false otherwise
	 */
	function deleteConferenceUser($id_user) {
		
		$query = "
		DELETE FROM ".$this->_getUserTable()."
		WHERE ".$this->user_field[INTE_USER_ID]." = '".$id_user."' ";
		if(!$this->_query($query)) return false;
		else return true;
	}
	
	function deleteConferenceUserForRoom($room_id)
	{
		$query = "
		DELETE FROM ".$this->_getUserTable()."
		WHERE in_room = '".$room_id."' ";
		
		if($this->_query($query))
			return true;
		else
			return false;
	}
	
	/**
	 * Return the room info
	 * @param int 	$id_room 	the id of the room  
	 * 
	 * @return array return the information on the room selected or false if the room does not exixts
	 */
	function getRoomInfo($id_room) {
		
		$query = "
		SELECT ".implode(', ', $this->room_field)." 
		FROM ".$this->_getRoomTable()."
		WHERE ".$this->room_field[INTE_ROOM_ID_ROOM]." = '".$id_room."'";
		
		if(!$re_room = $this->_query($query)) return false;
		if($this->num_rows($re_room) === 0) return false;
		return $this->fetch_row($re_room);
	}
	
	/**
	 * Return the room info searched with the external id
	 * @param int 	$ext_key 	the external id of the room  
	 * 
	 * @return array return the information on the room selected or false if the room does not exixts
	 */
	function getRoomInfoFromExt($ext_key) {
		
		$query = "
		SELECT ".implode(', ', $this->room_field)." 
		FROM ".$this->_getRoomTable()."
		WHERE ".$this->room_field[INTE_ROOM_EXT_KEY]." = '".$ext_key."'";
		
		if(!$re_room = $this->_query($query)) return false;
		if($this->num_rows($re_room) === 0) return false;
		return $this->fetch_row($re_room);
	}
	
	/**
	 * Return the list of all the room or only the subroom of a given one
	 * @param int 	$id_room 	the id of the room, if passed only the subroom of this one will be returned 
	 * 
	 * @return resource_id return the info of the rooms
	 */
	function getRoomList($id_room = false) {
		
		$query = "
		SELECT ".implode(', ', $this->room_field)." 
		FROM ".$this->_getRoomTable()."
		WHERE 1";
		if($id_room !== false) $query .= " AND ".$this->room_field[INTE_ROOM_ROOM_PARENT]." = '%/".$id_room."%' ";
		$query .= " ORDER BY ".$this->room_field[INTE_ROOM_ROOM_PATH].", ".$this->room_field[INTE_ROOM_NAME]." ";
		
		if(!$re_room = $this->_query($query)) return false;
		return $re_room;
	}
	
	/**
	 * Create a new room in the videoconference
	 * @param int 		$ext_key 		the external key of the room
	 * @param string 	$name 			the name of the room
	 * @param string 	$room_parent 	the id of the room parent of this one
	 * @param int 		$max_user 		the maximum number of user allowed in the room
	 * @param string 	$type 			type of the room public or rivate
	 * @param int 		$owner 			the id of the owner of the room (can be 0 if the room is created automaticaly)
	 * @param int 		$blocked 		0=normal room, 1=blocked room
	 * @param array 	$room_opt		an array with the room init option 
	 * 
	 * @return int return the id of the room created or false n case of failure
	 */
	function createRoom($name, $ext_key, $room_parent, $max_user, $type, $owner, $blocked, $room_opt) {
	
		$query = "
		INSERT INTO ".$this->_getRoomTable()." 
		( ext_key, name, room_parent, type, max_user, owner, blocked, room_opt ) VALUES (
			'".$ext_key."', 
			'".$name."', 
			'".$room_parent."', 
			'".$type."', 
			'".$max_user."', 
			'".$owner."', 
			'".$blocked."', 
			'".$room_opt."'
		) ";
		
		if(!$this->_query($query)) return false;
		$id_room = $this->_last_id();
		return $id_room;
	}
	
	/**
	 * Update the info of a room, if id_room is 0 or false the function use ext_id for find the record to update
	 * @param int 		$id_room 		the id of the room
	 * @param int 		$ext_key 		the external key of the room
	 * @param string 	$name 			the name of the room
	 * @param string 	$room_parent 	the id of the room parent of this one
	 * @param string 	$type 			type of the room public or rivate
	 * @param int 		$max_user 		the maximum number of user allowed in the room
	 * @param int 		$owner 			the id of the owner of the room (can be 0 if the room is created automaticaly)
	 * @param int 		$blocked 		0=normal room, 1=blocked room
	 * @param array 	$room_opt		an array with the room init option 
	 * 
	 * @return bool return true if the room was successfully modificated or false otherwise
	 */
	function updateRoom($id_room, $ext_key, $name, $room_parent, $type, $max_user, $owner, $blocked, $room_opt) {
		
		$query = "
		UPDATE ".$this->_getRoomTable()." 
		SET ext_key = '".$ext_key."', 
			name = '".$name."', 
			room_parent = '".$room_parent."', 
			type = '".$type."', 
			max_user = '".$max_user."', 
			owner = '".$owner."', 
			blocked = '".$blocked."', 
			room_opt = '".$room_opt."'";
		if($id_room == false) {
			
			$query .= " WHERE  ".$this->room_field[INTE_ROOM_EXT_KEY]." = '".$ext_key."'";
		} else {

			$query .= " WHERE  ".$this->room_field[INTE_ROOM_ID_ROOM]." = '".$id_room."'";
		}
		
		if(!$re_room = $this->_query($query)) return false;
		return $re_room;
	}
	
	/**
	 * Delete a room 
	 * @param int 		$id_room 		the id of the room
	 * 
	 * @return bool return true if the room was successfully deleted or false otherwise
	 */
	function deleteRoom($id_room) {
		
		$result = $this->deleteConferenceUserForRoom($id_room);
		if ($result)
		{
		$query = "
		DELETE FROM ".$this->_getRoomTable()."
		WHERE ".$this->room_field[INTE_ROOM_ID]." = '".$id_room."' ";
		if(!$this->_query($query)) return false;
		else return true;
		}
		else
			return false;
	}
	
	/**
	 * Delete a room from ext_key
	 * @param int 		$ext_key 		the external key of the room
	 * 
	 * @return bool return true if the room was successfully deleted or false otherwise
	 */
	function deleteRoomFromExt($ext_key) {
	
		$query = "
		DELETE FROM ".$this->_getRoomTable()."
		WHERE ".$this->room_field[INTE_ROOM_EXT_KEY]." = '".$ext_key."' ";
		if(!$this->_query($query)) return false;
		else return true;
	}
	
	/**
	 * Return the list of the user in a room
	 * @param int 	$id_room 	the id of the room
	 * 
	 * @return bool return true if the room was successfully deleted or false otherwise
	 */
	function getUsersInRoom($id_room) {
		
		$query = "
		SELECT ".implode(', ', $this->user_field)." 
		FROM ".$this->_getUserTable()."
		WHERE ".$this->user_field[INTE_USER_IN_ROOM]." = '".$id_room."'
		ORDER BY ".$this->user_field[INTE_USER_LASTNAME].", 
				".$this->user_field[INTE_USER_FIRSTNAME].", 
				".$this->user_field[INTE_USER_USERID]."";
		
		if(!$re_room = $this->_query($query)) return false;
		return $re_room;
	}
	
	/**
	 * Change the room in which the user is with the one passed (if exists)
	 * @param int 	$id_user 	
	 * @param int 	$id_room 	the id of the room
	 * 
	 * @return bool return true if the room was successfully deleted or false otherwise
	 */
	function logUserInRoom($id_user, $id_room) {
		
		$query = "
		UPDATE ".$this->_getUserTable()."
		SET ".$this->user_field[INTE_USER_IN_ROOM]." = '".$id_room."'
		WHERE ".$this->user_field[INTE_USER_ID]." = '".$id_user."'";
		
		if(!$re_room = $this->_query($query)) return false;
		return true;
	}
	
}

?>