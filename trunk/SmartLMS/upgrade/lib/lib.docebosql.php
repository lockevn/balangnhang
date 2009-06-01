<?php

class DoceboSql {

	var $conn;
	
	var $is_db_selected = false;
	
	var $last_query = '';
	
	function DoceboSql($address, $user, $pwd, $db_name = false, $die = true) {
		
		$this->conn = @mysql_connect($address, $user, $pwd, true);
		if(!$this->conn) {
			if($die) die( "Can't connect to db. Check configurations" );
			else $this->conn = false;
		}
		if($db_name !== false ) $this->selDatabase($db_name, $die);
	}
	
	function isConnected() {
		
		return $this->conn;
	}
	
	function isDbSelected() {
		
		return $this->is_db_selected;
	}
	
	function selDatabase($db_name, $die = true) {
		
		if(!@mysql_select_db($db_name, $this->conn)) {
			
			if($die) die( "Database not found. Check configurations" );
			return $this->is_db_selected = false;
		}
		@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $this->conn);
		@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $this->conn);
			
		return $this->is_db_selected = true;
	}
	
	function error($with_query = true) {
		
		return mysql_error().( $with_query ? "\n".' <br />[ '.$this->last_query.' ] ' : '' );
	}
	
	function query($query) {
		
		$this->last_query = $query;
		
		$query = preg_replace("/--(.*)[^\$]/", "", $query);
		/*
		if(ereg('\;', $query)) {
			*/
			$arr_query = preg_split("/;([\s]*)[\n\r]/", $query); 
			foreach($arr_query  as $q) {
				if(trim($q) != '') {
					
					$resource = mysql_query($q, $this->conn);
					if(!$resource) {
						echo '<!-- query_exp_sliced :: '.$q.' '
							.( $resource === false ? "\n".' - error :: ('.mysql_errno().')'.mysql_error() : '' ).'-->'."\n";
					}
				}
			}
			/*
		} else {
		
			$resource = mysql_query($query, $this->conn);
			echo '<!-- query :: '.$query.' '
				.( $resource === false ? "\n".' - error :: ('.mysql_errno().')'.mysql_error() : '' ).'-->'."\n";
		}*/
		return $resource;
	}
	
	function querySingle($query) {
		
		$this->last_query = $query;
		
		$resource = mysql_query($query, $this->conn);
		if(!$resource) {
			echo '<!-- query :: '.$query.' '
				.( $resource === false ? "\n".' - error :: ('.mysql_errno().')'.mysql_error() : '' ).'-->'."\n";
		}
		return $resource;
	}
	
	function lastInsertId() {
		
		list($id) = $this->fetchRow($this->query("SELECT LAST_INSERT_ID()"));
		return $id;
		//return mysql_insert_id($this->conn);
	}
	
	function numRows($resource) {
		
		return mysql_num_rows($resource);
	}
	
	function fetchRow($resource) {
		
		return mysql_fetch_row($resource);
	}
	
	function fetchAssoc($resource) {
		
		return mysql_fetch_assoc($resource);
	}
	
	function fetchArray($resource) {
		
		return mysql_fetch_array($resource);
	}
	
	function closeConn() {
		
		if($this->conn) return true;
		return @mysql_close($this->conn);
	}
}


?>