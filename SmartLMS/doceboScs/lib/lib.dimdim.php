<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.5.0
 * 
 * ( editor = Eclipse 3.2.0 [phpeclipse,subclipse,WTP], tabwidth = 4 ) 
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define("_DIMDIM_STREAM_TIMEOUT", 30);
require_once($GLOBALS['where_scs'].'/setting.php');

class DimDim_Manager {
	function DimDim_Manager() {
		$this->server = $GLOBALS['scs']['dimdim_server'];
		$this->port = $GLOBALS['scs']['dimdim_port'];
	}
	
	function _getRoomTable() {
		
		return $GLOBALS['prefix_scs'].'_dimdim';
	}
	
	function _query($query) {

		$re = mysql_query($query);
		return $re;
	}
	
	function generateConfKey() {
		$conf_key = '';
		for($i = 0; $i <8;$i++) {
			switch(mt_rand(0, 2)) {
				case "0" : $conf_key .= chr(mt_rand(65, 90));
				case "1" : $conf_key .= chr(mt_rand(97, 122));
				case "2" : $conf_key .= mt_rand(0, 9);
			}
		}
		return $conf_key;
	}
	
	
	function canOpenRoom($start_time) {
		return true;
	}
	
	
	function insert_room($idConference,$user_email,$display_name,$confkey,$audiovideosettings,$maxmikes) {
		
		//save in database the roomid for user login
		$insert_room = "
		INSERT INTO ".$this->_getRoomTable()."
		( idConference,confkey,emailuser,displayname,audiovideosettings,maxmikes ) VALUES (
			'".$idConference."',
			'".$confkey."',
			'".$user_email."',
			'".$display_name."',
			'".$audiovideosettings."',
			'".$maxmikes."'
		)";

		if(!mysql_query($insert_room)) return false;
		return mysql_insert_id();
	}
	
	function roomInfo($room_id) {

		$room_open = "
		SELECT  idCourse,idSt,name, starttime, confkey, emailuser, displayname, meetinghours,maxparticipants,audiovideosettings,maxmikes
		FROM ".$this->_getRoomTable()."
		WHERE id = '".$room_id."'";
		$re_room = $this->_query($room_open);

		return $this->nextRow($re_room);
	}
	
	function roomActive($idCourse, $at_date = false) {

		$room_open = "
		SELECT id,idCourse,idSt,name, starttime,endtime, confkey, emailuser, displayname, meetinghours,maxparticipants,audiovideosettings,maxmikes
		FROM ".$this->_getRoomTable()."
		WHERE idCourse = '".$idCourse."'";
		
		if ($at_date !== false) {
			$room_open .= " AND endtime >= '".$at_date."'";
		}
		
		$room_open .= " ORDER BY starttime";
		
		$re_room = $this->_query($room_open);
			
		return $re_room;
	}
	


	function nextRow($re_room) {

		return mysql_fetch_array($re_room);
	}
	
	function deleteRoom($room_id) {

		$room_del = "
		DELETE FROM ".$this->_getRoomTable()."
		WHERE idConference = '".$room_id."'";
		$re_room = $this->_query($room_del);

		return $re_room;
	}
	
	function getUrl($idConference,$room_type) {
		$lang =& DoceboLanguage::createInstance('conference', 'lms');
		
		$conf=new Conference_Manager();
		
		$conference = $conf->roomInfo($idConference);
		
		$acl_manager =& $GLOBALS['current_user']->getAclManager();
		$display_name = $GLOBALS['current_user']->getUserName();
		$u_info = $acl_manager->getUser(getLogUserId(), false);
		$user_email=$u_info[ACL_INFO_EMAIL];
		
		
		$query2="SELECT * FROM ".$this->_getRoomTable()." WHERE idConference = '".$idConference."'";
		$re_room = $this->_query($query2);
		$room=$this->nextRow($re_room);
				
		
				
		if ($room["audiovideosettings"]==0) {
			$av="audio";
		} else {
			$av="av";
		}
		$returnurl="http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["PHP_SELF"]."?modname=conference&op=list";
		
		$error = false;
		if (getLogUserId()==$conference["idSt"]) {
			
			$url='<a onclick="window.open(this.href, \'\', \'\');return false;" href="http://'.$this->server.'/dimdim/html/envcheck/connect.action'
								.'?action=host'
								.'&email='.urlencode($GLOBALS['scs']['dimdim_user'])

								.'&confKey='.$room["confkey"]
								.'&confName='.urlencode($conference["name"])

								.'&lobby=false'
								.'&networkProfile=2'
								.'&meetingHours='.$conference["meetinghours"]
								.'&meetingMinutes=0'
								.'&presenterAV=av'
								.'&maxAttendeeMikes='.$room["maxmikes"]

								.'&displayName='.urlencode($acl_manager->getConvertedUserName($u_info))
								.'&attendees='.$user_email

								.'&maxParticipants='.$conference["maxparticipants"]

								.'&submitFormOnLoad=true'
								."&returnUrl=".urlencode($returnurl)."\">".$lang->def('_START_CONFERENCE')."</a>";
			/*
			$url='<a onclick="window.open(this.href, \'\', \'\');return false;" href="http://'.$this->server.'/dimdim/html/signin/signin.action'
								.'?action=host'
								.'&email='.urlencode($GLOBALS['scs']['dimdim_user'])

								.'&confKey='.$room["confkey"]
								.'&confName='.urlencode($conference["name"])

								.'&lobby=false'
								.'&networkProfile=2'
								.'&meetingHours='.$conference["meetinghours"]
								.'&meetingMinutes=0'
								.'&presenterAV=av'
								.'&maxAttendeeMikes='.$room["maxmikes"]

								.'&displayName='.urlencode($acl_manager->getConvertedUserName($u_info))
								.'&attendees='.$user_email

								.'&maxParticipants='.$conference["maxparticipants"]

								.'&submitFormOnLoad=true'
								."&returnUrl=".urlencode($returnurl)."\">".$lang->def('_START_CONFERENCE')."</a>";*/
		} else {
		
			$url='<a onclick="window.open(this.href, \'\', \'\');return false;" href="http://'.$this->server.'/dimdim/html/envcheck/connect.action'
					.'?action=join'

					.'&email='.$user_email

					.'&displayName='.urlencode($acl_manager->getConvertedUserName($u_info))

					.'&confKey='.$room["confkey"]

					."&returnUrl=".urlencode($returnurl)."\">".$lang->def('_JOIN_CONFERENCE')."</a>";
			/*$url='<a onclick="window.open(this.href, \'\', \'\');return false;" href="http://'.$this->server.'/dimdim/html/signin/signin.action'
					.'?action=join'
					.'&displayName='.urlencode($acl_manager->getConvertedUserName($u_info))
					.'&email='.$user_email

					.'&confKey='.$room["confkey"]

					.'&submitFormOnLoad=true'

					.'&response=json'

					."&returnUrl=".urlencode($returnurl)."\">".$lang->def('_JOIN_CONFERENCE')."</a>";*/

		}
		
		return $url;
	}
	
