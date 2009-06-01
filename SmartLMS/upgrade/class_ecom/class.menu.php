<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

class Upgrade_EcomMenu extends Upgrade {
	
	var $platfom = 'ecom';
	
	var $mname = 'menu';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.0.6" : {
				$i = 0;
				
				$content = "
				CREATE TABLE `ecom_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `ecom_menu` VALUES (1, '_ECOMMERCE_MANAGMENT', '', 1, 'false');
				INSERT INTO `ecom_menu` VALUES (2, '_ECOMMERCE_SELLING', '', 2, 'false');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_menu_under` (
				  `idUnder` int(11) NOT NULL auto_increment,
				  `idMenu` int(11) NOT NULL default '0',
				  `module_name` varchar(255) NOT NULL default '',
				  `default_name` varchar(255) NOT NULL default '',
				  `default_op` varchar(255) NOT NULL default '',
				  `associated_token` varchar(255) NOT NULL default '',
				  `of_platform` varchar(255) default NULL,
				  `sequence` int(3) NOT NULL default '0',
				  `class_file` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idUnder`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `ecom_menu_under` VALUES (1, 1, 'payaccount', '_PAYACCOUNT', 'payaccount', 'view', NULL, 1, 'class.payaccount.php', 'EcomAdmin_PayAccount');
				INSERT INTO `ecom_menu_under` VALUES (2, 1, 'taxzone', '_TAXZONE', 'taxzone', 'view', NULL, 2, 'class.taxzone.php', 'EcomAdmin_TaxZone');
				INSERT INTO `ecom_menu_under` VALUES (3, 1, 'taxcountry', '_TAXCOUNTRY', 'taxcountry', 'view', NULL, 3, 'class.taxcountry.php', 'EcomAdmin_TaxCountry');
				INSERT INTO `ecom_menu_under` VALUES (4, 1, 'taxcatgod', '_TAXCATGOD', 'taxcatgod', 'view', NULL, 4, 'class.taxcatgod.php', 'EcomAdmin_TaxCatGod');
				INSERT INTO `ecom_menu_under` VALUES (5, 1, 'taxrate', '_TAXRATE', 'taxrate', 'view', NULL, 5, 'class.taxrate.php', 'EcomAdmin_TaxRate');
				INSERT INTO `ecom_menu_under` VALUES (6, 2, 'transaction', '_TRANSACTION', 'transaction', 'view', NULL, 1, 'class.transaction.php', 'EcomAdmin_Transaction');
				INSERT INTO `ecom_menu_under` VALUES (7, 2, 'reservation', '_RESERVATION_APPROVAL', 'main', 'view', NULL, 2, 'class.reservation.php', 'EcomAdmin_Reservation');
				INSERT INTO `ecom_menu_under` VALUES (8, 2, 'bought', '_BOUGHT_ITEMS', 'main', 'view', NULL, 3, 'class.bought.php', 'EcomAdmin_Bought');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>