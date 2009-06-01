<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

if(!defined("IN_DOCEBO")) die("You can't access this file directly!");

require_once(dirname(__FILE__).'/resource.main.php');

define("USERVR_LATENCY", 			9);

define("USERVR_ID", 				0);
define("USERVR_USERID", 			1);
define("USERVR_FIRSTNAME",			2);
define("USERVR_LASTNAME", 			3);
define("USERVR_ROLE", 				4);
define("USERVR_AVATAR", 			5);
define("USERVR_WEBCAM", 			6);
define("USERVR_MIC", 				7);
define("USERVR_IN_ROOM", 			8);
define("USERVR_LOGIN_TIME",			9);
define("USERVR_LAST_REFRESH_TIME", 	10);
define("USERVR_BROADCAST",			11);
define("USERVR_LOGGED_OUT",			12);
define("USERVR_DISPLAYNAME", 		13);

define("SECOND_BEFORE_LOGOUT", 		30);

class User_VR extends VideoResource {
	
	var $_id_user;
	
	var $_logged_user_table;
	
	var $_lu_field;
	
	/**
	 * create the user form the id_user or the userid and initialize the class
	 */
	function User_VR($id_user, $userid = false) {
		
		// set standards params
		$this->_lu_field = array(
			USERVR_ID 					=> 'id_user', 
			USERVR_USERID 				=> 'userid', 
			USERVR_FIRSTNAME 			=> 'firstname', 
			USERVR_LASTNAME 			=> 'lastname', 
			USERVR_ROLE 				=> 'role', 
			USERVR_AVATAR 				=> 'avatar', 
			USERVR_WEBCAM				=> 'webcam',
			USERVR_MIC					=> 'mic',
			USERVR_IN_ROOM 				=> 'in_room', 
			USERVR_LOGIN_TIME 			=> 'login_time',
			USERVR_LAST_REFRESH_TIME 	=> 'last_refresh_time',
			USERVR_BROADCAST 			=> 'in_broadcast',
			USERVR_LOGGED_OUT 			=> 'logged_out'
		);
		ksort($this->_lu_field);
		reset($this->_lu_field);
		
		$this->_id_user 			= $id_user;
		$this->_logged_user_table 	= $GLOBALS['prefix_scs'].'_vc_user';
		
		// retrive user details
		$query_info = "SELECT ".implode(', ', $this->_lu_field)." "
			." FROM ".$this->_logged_user_table." ";
		if($userid !== false) {
			
			$query_info .= " WHERE ".$this->_lu_field[USERVR_USERID]." = '".$userid."'";
		} else {
			
			$query_info .= " WHERE ".$this->_lu_field[USERVR_ID]." = ".(int)$this->_id_user."";
		}
		$re_info = $this->_query($query_info);
		if($re_info) $this->_userinfo = $this->_db->fetch_row($re_info);
	}
		
