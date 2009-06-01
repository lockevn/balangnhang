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

require_once(dirname(__FILE__).'/class.payaccount.php');

class PayAccount_MoneyOrder extends PayAccount {
	
	/**
	 * class constructor
	 */
	function PayAccount_MoneyOrder() {
		
	}
	
	/**
	 * return a short summary about the account
	 *
	 * @return string	some info to display
	 */
	function getSummary() {
	
	}
	
	/**
	 * return a form for account details editing
	 *
	 * @return string	the html code of the form
	 */
	function getFormDetails() {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		$lang =& DoceboLanguage::createInstance('payaccount_money_order', 'ecom');
		
		$query_account = "
		SELECT param_name , param_value
		FROM ".$this->getPaySettingTable()."
		WHERE account_name = 'money_order'";
		$re_account = $this->_query($query_account);
		$html='';
		while (list($param_name,$param_value) = mysql_fetch_row($re_account)){
			$html.=Form::getTextfield($lang->def($param_name), 
									$param_name, 
									$param_name,
									255 ,
									$param_value );
		}
						
		return $html;
	}
	
	/**
	 * save the information edited
	 * @param array	$data_source the array with the info to save ( i.e. : $_POST )
	 *
	 * @return bool	true if the information was ssaved successfully, false otherwise
	 */
	function saveDetails($data_source) {

		foreach ($data_source as $key => $value) {
			if($key!='save' && $key!='account_name'){
				$query = "UPDATE ".$this->getPaySettingTable()."
				SET param_value = '".$value."'
				WHERE account_name='money_order' and  param_name ='".$key."'";
				if(!mysql_query($query))
				return false;
			}
		}
		return true;
	}

}

?>