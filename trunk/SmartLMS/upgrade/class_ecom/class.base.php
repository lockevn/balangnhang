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

class Upgrade_EcomBase extends Upgrade {
	
	var $platfom = 'ecom';
	
	var $mname = 'base';
	
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
				CREATE TABLE `ecom_payaccount` (
				  `account_name` varchar(100) NOT NULL default '',
				  `class_file` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  `active` enum('true','false') NOT NULL default 'true',
				  PRIMARY KEY  (`account_name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `ecom_payaccount` VALUES ('check', 'class.check.php', 'PayAccount_Check', 'false');
				INSERT INTO `ecom_payaccount` VALUES ('mark', 'class.mark.php', 'PayAccount_Mark', 'false');
				INSERT INTO `ecom_payaccount` VALUES ('money_order', 'class.money_order.php', 'PayAccount_MoneyOrder', 'false');
				INSERT INTO `ecom_payaccount` VALUES ('paypal', 'class.paypal.php', 'PayAccount_PayPal', 'false');
				INSERT INTO `ecom_payaccount` VALUES ('wire_transfer', 'class.wire_transfer.php', 'PayAccount_WireTransfer', 'true')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_payaccount_setting` (
				  `account_name` varchar(100) NOT NULL default '',
				  `param_name` varchar(100) NOT NULL default '',
				  `param_value` text NOT NULL,
				  `value_type` varchar(255) NOT NULL default 'string',
				  `max_size` int(3) NOT NULL default '255',
				  `pack` varchar(255) NOT NULL default 'main',
				  `regroup` int(5) NOT NULL default '0',
				  `sequence` int(5) NOT NULL default '0',
				  `param_load` tinyint(1) NOT NULL default '1',
				  `hide_in_modify` tinyint(1) NOT NULL default '0',
				  `extra_info` text NOT NULL,
				  PRIMARY KEY  (`param_name`,`account_name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `ecom_payaccount_setting` VALUES ('wire_transfer', 'abi', '3200', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('money_order', 'address', 'via boccaccio 1', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('paypal', 'back_ko', '', 'string', 255, 'main', 0, 0, 1, 1, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('paypal', 'back_ko_buyer', '', 'string', 255, 'main', 0, 0, 1, 1, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('paypal', 'back_ok', '', 'string', 255, 'main', 0, 0, 1, 1, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('paypal', 'back_ok_buyer', '', 'string', 255, 'main', 0, 0, 1, 1, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('wire_transfer', 'bank_account', '44047438', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('wire_transfer', 'cab', '', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('wire_transfer', 'cin', 'ww', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('money_order', 'city', 'acquaviva delle fonti', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('wire_transfer', 'company', 'Claudio Erba', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('paypal', 'email', 'info@smsmarket.it', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('wire_transfer', 'iban', 'boh', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('money_order', 'name', 'claudio', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('check', 'registered_person', 'claudio demarinis', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('money_order', 'surname', 'demarinis', 'string', 255, 'main', 0, 0, 1, 0, '');
				INSERT INTO `ecom_payaccount_setting` VALUES ('money_order', 'zip_code', '70021', 'string', 255, 'main', 0, 0, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_reservation` (
				  `reservation_id` int(11) NOT NULL auto_increment,
				  `product_code` varchar(255) NOT NULL default '',
				  `company_id` int(11) NOT NULL default '0',
				  `user_id` int(255) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `type` enum('course','course_edition','other') NOT NULL default 'course',
				  `price` varchar(255) NOT NULL default '',
				  `reservation_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`reservation_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_tax_cat_god` (
				  `id_cat_god` int(11) NOT NULL auto_increment,
				  `name_cat_god` varchar(255) NOT NULL default '',
				  `cat_code` enum('course') default NULL,
				  PRIMARY KEY  (`id_cat_god`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_tax_rate` (
				  `id_zone` int(11) NOT NULL default '0',
				  `id_cat_god` int(11) NOT NULL default '0',
				  `rate` int(2) NOT NULL default '0',
				  PRIMARY KEY  (`id_zone`,`id_cat_god`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_tax_zone` (
				  `id_zone` int(11) NOT NULL auto_increment,
				  `name_zone` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id_zone`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `ecom_tax_zone` VALUES (1, '_EUROPE');
				INSERT INTO `ecom_tax_zone` VALUES (2, '_USA');
				INSERT INTO `ecom_tax_zone` VALUES (3, '_REST_OF_THE_WORLD');
				INSERT INTO `ecom_tax_zone` VALUES (4, '_TAX_FREE')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_transaction` (
				  `id_trans` int(11) NOT NULL auto_increment,
				  `id_user` int(11) NOT NULL default '0',
				  `company_id` int(11) NOT NULL default '0',
				  `total_amount` float NOT NULL default '0',
				  `transaction_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `order_status` enum('NOTPROC','PROC','PARTPROC','CANC') NOT NULL default 'NOTPROC',
				  `payment_status` enum('NOTPAY','PAYED','PARTPAY','CANC') NOT NULL default 'NOTPAY',
				  `order_notes` text NOT NULL,
				  `payment_notes` text NOT NULL,
				  `payment_type` varchar(255) NOT NULL default '0',
				  `active_status` enum('none','partial','all') NOT NULL default 'none',
				  PRIMARY KEY  (`id_trans`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_transaction_product` (
				  `product_id` int(11) NOT NULL auto_increment,
				  `id_trans` int(11) NOT NULL default '0',
				  `id_prod` varchar(255) NOT NULL default '',
				  `id_user` int(11) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `type` enum('course','course_edition','other') NOT NULL default 'course',
				  `price` varchar(255) NOT NULL default '',
				  `quantity` int(11) NOT NULL default '0',
				  `active` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`product_id`),
				  UNIQUE KEY `id_trans` (`id_trans`,`id_prod`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				$content = "				
				CREATE TABLE `ecom_paramset` (
				  `set_id` int(11) NOT NULL auto_increment,
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  PRIMARY KEY  (`set_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_paramset_fieldgrp` (
				  `fieldgrp_id` int(11) NOT NULL auto_increment,
				  `set_id` int(11) NOT NULL default '0',
				  `title` text NOT NULL,
				  `description` text NOT NULL,
				  `is_main` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`fieldgrp_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "			
				CREATE TABLE `ecom_paramset_grpitem` (
				  `item_id` int(11) NOT NULL auto_increment,
				  `fieldgrp_id` int(11) NOT NULL default '0',
				  `set_id` int(11) NOT NULL default '0',
				  `idField` int(11) NOT NULL default '0',
				  `type` varchar(20) NOT NULL default '',
				  `compulsory` tinyint(1) NOT NULL default '0',
				  `ord` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`item_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product` (
				  `prd_id` int(11) NOT NULL auto_increment,
				  `prd_code` varchar(255) NOT NULL default '',
				  `price` varchar(20) NOT NULL default '',
				  `param_set_id` int(11) NOT NULL,
				  `image` varchar(255) NOT NULL default '',
				  `can_add_to_cart` tinyint(1) NOT NULL,
				  PRIMARY KEY  (`prd_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_cat` (
				  `cat_id` int(11) NOT NULL auto_increment,
				  `parent_id` int(11) NOT NULL default '0',
				  `path` varchar(255) NOT NULL default '',
				  `lev` int(3) NOT NULL default '0',
				  `param_set_id` int(11) NOT NULL default '0',
				  `image` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`cat_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_cat_info` (
				  `cat_id` int(11) NOT NULL,
				  `language` varchar(50) NOT NULL,
				  `title` varchar(255) NOT NULL,
				  PRIMARY KEY  (`cat_id`,`language`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_cat_item` (
				  `cat_id` int(11) NOT NULL default '0',
				  `prd_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`cat_id`,`prd_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_field` (
				  `idField` int(11) NOT NULL auto_increment,
				  `id_common` int(11) NOT NULL default '0',
				  `type_field` varchar(255) NOT NULL default '',
				  `lang_code` varchar(255) NOT NULL default '',
				  `translation` varchar(255) NOT NULL default '',
				  `sequence` int(5) NOT NULL default '0',
				  `show_on_platform` varchar(255) NOT NULL default 'framework,',
				  `use_multilang` tinyint(1) NOT NULL,
				  PRIMARY KEY  (`idField`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_field_entry` (
				  `id_common` varchar(11) NOT NULL default '',
				  `id_common_son` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `language` varchar(50) NOT NULL,
				  `user_entry` text NOT NULL,
				  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`,`language`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_img` (
				  `img_id` int(11) NOT NULL auto_increment,
				  `prd_id` int(11) NOT NULL default '0',
				  `image` varchar(255) NOT NULL default '',
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `published` tinyint(1) NOT NULL default '0',
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`img_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `ecom_product_info` (
				  `prd_id` int(11) NOT NULL,
				  `language` varchar(50) NOT NULL,
				  `title` varchar(255) NOT NULL,
				  `description` text NOT NULL,
				  PRIMARY KEY  (`prd_id`,`language`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>