	/**
	 * recognize the requested action and perform it
	 * @param string 	$action_idref 	the identifer of the action
	 * @param array		$data the 		data that the action require in order to be performed
	 **/
	function performAction($action_idref, &$data) {
		
		if(!isset($_SESSION['vc_user']['last_user_update'])) {
			$_SESSION['vc_user']['last_user_update'] = date("Y-m-d H:i:s");
		}
		
		$result = false;
		// dispath the requested action
		switch($action_idref) {
			
			// log the user into the chat =====================================
			case "login_user" : {
				$result = array();
				$result['user_self'] = array(
					'id' 			=> $_SESSION['id_user'], 
					'role' 			=> $_SESSION['role'], 
					'display_name' 	=> $_SESSION['display_name']
				);
				$_SESSION['vc_user']['login_date'] 			= date("Y-m-d H:i:s");
				$this->setLogin();
				
				// send system message
				$chat_man 	= new Chat_VR($_SESSION['id_room']);
				$q_data = array($this->_id_user, $_SESSION['display_name']);
				$chat_man->sendSytemMessage('login_user', $q_data);
			};break;
			case "set_media" : {
				$webcam = ( isset($data->webcam) ? $data->webcam : 0 );
				$mic = ( isset($data->mic) ? $data->mic : 0 );
				
				$this->setMedia($webcam, $mic);
				$this->updateUserRefreshTime();
				$result = $this->getCompleteUserList($_SESSION['id_room'], false);
			};break;
			
			// log the user into the chat =====================================
			case "logout_user" : {
				$result = array();
				$result['user_self'] = array('id' => $_SESSION['id_user'], 'display_name' => $_SESSION['display_name'] );
				$this->setLogout();
				
				// send system message
				$chat_man 	= new Chat_VR($_SESSION['id_room']);
				$q_data = array($this->_id_user, $_SESSION['display_name']);
				$result = $chat_man->sendSytemMessage('logout_user', $q_data);
				
				//$_SESSION = array();
				session_write_close();
			};break;
			
			// retrive the complete user list =================================
			case "get_user_list" : {
				$this->updateUserRefreshTime();
				$result = $this->getCompleteUserList($_SESSION['id_room'], false);
			};break;
			
			// retrive the user list change from last visti ===================
			case "get_userlist_change" : {
				$this->updateUserRefreshTime();
				$result = $this->getChangeInUserListFromDate($_SESSION['vc_user']['last_user_update'], $_SESSION['id_room']);
			};break;
			
			// user rise/lower an hand =========================================
			case "rise_hand" : {
				
				// send system message
				$chat_man 	= new Chat_VR($_SESSION['id_room']);
				$q_data[] = $data->rise_user;
				$result = $chat_man->sendSytemMessage('rise_hand', $q_data);
			};break;
			case "lower_hand" : {
				
				// send system message
				$chat_man 	= new Chat_VR($_SESSION['id_room']);
				$q_data[] = end(explode('_', $data->rise_user));
				$result = $chat_man->sendSytemMessage('lower_hand', $q_data);
			};break;
			
			// broadcast a webcam to all the room =============================
			case "broadcast_webcam" : {
				
				$q_data = array($data->to_broadcast);
				$this->_query("
				UPDATE ".$this->_logged_user_table."
				SET ".$this->_lu_field[USERVR_BROADCAST]." = 1
				WHERE ".$this->_lu_field[USERVR_ID]." = %i ", $q_data);
				
				// recover display name
				$query = " SELECT ".implode(', ', $this->_lu_field)." "
					." FROM ".$this->_logged_user_table." "
					." WHERE ".$this->_lu_field[USERVR_ID]." = %i ";
				$row = $this->_db->fetch_row($this->_query($query, $q_data));
		
				$display_name = ( trim($row[USERVR_LASTNAME].$row[USERVR_FIRSTNAME]) != ''
					? $row[USERVR_LASTNAME]." ".$row[USERVR_FIRSTNAME]
					: $row[USERVR_USERID] );
				
				// send system message
				$chat_man 	= new Chat_VR($_SESSION['id_room']);
				$q_data[] = $display_name;
				$result = $chat_man->sendSytemMessage('broadcast_webcam', $q_data);
			};break;
			case "unbroadcast_webcam" : {
				
				$q_data = array($data->to_broadcast);
				$this->_query("
				UPDATE ".$this->_logged_user_table."
				SET ".$this->_lu_field[USERVR_BROADCAST]." = 0
				WHERE ".$this->_lu_field[USERVR_ID]." = %i ", $q_data);
				
				// send system message
				$chat_man 	= new Chat_VR($_SESSION['id_room']);
				$result = $chat_man->sendSytemMessage('unbroadcast_webcam', $q_data);
			};break;
			
		}
		// update last visit ==================================================
		$_SESSION['vc_user']['last_user_update'] = date("Y-m-d H:i:s");
		return $result;
	}
	
	function getId() 					{ return $this->_id_user; }
	
	function getInfo() 					{ return $this->_userinfo; }
	
	/**
	 * return the role of the user into the room
	 */
	function getUserRole() 				{ return $this->_userinfo[USERVR_ROLE]; }
	
	/**
	 * return the room of the user
	 */
	function getUserRoom() 				{ return $this->_userinfo[USERVR_IN_ROOM]; }
	
	/**
	 * return when the user logged into the room
	 */
	function getLoginTime() 			{ return $this->_userinfo[USERVR_LOGIN_TIME]; }
	
	function changeUserRoom($new_room) 	{ $this->_userinfo[USERVR_IN_ROOM] = $new_room; }
	
	function setMedia($webcam, $mic) {
		
		$query = "UPDATE ".$this->_logged_user_table."
		SET ".$this->_lu_field[USERVR_WEBCAM]." = %i,
			".$this->_lu_field[USERVR_MIC]." = %i
		WHERE ".$this->_lu_field[USERVR_ID]." = %i ";
		return $this->_query($query, array(( $webcam ? 1 : 0 ), ( $mic ? 1 : 0 ), $this->_id_user));
	}
	
	function setLogin() {
		
		$query = "UPDATE ".$this->_logged_user_table."
		SET ".$this->_lu_field[USERVR_LOGIN_TIME]." = %date, 
			".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." = %date,
			".$this->_lu_field[USERVR_BROADCAST]." = %i,
			".$this->_lu_field[USERVR_LOGGED_OUT]." = %i
		WHERE ".$this->_lu_field[USERVR_ID]." = %i";
		return $this->_query($query, array(date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), 0, 0, $this->_id_user));
	}
	
	/**
	 * perform the user logout 
	 */
	function setLogout() {
		
		$query = " UPDATE ".$this->_logged_user_table." "
				." SET ".$this->_lu_field[USERVR_LOGGED_OUT]." = %i "
				." WHERE ".$this->_lu_field[USERVR_ID]." = %i ";
		$data = array(1, $this->_id_user);
		return $this->_query($query, $data);
	}
	
	/**
	 * create an entry needed for the user login
	 */
	function logUser($id_user, $userid, $lastname, $firstname, $id_room, $role) {
		
		$sel_data = array();
		$select = "SELECT ".$this->_lu_field[USERVR_ID]
			." FROM ".$this->_logged_user_table." ";
		if($id_user == false) {
			$select .= " WHERE ".$this->_lu_field[USERVR_USERID]." = %s ";
			$sel_data[] = $userid;
		} else {
			$select .= " WHERE ".$this->_lu_field[USERVR_ID]." = %i ";
			$sel_data[] = $id_user;
		}
		$re_sel = $this->_query($select, $sel_data);
		
		if($this->_db->num_rows($re_sel)) {
			
			// update the user info
			list($id_user) = $this->_db->fetch_row($re_sel);
			$query = " UPDATE ".$this->_logged_user_table." "
				." SET "
				." ".$this->_lu_field[USERVR_USERID]." = %s, "
				." ".$this->_lu_field[USERVR_FIRSTNAME]." = %s, "
				." ".$this->_lu_field[USERVR_LASTNAME]." = %s, "
				." ".$this->_lu_field[USERVR_IN_ROOM]." = %i,"
				." ".$this->_lu_field[USERVR_ROLE]." = %i, "
				." ".$this->_lu_field[USERVR_LOGIN_TIME]." = %date, "
				." ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." = %date, "
				." ".$this->_lu_field[USERVR_LOGGED_OUT]." = %i ";
			
			$data = array(	$userid, 
							$lastname, 
							$firstname, 
							$id_room, 
							$role, 
							date("Y-m-d H:i:s"), 
							date("Y-m-d H:i:s"), 
							0 );
			
			if($id_user !== false) {
				$query .= " WHERE ".$this->_lu_field[USERVR_ID]." = %i ";
				$data[] = $id_user;
			} else {
				$query .= " WHERE ".$this->_lu_field[USERVR_USERID]." = %s ";
				$data[] = $userid;
			}
			if(!$this->_query($query, $data)) return false;
			
		} else  {
			
			// insert a new user
			$query = "INSERT INTO ".$this->_logged_user_table." ( "
				." ".$this->_lu_field[USERVR_ID].", "
				." ".$this->_lu_field[USERVR_USERID].", "
				." ".$this->_lu_field[USERVR_FIRSTNAME].", "
				." ".$this->_lu_field[USERVR_LASTNAME].", "
				
				." ".$this->_lu_field[USERVR_ROLE].", "
				
				." ".$this->_lu_field[USERVR_AVATAR].", "
				." ".$this->_lu_field[USERVR_WEBCAM].", "
				." ".$this->_lu_field[USERVR_MIC].", "
				
				." ".$this->_lu_field[USERVR_IN_ROOM].", "
				
				." ".$this->_lu_field[USERVR_LOGIN_TIME].", "
				." ".$this->_lu_field[USERVR_LAST_REFRESH_TIME].", "
				
				." ".$this->_lu_field[USERVR_LOGGED_OUT]." "
				
				." ) VALUES ( %i, %s, %s, %s, %i, %s, %i, %i, %i, %date, %date, %i ) ";
			$data = array($id_user, $userid, $firstname, $lastname, $role, '', '', '', $id_room, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), 0);	
			
			if(!$this->_query($query, $data)) return false;
			$id_user = $this->_db->insert_id(); 
		}
		return array( 'id_user' => $id_user, 
			'display_name' => ( trim($lastname.' '.$firstname) != '' ? $lastname.' '.$firstname : $userid ), 
			'id_room' => $id_room, 
			'role' => $role,
			'query' => $query );
	}
	
