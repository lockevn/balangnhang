<?php
/*************************************************************************/
/* DOCEBO CRM - Customer Relationship Management                         */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @version  $Id:  $
 */
// ----------------------------------------------------------------------------


Class ContactReasonManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $status_info=array();


	function ContactReasonManager($prefix="crm", $dbconn=NULL) {
		$this->prefix=$prefix;
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


	function _getMainTable() {
		return $this->prefix."_contact_reason";
	}


	function GetLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function moveItem($direction, $id_val) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getMainTable();

		utilMoveItem($direction, $table, "reason_id", $id_val, "ord");
	}


	function getContactReasonList($ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMainTable()." ";
		$qtxt.="ORDER BY ord ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {

				$id=$row["reason_id"];
				$data_info["data_arr"][$i]=$row;
				$this->status_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadContactReasonInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMainTable()." ";
		$qtxt.="WHERE reason_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getContactReasonInfo($id) {

		if (!isset($this->status_info[$id]))
			$this->status_info[$id]=$this->loadContactReasonInfo($id);

		return $this->status_info[$id];
	}



	function saveData($data) {

		$id=(int)$data["id"];
		$label=$data["label"];

		if ($id == 0) {
			$ord=$this->getLastOrd($this->_getMainTable())+1;

			if (empty($label)) {
				$lang=& DoceboLanguage::createInstance("contactreason", "crm");
				$label=$lang->def("_NO_LABEL");
			}

			$field_list="label, ord";
			$field_val="'".$label."', '".$ord."'";

			$qtxt="INSERT INTO ".$this->_getMainTable()." (".$field_list.") VALUES(".$field_val.")";
			$id=$this->_executeInsert($qtxt);
		}
		else if ($id > 0) {

			$qtxt="UPDATE ".$this->_getMainTable()." SET label='".$label."' WHERE reason_id='".$id."'";
			$q=$this->_executeQuery($qtxt);

		}

		return $id;
	}


	function deleteContactReason($id) {
		$qtxt="DELETE FROM ".$this->_getMainTable()." WHERE reason_id='".$id."' AND is_used='0' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}

}


?>