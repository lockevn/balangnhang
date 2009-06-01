<?php

/************************************************************************/
/* DOCEBO Ecommerce - E-commerce system									*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package  DoceboEcom
 * @version  $Id$
 * @author	 Claudio Demarinis
 */


class Payment {


	function getPayAccountTable() {

		return $GLOBALS['prefix_ecom'].'_payaccount';
	}

	function _query($query) {

		$re = mysql_query($query);
		if($GLOBALS['framework']['do_debug'] == 'on') {
			echo '<!-- debug :: '.__CLASS__.' query: "'.$query.'" '.( !$re ? '@with_error: '.mysql_error() : '' ).' -->';
		}
		return $re;
	}

	function _executeQuery($query) {
		return $this->_query($query);
	}


	function _getTransactionTable() {
		return $GLOBALS['prefix_ecom']."_transaction";
	}


	function _getTransactionProductsTable() {
		return $GLOBALS['prefix_ecom']."_transaction_product";
	}


	/**
	 * class constructor
	 */
	function Payment() {

	}


	/**
	 * return an array with active payaccounts
	 *
	 * @return array active payaccounts
	 */


	function getActivePayment() {
		$query_payaccount = "
		SELECT account_name
		FROM ".$this->getPayAccountTable()."
		WHERE active = 'true'";
		$re_payaccount = $this->_query($query_payaccount);
		$payaccount=array();
		while (list($account_name) = mysql_fetch_row($re_payaccount)){
			$payaccount[]=$account_name;
		}
		return $payaccount;
	}


	/**
	 * return a form for payaccount selection
	 *
	 * @return string	the html code of the form
	 */
	function getFormSelection($sel=FALSE) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		$lang =& DoceboLanguage::createInstance('admin_payaccount', 'ecom');
		//$tb	= new TypeOne('', $lang->def('_PAYACCOUNT_CAPTION'), $lang->def('_PAYACCOUNT_SUMMARY'));
		//$tb->setTableStyle('payaccount_table');
		//$type_h = array('image', 'news_short_td');
		//$cont_h	= array('','');
		//$tb->setColsStyle($type_h);
		//$tb->addHead($cont_h);
		$active_payment= $this->getActivePayment();
		if ($sel === FALSE) {
			$sel =current($active_payment);
		}
		$out='';
		if (!empty($active_payment)){
			foreach ($active_payment as  $value) {
				$checked =($value == $sel ? TRUE : FALSE);
				$out.=Form::getRadio($lang->def('_ADMIN_PAYACCOUNT_'.$value,'admin_payaccount','ecom'),'paymod_'.$value,'paymod',$value, $checked);

			//	$tb->addBody($cont);

			}
		}