	/**
	 * set the last time the user was seen online, is used for logout
	 */
	function updateUserRefreshTime() {
		
		$query = " UPDATE ".$this->_logged_user_table." "
				." SET ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." = %date "
				." WHERE ".$this->_lu_field[USERVR_ID]." = %i ";
		return $this->_query($query, array(date("Y-m-d H:i:s"), $this->_id_user));
	}
	
	/**
	 * return the complete user list in a room if specified
	 * @param int $room_id the room_identifier
	 * 
	 * @return array the user list 
	 */
	function getCompleteUserList($room_id = false, $not_me = true) {
		
		$users = array();
		$query = " SELECT ".implode(', ', $this->_lu_field)." "
			." FROM ".$this->_logged_user_table." "
			." WHERE ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." > %date "
			." 		AND ".$this->_lu_field[USERVR_LOGGED_OUT]." <> %i ";
		$data = array( date("Y-m-d H:i:s", time() - SECOND_BEFORE_LOGOUT), 1 );
		
		if($room_id !== false) {
			$query .= " AND ".$this->_lu_field[USERVR_IN_ROOM]." = %i ";
			$data[] = $room_id;
		}
		if($not_me !== false) {
			$query .= " AND ".$this->_lu_field[USERVR_ID]." <> %i ";
			$data[] = $this->_id_user;
		}
		$query .= " ORDER BY ".$this->_lu_field[USERVR_LASTNAME].", ".$this->_lu_field[USERVR_FIRSTNAME].", ".$this->_lu_field[USERVR_USERID]." ";
		$re = $this->_query($query, $data);
		
		if(!$this->_db->num_rows($re)) return $users;
		while($row = $this->_db->fetch_row($re)) {
			
			$row[USERVR_DISPLAYNAME] = ( trim($row[USERVR_LASTNAME].$row[USERVR_FIRSTNAME]) != ''
				? $row[USERVR_LASTNAME]." ".$row[USERVR_FIRSTNAME]
				: $row[USERVR_USERID]
			);
			$users[] = $row;
		}
		return $users;
	}
	