	/**
	 * Thanks to : jbr at ya-right dot com
	 * http://it2.php.net/manual/it/function.fsockopen.php
	 * for the HTTP 1.1 implementation
	 */
	function _decode_header ( $str ) {
	    
	    $out = array ();
	    $part = preg_split ( "/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY );
		for( $h = 0; $h < sizeof ( $part ); $h++ ) {
			
			if ( $h != 0 ) {
	        
				$pos = strpos ( $part[$h], ':' );
				$k = strtolower ( str_replace ( ' ', '', substr ( $part[$h], 0, $pos ) ) );
				$v = trim ( substr ( $part[$h], ( $pos + 1 ) ) );
			} else {
				
				$k = 'status';
	            $v = explode ( ' ', $part[$h] );
	            $v = $v[1];
	        }
	        if ( $k == 'set-cookie' ) {
				$out['cookies'][] = $v;
	        } else if ( $k == 'content-type' ) {
	            
	            if(($cs = strpos ($v, ';')) !== false ) { $out[$k] = substr ( $v, 0, $cs ); }
	            else { $out[$k] = $v; }
			} else {
				$out[$k] = $v;
			}
	    }
	    return $out;
	}
	
	function _decode_body( $info, $str, $eol = "\r\n" ) {
	   
	    $tmp = $str;
	    $add = strlen ( $eol );
	    if ( isset ( $info['transfer-encoding'] ) && $info['transfer-encoding'] == 'chunked' ) {
	        
	        do {
	            $tmp = ltrim ( $tmp );
	            $pos = strpos ( $tmp, $eol );
	            $len = hexdec ( substr ( $tmp, 0, $pos ) );
	            if ( isset ( $info['content-encoding'] ) )  {
	                $str .= gzinflate ( substr ( $tmp, ( $pos + $add + 10 ), $len ) );
	            } else {
	                $str .= substr ( $tmp, ( $pos + $add ), $len );
	            }
	            $tmp = substr ( $tmp, ( $len + $pos + $add ) );
	            $check = trim ( $tmp );
	        } while ( ! empty ( $check ) );
	    }
	    else if ( isset ( $info['content-encoding'] ) ) {
	        $str = gzinflate ( substr ( $tmp, 10 ) );
	    }else {
	    	$str = $tmp;
	    }
	    return $str;
	}
		
	/**
	 * The only purpose of this function is to send the message to the server, read the server answer,
	 * discard the header and return the other content
	 *
	 * @param 	string	$url 		the server url
	 * @param 	string	$port 		the server port
	 * @param	string 	$get_params	the get_params
	 *
	 * @return 	json 	
	 */
	function _sendRequest($url, $port, $get_params) {

		$json_response = '';
		$tmp_url = parse_url($url);
				
		if(( $io = fsockopen($tmp_url['host'], $port, $errno, $errstr, _DIMDIM_STREAM_TIMEOUT)) !== false) {
			
			socket_set_timeout($io, _DIMDIM_STREAM_TIMEOUT);
			
		    $send  = "GET /".$get_params." HTTP/1.1\r\n";
		    $send .= "Host: ".$tmp_url['host']."\r\n";
		    $send .= "User-Agent: PHP Script\r\n";
		    $send .= "Accept: text/xml,application/xml,application/xhtml+xml,";
		    $send .= "text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,";
		    $send .= "image/jpeg,image/gif;q=0.2,text/css,*/*;q=0.1\r\n";
		    $send .= "Accept-Language: en-us, en;q=0.50\r\n";
		    $send .= "Accept-Encoding: gzip, deflate, compress;q=0.9\r\n";
		    $send .= "Connection: Close\r\n\r\n";
		
		    fputs ( $io, $send );
			$header = '';
			do {
				$header .= fgets ( $io, 4096 );
			} while( strpos ( $header, "\r\n\r\n" ) === false );
			$info = $this->_decode_header ( $header );
			$body = '';
			while(!feof($io)) {
				$body .= fread ( $io, 8192 );
			}
			fclose ( $io );

			$json_response = $this->_decode_body ( $info, $body );

			echo $json_response;
		}
		return $json_response;
	}
	
}

?>