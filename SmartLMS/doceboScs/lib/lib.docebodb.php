<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2007													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

class DbConn {
	
	var $html_filter = false;
	
	function DbConn($host, $user, $pwd, $dbname = false) {
		
		$this->html_filter 	=& DbPurifier::getInstance();
	}
	
	function &getInstance() {
		
		if(!isset($GLOBALS['db_conn'])) {
			$GLOBALS['db_type'] = 'mysql';
			$var = false;
			switch($GLOBALS['db_type']) {
				case "mysql" : { Mysql_DbConn::getInstance(); };break;										
			}
		}
		return $GLOBALS['db_conn'];
	}
	
}

class Mysql_DbConn extends DbConn {
	
	var $conn;
	
	function Mysql_DbConn($host, $user, $pwd, $dbname = false) {
		
		parent::DbConn($host, $user, $pwd, $dbname);
		
		$this->connect($host, $user, $pwd);
		if($dbname !== false) $this->select_db($dbname);
		if($this->html_filter === false) $this->html_filter =& DbPurifier::getInstance();
	}
	
	function &getInstance() {
		
		if(!isset($GLOBALS['db_conn'])) {
			
			$GLOBALS['db_conn'] = new Mysql_DbConn( $GLOBALS['dbhost'], 
													$GLOBALS['dbuname'], 
													$GLOBALS['dbpass'], 
													$GLOBALS['dbname'] );
		}
		return $GLOBALS['db_conn'];
	}
	
	function connect($host, $user, $pwd) {
		
		if(!$this->conn = @mysql_connect($host, $user, $pwd)) return false;
		return true;
	}
	
	function select_db($dbname) {
		
		if(!@mysql_select_db($dbname)) return false;
		
		// change charset for utf8 connection with the server
		$this->query("SET NAMES '".$GLOBALS['db_conn_names']."'", $this->conn);
		$this->query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $this->conn);
		return true;
	}
	
	function escape($data) {
		
		return mysql_real_escape_string($data, $this->conn);
	}
	
	function get_parsed_query($query, $data) {
		
		if($data === false) return $query; 
		
		if($keys = preg_split ( "/%[a-zA-Z]+/", $query, '-1', PREG_SPLIT_OFFSET_CAPTURE)) {
			
			$current = 0;
			$parsed_query = '';
			while(list($ind, $match) = each($keys)) {
				
				$parsed_query .= $match[0];
				
				$str_start = $match[1] + strlen($match[0]);
				if(isset($keys[$ind+1])) $type = substr($query, $str_start, $keys[$ind+1][1] - $str_start);
				else $type = '%last';
				
				
				//if(isset($data[$current]) || $data[$current] == NULL)
				switch($type) {
					case "%last" : {
						$parsed_query .= "";
					};break;
					// select by type ====================================
					case "%autoinc" : {
						$parsed_query .= 'NULL';
					};break;
					case "%i" : {
						$parsed_query .= (int)$data[$current];
					};break;
					case "%f" : {
						$parsed_query .= (float)$data[$current];
					};break;
					case "%d" : {
						$parsed_query .= (double)$data[$current];
					};break;
					case "%date" : {
						$parsed_query .= "'".$this->escape($this->html_filter->text($data[$current]))."'";
					};break;
					case "%text" : 
					case "%s" : {
						$parsed_query .= "'".$this->escape($this->html_filter->text($data[$current]))."'";
					};break;
					case "%html" : {
						$parsed_query .= "'".$this->escape($this->html_filter->purify($data[$current]))."'";
					};break;
					default: {
						if(trim($type) != '') $parsed_query .= "'".$this->escape($this->html_filter->text($data[$current]))."'";
					}
				}
				++$current;
			}
		} else {
			return $query;
		}
		return $parsed_query;
	}
	
	function query($query, $data = false) {
		
		$parsed_query = $this->get_parsed_query($query, $data);
		
		$re = mysql_query($parsed_query, $this->conn);
		//if($GLOBALS['do_debug']) $GLOBALS['debug_list'][] = $parsed_query.( !$re ? ' :: '.mysql_error() : '' ); 
		return $re;
	}
	
	function insert_id() {
		
		return mysql_insert_id($this->conn);
	}
	
	function fetch_row($resource) {
		
		if(!$resource) return false;
		return mysql_fetch_row($resource);
	}
		
	function fetch_assoc($resource) {
	
		if(!$resource) return false;
		return mysql_fetch_assoc($resource);
	}
	
	function num_rows($resource) {
		
		if(!$resource) return false;
		return mysql_num_rows($resource);
	}
	
	function close() {
		
		if($this->conn) @mysql_close($this->conn);
	}
	
}

?>