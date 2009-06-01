<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/



// Tree permissions for administrators (admin area)

class CmsTreePermissions {

	var $type =""; // page, news, document, media, ..
	var $tree_id ="";
	var $prefix=NULL;
	var $dbconn=NULL;

	var $all_node_perm=FALSE;


	function CmsTreePermissions($type, $prefix=FALSE, $dbconn=NULL) {
		$this->type =$type;
		$this->tree_id =$this->setTreeIdFromType();
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_cms"]);
		$this->dbconn=$dbconn;
	}


	function _executeQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	function getTreeType() {
		return $this->type;
	}


	function getTreeId() {
		return $this->tree_id;
	}


	function _getTable() {
		return $this->prefix."_tree_perm";
	}


	function _getTreeTable() {
		switch ($this->getTreeType()) {
			case "page": {
				return $this->prefix."_area";
			} break;
			case "news": {
				return $this->prefix."_news_dir";
			} break;
			case "document": {
				return $this->prefix."_docs_dir";
			} break;
			case "media": {
				return $this->prefix."_media_dir";
			} break;
			case "content": {
				return $this->prefix."_content_dir";
			} break;
		}
	}


	function setTreeIdFromType($type=FALSE) {
		switch (($type !== FALSE ? $type : $this->getTreeType())) {
			case "page": {
				return "idArea";
			} break;
			case "news": {
				return "id";
			} break;
			case "document": {
				return "id";
			} break;
			case "media": {
				return "id";
			} break;
			case "content": {
				return "id";
			} break;
		}
	}


	function saveNodePerm($user_id, $data) {
		$res=FALSE;

		$table=$this->_getTable();
		$type =$this->getTreeType();

		$qtxt ="DELETE FROM ".$table." WHERE type='".$type."' ";
		$qtxt.="AND user_id='".(int)$user_id."'";
		$q=$this->_executeQuery($qtxt);


		$values_arr=array();
		foreach($data as $node_id=>$value) {
			$do_insert=FALSE;

			if ($value == "yes") {
				$recursive="0";
				$do_insert=TRUE;
			}


			if ($value == "inherit") {
				$recursive="1";
				$do_insert=TRUE;
			}

			if ($do_insert) {
				$values_arr[]="('".$type."', '".(int)$user_id."', '".$node_id."', '".$recursive."')";
			}

		}

		if (count($values_arr) > 0) {
				$qtxt ="INSERT INTO ".$table." (type, user_id, node_id, recursive) VALUES ";
				$qtxt.=implode(",\n", $values_arr);
				$res=$this->_executeQuery($qtxt);
		}

		return $res;
	}


	function appendNewNodePerm($user_id, $node_id, $recursive=FALSE) {
		$table=$this->_getTable();
		$type =$this->getTreeType();
		$recoursive =($recursive !== FALSE ? 1 : 0);

		$qtxt ="INSERT INTO ".$table." (type, user_id, node_id, recursive) VALUES ";
		$qtxt.="('".$type."', '".(int)$user_id."', '".(int)$node_id."', '".$recursive."')";
		$res=$this->_executeQuery($qtxt);

		return $res;
	}


	function loadAllNodePerm($user_id, $load_all=FALSE) {
		$res=array("normal"=>array(), "recursive"=>array(), "all"=>array());

		$type =$this->getTreeType();
		$qtxt ="SELECT * FROM ".$this->_getTable()." WHERE type='".$type."' AND user_id='".(int)$user_id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {

				$node_id =$row["node_id"];

				if ($row["recursive"] == 1)
					$res["recursive"][$node_id]=$node_id;
				else
					$res["normal"][$node_id]=$node_id;

			}
		}


		if ($load_all) {

			$descendants_arr=array();

			if (count($res["recursive"]) > 0) {

				$like_arr =array();
				// If root is selected..
				if (in_array(0, $res["recursive"])) {
					$like_arr[]="path LIKE '/root/%'";;
					unset($res["recoursive"][0]);
				}

				$qtxt ="SELECT path FROM ".$this->_getTreeTable()." WHERE ";
				$qtxt.=$this->getTreeId()." IN (".implode(",", $res["recursive"]).")";

				$q=$this->_executeQuery($qtxt);

				if (($q) && (mysql_num_rows($q) > 0)) {
					while($row=mysql_fetch_assoc($q)) {

						$like_arr[]="path LIKE '".$row["path"]."/%'";

					}
				}

				if (count($like_arr) > 0) {

					$qtxt ="SELECT ".$this->getTreeId()." FROM ".$this->_getTreeTable()." WHERE ";
					$qtxt.=implode(" OR ", $like_arr);

					$q=$this->_executeQuery($qtxt);

					if (($q) && (mysql_num_rows($q) > 0)) {
						while($row=mysql_fetch_assoc($q)) {
							$descendants_arr[]=$row[$this->getTreeId()];
						}
					}
				}
			}

			$res["all"]=array_unique(array_merge($res["normal"], $res["recursive"], $descendants_arr));
		}

		return $res;
	}


	function getAllNodePerm($user_id, $load_all=FALSE, $reload=FALSE) {

		$type =$this->getTreeType();
		if (($reload) || (!isset($this->all_node_perm[$type][$user_id]))) {
			$this->all_node_perm[$type][$user_id]=$this->loadAllNodePerm($user_id, $load_all);
		}

		return $this->all_node_perm[$type][$user_id];
	}


	function checkNodePerm($user_id, $node_id, $return_value=FALSE, $ignore_godadmin=TRUE) {

		$user_level=$GLOBALS["current_user"]->getUserLevelId();
		if (($ignore_godadmin) && ($user_level == ADMIN_GROUP_GODADMIN)) {
			return TRUE;
		}

		$node_perm=$this->getAllNodePerm($user_id, TRUE);

		$res=(in_array($node_id, $node_perm["all"]) ? TRUE : FALSE);

		if ($return_value)
			return $res;
		else if (!$res)
			die("You can't access!");
	}


}


?>