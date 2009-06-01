-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 04 Lug, 2008 at 11:51 AM
-- Versione MySQL: 5.0.51
-- Versione PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `docebo_3504`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_menu`
--

CREATE TABLE `ecom_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_menu`
--

INSERT INTO `ecom_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_ECOMMERCE_MANAGMENT', '', 1, 'false'),
(2, '', '', 2, 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_menu_under`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_menu_under`
--

INSERT INTO `ecom_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'payaccount', '_PAYACCOUNT', 'payaccount', 'view', NULL, 1, 'class.payaccount.php', 'EcomAdmin_PayAccount'),
(2, 1, 'taxzone', '_TAXZONE', 'taxzone', 'view', NULL, 2, 'class.taxzone.php', 'EcomAdmin_TaxZone'),
(3, 1, 'taxcountry', '_TAXCOUNTRY', 'taxcountry', 'view', NULL, 3, 'class.taxcountry.php', 'EcomAdmin_TaxCountry'),
(4, 1, 'taxcatgod', '_TAXCATGOD', 'taxcatgod', 'view-hidden', NULL, 4, 'class.taxcatgod.php', 'EcomAdmin_TaxCatGod'),
(5, 1, 'taxrate', '_TAXRATE', 'taxrate', 'view', NULL, 5, 'class.taxrate.php', 'EcomAdmin_TaxRate'),
(6, 2, 'transaction', '_TRANSACTION', 'transaction', 'view', NULL, 1, 'class.transaction.php', 'EcomAdmin_Transaction');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_paramset_fieldgrp`
--

CREATE TABLE `ecom_paramset_fieldgrp` (
  `fieldgrp_id` int(11) NOT NULL auto_increment,
  `set_id` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `description` text NOT NULL,
  `is_main` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fieldgrp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_paramset_fieldgrp`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_paramset_grpitem`
--

CREATE TABLE `ecom_paramset_grpitem` (
  `item_id` int(11) NOT NULL auto_increment,
  `fieldgrp_id` int(11) NOT NULL default '0',
  `set_id` int(11) NOT NULL default '0',
  `idField` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `compulsory` tinyint(1) NOT NULL default '0',
  `ord` int(3) NOT NULL default '0',
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_paramset_grpitem`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_payaccount`
--

CREATE TABLE `ecom_payaccount` (
  `account_name` varchar(100) NOT NULL default '',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `active` enum('true','false') NOT NULL default 'true',
  PRIMARY KEY  (`account_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_payaccount`
--

INSERT INTO `ecom_payaccount` (`account_name`, `class_file`, `class_name`, `active`) VALUES
('check', 'class.check.php', 'PayAccount_Check', 'false'),
('mark', 'class.mark.php', 'PayAccount_Mark', 'false'),
('money_order', 'class.money_order.php', 'PayAccount_MoneyOrder', 'false'),
('paypal', 'class.paypal.php', 'PayAccount_PayPal', 'false'),
('wire_transfer', 'class.wire_transfer.php', 'PayAccount_WireTransfer', 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_payaccount_setting`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_payaccount_setting`
--

INSERT INTO `ecom_payaccount_setting` (`account_name`, `param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('wire_transfer', 'abi', '3200', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'address', 'via boccaccio 1', 'string', 255, 'main', 0, 0, 1, 0, ''),
('paypal', 'back_ko', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('paypal', 'back_ko_buyer', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('paypal', 'back_ok', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('paypal', 'back_ok_buyer', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('wire_transfer', 'bank_account', '44047438', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'cab', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'cin', 'ww', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'city', 'acquaviva delle fonti', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'company', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('paypal', 'email', 'info@smsmarket.it', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'iban', 'boh', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'name', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('check', 'registered_person', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'surname', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'zip_code', '70021', 'string', 255, 'main', 0, 0, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product`
--

CREATE TABLE `ecom_product` (
  `prd_id` int(11) NOT NULL auto_increment,
  `prd_code` varchar(255) NOT NULL default '',
  `price` varchar(20) NOT NULL default '',
  `param_set_id` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `can_add_to_cart` tinyint(1) NOT NULL default '0',
  `ord` int(11) default NULL,
  PRIMARY KEY  (`prd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_cat`
--

CREATE TABLE `ecom_product_cat` (
  `cat_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(3) NOT NULL default '0',
  `param_set_id` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_cat_info`
--

CREATE TABLE `ecom_product_cat_info` (
  `cat_id` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cat_id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_cat_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_cat_item`
--

CREATE TABLE `ecom_product_cat_item` (
  `cat_id` int(11) NOT NULL default '0',
  `prd_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`,`prd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_cat_item`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_field`
--

CREATE TABLE `ecom_product_field` (
  `idField` int(11) NOT NULL auto_increment,
  `id_common` int(11) NOT NULL default '0',
  `type_field` varchar(255) NOT NULL default '',
  `lang_code` varchar(255) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `show_on_platform` varchar(255) NOT NULL default 'framework,',
  `use_multilang` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idField`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_field_entry`
--

CREATE TABLE `ecom_product_field_entry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_field_entry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_img`
--

CREATE TABLE `ecom_product_img` (
  `img_id` int(11) NOT NULL auto_increment,
  `prd_id` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`img_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_img`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_info`
--

CREATE TABLE `ecom_product_info` (
  `prd_id` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`prd_id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_reservation`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_reservation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_setting`
--

CREATE TABLE `ecom_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_setting`
--

INSERT INTO `ecom_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('admin_mail', 'sample@localhost.com', 'string', 255, 0, 2, 1, 0, ''),
('ecom_type', 'standard', 'ecommerce_type', 30, 0, 4, 1, 1, ''),
('ttlSession', '1000', 'int', 5, 0, 3, 1, 0, ''),
('url', 'http://localhost/docebo_35/doceboEcom/', 'string', 255, 0, 1, 1, 1, ''),
('company_details', '', 'textarea', 65535, 0, 5, 1, 0, ''),
('send_order_email', '', 'string', 255, 0, 6, 1, 0, ''),
('currency_label', '&euro;', 'string', 255, 0, 7, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_tax_cat_god`
--

CREATE TABLE `ecom_tax_cat_god` (
  `id_cat_god` int(11) NOT NULL auto_increment,
  `name_cat_god` varchar(255) NOT NULL default '',
  `cat_code` enum('course') default NULL,
  PRIMARY KEY  (`id_cat_god`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_tax_cat_god`
--

INSERT INTO `ecom_tax_cat_god` (`id_cat_god`, `name_cat_god`, `cat_code`) VALUES
(1, 'Online courses', 'course');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_tax_rate`
--

CREATE TABLE `ecom_tax_rate` (
  `id_zone` int(11) NOT NULL default '0',
  `id_cat_god` int(11) NOT NULL default '0',
  `rate` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id_zone`,`id_cat_god`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_tax_rate`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_tax_zone`
--

CREATE TABLE `ecom_tax_zone` (
  `id_zone` int(11) NOT NULL auto_increment,
  `name_zone` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_tax_zone`
--

INSERT INTO `ecom_tax_zone` (`id_zone`, `name_zone`) VALUES
(1, '_EUROPE'),
(2, '_USA'),
(3, '_REST_OF_THE_WORLD'),
(4, '_TAX_FREE');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_transaction`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_transaction`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_transaction_product`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_transaction_product`
--


-- ---------------------------------------------------------- --------------------------------------------------------