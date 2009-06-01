<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ----------------------------------------------------------------------------



Class TransactionManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $product_info=NULL;


	function TransactionManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_ecom"]);
		$this->dbconn=$dbconn;
	}


	function _query( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _insQuery( $query ) {
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


	function _getTransactionTable() {
		return $this->prefix."_transaction";
	}


	function _getTransactionProductTable() {
		return $this->prefix."_transaction_product";
	}



	function getTransactionList($company_id=FALSE, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info =array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTransactionTable()." ";
		$qtxt.="WHERE 1 ";


		if (($company_id !== FALSE) && ($company_id > 0)) {
			$qtxt.="AND company_id='".$company_id."' ";
		}

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY product_id ";
		$q =$this->_query($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_query($qtxt);
		}

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

				$id =$row["prd_id"];
				$data_info["data_arr"][$i]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	/**
	 * Get a list with information about the courses orders placed
	 * by a specified company
	 */
	 function getCompanyCourseTransactionList($company_id, $where=FALSE) {

		$data_info =array();

		$fields ="t1.company_id, t2.*, ";
		$fields.="SUBSTRING_INDEX(t2.id_prod, '_', -1) as item_id ";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTransactionTable()." as t1, ";
		$qtxt.=$this->_getTransactionProductTable()." as t2 ";
		$qtxt.="WHERE t1.id_trans=t2.id_trans AND t1.company_id='".(int)$company_id."' ";
		$qtxt.="AND t2.id_prod LIKE 'course_%' ";


		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY t2.name ";
		$q =$this->_query($qtxt);


		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {
				$id =$row["item_id"];
				$data_info[$id]=$row;
			}
		}

		return $data_info;
	}


	/**
	 * Get a list with information about the specified products
	 * from the transactions database.
	 */
	 function getTransactionPrdInfoFromArr($prd_arr, $where=FALSE) {

		$data_info =array();
		$prd_arr =addSurroundingQuotes($prd_arr);

		$fields ="t1.company_id, t2.*, ";
		$fields.="SUBSTRING_INDEX(t2.id_prod, '_', -1) as item_id ";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTransactionTable()." as t1, ";
		$qtxt.=$this->_getTransactionProductTable()." as t2 ";
		$qtxt.="WHERE t1.id_trans=t2.id_trans AND t2.id_prod IN (".implode(",", $prd_arr).") ";


		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY t2.name ";
		$q =$this->_query($qtxt); //echo $qtxt;


		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {
				$id =$row["item_id"];
				$data_info[$id]=$row;
			}
		}

		return $data_info;
	}


}


?>
