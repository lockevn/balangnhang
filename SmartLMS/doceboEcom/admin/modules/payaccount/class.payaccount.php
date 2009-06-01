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

class PayAccount {
	
	function getPaySettingTable() {
		
		return $GLOBALS['prefix_ecom'].'_payaccount_setting';
	}
	
	function _query($query) {
		
		$re = mysql_query($query);
		if($GLOBALS['framework']['do_debug'] == 'on') {
			echo '<!-- debug :: '.__CLASS__.' query: "'.$query.'" '.( !$re ? '@with_error: '.mysql_error() : '' ).' -->';
		}
		return $re;
	}
	
	/**
	 * class constructor
	 */
	function PayAccount() {
	
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
		
		return '';
	}
	
	/**
	 * save the information edited
	 * @param array	$data_source the array with the info to save ( i.e. : $_POST )
	 *
	 * @return bool	true if the information was ssaved successfully, false otherwise
	 */
	function saveDetails($data_source) {
		
		return true;
	}
	
}

?>