		return $out;
	}

	/**
	 * save the information edited
	 * @param array	$data_source the array with the info to save ( i.e. : $_POST )
	 *
	 * @return bool	true if the information was ssaved successfully, false otherwise
	 */
	function saveTransaction($company_id, $total_amount,$order_status,$payment_status,$payment_type,$array_item) {

		$re = true;
		$query_insert_transaction=
		"INSERT INTO ".$GLOBALS['prefix_ecom']."_transaction
		SET id_user = '".getLogUserId()."',
		company_id='".(int)$company_id."',
		total_amount = '".$total_amount."',
		transaction_date = NOW(),
		order_status = '".$order_status."',
		payment_status = '".$payment_status."',
		payment_type =  '".$payment_type."'
		";
		$this->_query($query_insert_transaction);
		$id_transaction=mysql_insert_id();
		foreach ( $array_item as $id_prod => $product_detail) {

			$query_insert_product=
			"INSERT INTO ".$GLOBALS['prefix_ecom']."_transaction_product
			SET id_trans = '".$id_transaction."',
			id_prod = '".$id_prod."',
			id_user = '".(isset($product_detail["user"]) ? $product_detail["user"] : getLogUserId())."',
			name = '".$product_detail['descriptor']."',
			type = '".$product_detail['type']."',
			price = '".$product_detail['price']."',
			quantity = '".$product_detail['quantity']."'";
			$re &= $this->_query($query_insert_product);
		}

		return (int)$id_transaction;
	}


	function updateTransaction($data) {

		$id_trans=$data["id_trans"];

		if ($id_trans < 1)
			return FALSE;

		$order_status=$data["order_status"];
		$payment_status=$data["payment_status"];
		$order_notes=$data["order_notes"];
		$payment_notes=$data["payment_notes"];

		$sel_product=(isset($data["sel_product"]) ? $data["sel_product"] : array());
		$to_deactivate=(isset($data["to_deactivate"]) ? $data["to_deactivate"] : array());

		$qtxt ="UPDATE ".$GLOBALS['prefix_ecom']."_transaction SET ";
		$qtxt.="order_status='".$order_status."', payment_status='".$payment_status."', ";
		$qtxt.="order_notes='".$order_notes."', payment_notes='".$payment_notes."' ";
		$qtxt.="WHERE id_trans='".$id_trans."' LIMIT 1";
		$q=$this->_query($qtxt);

		if (($q) && (count($sel_product) > 0)) {
			$this->activateProducts($id_trans, array_keys($sel_product));
		}

		if (($q) && (count($to_deactivate) > 0)) {
			$this->deactivateProducts($id_trans, array_keys($to_deactivate));
		}
	}


	function activateProducts($id_trans, $product_list) {
		$table=$this->_getTransactionProductsTable();

		$where ="id_trans='".(int)$id_trans."' AND ";
		$where.="product_id IN (".implode(",", $product_list).")";

		$qtxt="UPDATE ".$table." SET active='1' WHERE ".$where;
		$q=$this->_executeQuery($qtxt);

		$fields="product_id, id_trans, id_prod, type, id_user";
		$qtxt="SELECT ".$fields." FROM ".$table." WHERE ".$where." AND type != 'other'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {
				$this->doActivateProduct($row);
			}
		}

		$this->updateActiveStatus($id_trans);
	}


	function doActivateProduct($data) {
		$type=$data["type"];

		$eproduct =& $this->getEProductObject($type, $data);

		$eproduct->doActivate();
	}


	function doDeactivateProduct($data) {
		$type=$data["type"];

		$eproduct =& $this->getEProductObject($type, $data);

		$eproduct->doDeactivate();
	}


	function &getEProductObject($type, $data) {

		switch ($type) {

			case "course": {
				require_once($GLOBALS["where_lms"]."/lib/lib.ecom_product.php");
				$eproduct=new EcomProductCourse();
				$eproduct->setProductInfo($data);
			} break;

			case "course_edition": {
				require_once($GLOBALS["where_lms"]."/lib/lib.ecom_product.php");
				$eproduct=new EcomProductCourseEdition();
				$eproduct->setProductInfo($data);
			} break;

		}

		return $eproduct;
	}


	function deactivateProducts($id_trans, $product_list) {
		$table=$this->_getTransactionProductsTable();

		$where ="id_trans='".(int)$id_trans."' AND ";
		$where.="product_id IN (".implode(",", $product_list).")";

		$qtxt="UPDATE ".$table." SET active='-1' WHERE ".$where;
		$q=$this->_executeQuery($qtxt);

		$fields="product_id, id_trans, id_prod, type, id_user";
		$qtxt="SELECT ".$fields." FROM ".$table." WHERE ".$where." AND type != 'other'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {
				$this->doDeactivateProduct($row);
			}
		}

		$this->updateActiveStatus($id_trans);
	}


	function updateActiveStatus($id_trans) {
		$table=$this->_getTransactionProductsTable();

		$qtxt ="SELECT sum(active = '0') AS not_active, sum(active = '1') AS active ";
		$qtxt.="FROM ".$table." WHERE id_trans='".(int)$id_trans."'";
		$q=$this->_executeQuery($qtxt);

		$active_status=FALSE;
		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_assoc($q);

			$active=$row["active"];
			$not_active=$row["not_active"];

			if (($not_active == 0) && ($active > 0)) {
				$active_status="all";
			}
			else if (($not_active > 0) && ($active > 0)) {
				$active_status="partial";
			}
		}

		if ($active_status !== FALSE) {
			$qtxt ="UPDATE ".$this->_getTransactionTable()." SET ";
			$qtxt.="active_status='".$active_status."' WHERE id_trans='".(int)$id_trans."'";
			$q=$this->_executeQuery($qtxt);
		}
	}


	function getOrderStatusList() {
		$lang=& DoceboLanguage::createInstance('admin_transaction', 'ecom');

		$res=array();
		$res["NOTPROC"]=$lang->def("_NOT_PROCESSED");
		$res["PROC"]=$lang->def("_PROCESSED");
		$res["PARTPROC"]=$lang->def("_PARTIALLY_PROCESSED");
		$res["CANC"]=$lang->def("_CANCELLED");

		/*
- da evadere
- evaso
- parzialmente evaso
- annullato
- stornato parzialmente <- come si traduce stornato?!?
- stornato totalmente   <- come si traduce stornato?!?
*/
		return $res;
	}


	function getPaymentStatusList() {
		$lang=& DoceboLanguage::createInstance('admin_transaction', 'ecom');

		$res=array();
		$res["NOTPAY"]=$lang->def("_NOT_PAYED");
		$res["PAYED"]=$lang->def("_PAYED");
		$res["PARTPAY"]=$lang->def("_PARTIALLY_PAYED");
		$res["CANC"]=$lang->def("_CANCELLED");

		return $res;
	}


	function getDefaultStatus($type) {
		$res="";

		switch($type) {
			case "order": {
				$status_arr=$this->getOrderStatusList();
			} break;
			case "payment": {
				$status_arr=$this->getPaymentStatusList();
			} break;
		}

		$status_code_arr=array_keys($status_arr);
		$res=$status_code_arr[0];

		return $res;
	}


	function getProductsList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$idst_arr=array();
		$data_info=array();
		$data_info["data_arr"]=array();
		$data_info["has_courses"]=FALSE;

		$fields="*";
		$table=$this->_getTransactionProductsTable();
		$qtxt ="SELECT ".$fields." FROM ".$table." ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="WHERE ".$where." ";
		}

		$qtxt.="ORDER BY name";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.=" LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}


		$users_arr=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

				//$version=$row["version"];
				$data_info["data_arr"][$i]=$row;
				//$this->revision_info[$version]=$row;

				$type =$row["type"];
				if (($type == "course") || ($type == "course_edition")) {
					$data_info["has_courses"] =TRUE;
				}

				if (!in_array($row["id_user"], $users_arr))
					$users_arr[]=$row["id_user"];

				$i++;
			}
		}


		if (count($users_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($users_arr);
			foreach ($users_arr as $idst) {
				if ((!empty($user_info[$idst][ACL_INFO_FIRSTNAME])) ||
				    (!empty($user_info[$idst][ACL_INFO_LASTNAME]))) {
					$username =$user_info[$idst][ACL_INFO_LASTNAME]." ";
					$username.=$user_info[$idst][ACL_INFO_FIRSTNAME];
					$data_info["user"][$idst]=$username;
				}
				else {
					$data_info["user"][$idst]=$acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
				}
			}
		}
		else {
			$data_info["user"]=array();
		}

		return $data_info;
	}


}

?>