	/**
	 * return information about the users login and logout from the $refer_date to now
	 * @param date 	$refer_date 	the function calculate the change form this date
	 * @param int 	$room_id 		the room id
	 * @param bool 	$not_me 		if true the current user is not inserted into the user list
	 * 
	 * @return array an array with 3 key, 'logged_in' that contains the info about the user logged in between $refer_date and now 
	 * 					'logged_out' the user logged out between $refer_date and now 
	 * 					'now_online' the number of the user now online
	 */
	function getChangeInUserListFromDate($refer_date, $room_id = false, $not_me = true) {
		
		$users = array('logged_in' => array(), 'logged_out' => array(), 'now_online' => 1);
		
		$remote_date = date("Y-m-d H:i:s", time() - SECOND_BEFORE_LOGOUT);
		
		$data = array();
		$query = " SELECT ".implode(', ', $this->_lu_field)." "
			." FROM ".$this->_logged_user_table." "
			." WHERE 1 ";
		if($room_id !== false) {
			$query .= " AND ".$this->_lu_field[USERVR_IN_ROOM]." = %i ";
			$data[] = $room_id;
		}
		if($not_me !== false) {
			$query .= " AND ".$this->_lu_field[USERVR_ID]." <> %i ";
			$data[] = $this->_id_user;
		}
		
		$query .= " AND ( ";
		// user logged since the last refresh =============================================
		
		$query .= " ( ".$this->_lu_field[USERVR_LOGIN_TIME]." >= %date " 
				." 		AND ".$this->_lu_field[USERVR_LOGIN_TIME]." < %date ) ";
		$data[] = $refer_date;
		$data[] = date("Y-m-d H:i:s");
		
		// user logged out remot ==========================================================
			
		if(strcmp($_SESSION['vc_user']['login_date'], $remote_date) < 0) {
			
			$query .= " OR ( ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." >= %date " 
					." 		AND ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." < %date )";
			$data[] = date("Y-m-d H:i:s", time() - SECOND_BEFORE_LOGOUT - USERVR_LATENCY);
			$data[] = $remote_date;
		}
		
		$query .= "  OR ( ".$this->_lu_field[USERVR_LOGGED_OUT]." = %i "
				."		AND ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." >= %date ) "
				." )";
		$data[] = 1;
		$data[] = $remote_date;
		
		// order the user by lastname, firstname, userid ==================================
		$query .= " ORDER BY ".$this->_lu_field[USERVR_LASTNAME].", ".$this->_lu_field[USERVR_FIRSTNAME].", ".$this->_lu_field[USERVR_USERID]." ";
		
		$re = $this->_query($query, $data);
		while($row = $this->_db->fetch_row($re)) {
			
			$row[USERVR_DISPLAYNAME] = ( trim($row[USERVR_LASTNAME].$row[USERVR_FIRSTNAME]) != ''
				? $row[USERVR_LASTNAME]." ".$row[USERVR_FIRSTNAME]
				: $row[USERVR_USERID]
			);
			if(strcmp($row[USERVR_LOGIN_TIME], $refer_date) >= 0) $users['logged_in'][] = $row;
			elseif($row[USERVR_LOGGED_OUT] == '1' && strcmp($row[USERVR_LAST_REFRESH_TIME], $remote_date) >= 0) $users['logged_out'][] = $row[USERVR_ID];
			elseif($row[USERVR_LOGGED_OUT] == '0' && strcmp($row[USERVR_LAST_REFRESH_TIME], $remote_date) < 0) $users['logged_out'][] = $row[USERVR_ID];
		}
		
		// number of user online =============================================================
		
		$count_data = array($refer_date);
		$query = " SELECT COUNT(*) "
			." FROM ".$this->_logged_user_table." "
			." WHERE ".$this->_lu_field[USERVR_LAST_REFRESH_TIME]." >= %data ";
		if($room_id !== false) {
			$query .= " AND ".$this->_lu_field[USERVR_IN_ROOM]." = %i ";
			$count_data[] = $room_id;
		}
		list($num_online) = $this->_db->fetch_row($this->_db->query($query, $count_data));
		$users['now_online'] = $num_online;
		
		return $users;
	}
	
}

?>