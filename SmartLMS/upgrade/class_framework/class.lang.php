<?php

class Upgrade_Lang extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'lang';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "2.0.4" : {
				
				$query = "
				CREATE TABLE `core_lang_language` (
				  `lang_code` varchar(50) NOT NULL default '',
				  `lang_description` varchar(255) NOT NULL default '',
				  `lang_charset` varchar(20) NOT NULL default 'utf-8',
				  `lang_browsercode` varchar(50) NOT NULL default '',
				  PRIMARY KEY  (`lang_code`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `core_lang_text` (
				  `id_text` int(11) NOT NULL auto_increment,
				  `text_key` varchar(50) NOT NULL default '',
				  `text_module` varchar(50) NOT NULL default '',
				  `text_platform` varchar(50) NOT NULL default '',
				  `text_description` varchar(255) NOT NULL default '',
				  `text_attributes` set('accessibility','sms','email') NOT NULL default '',
				  PRIMARY KEY  (`id_text`),
				  UNIQUE KEY `text_key` (`text_key`,`text_module`,`text_platform`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `core_lang_text_translation` (
				  `id_text` int(11) NOT NULL default '0',
				  `id_translation` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_text`,`id_translation`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "CREATE TABLE `core_lang_translation` (
				  `id_translation` int(11) NOT NULL auto_increment,
				  `translation_text` text,
				  `lang_code` varchar(50) NOT NULL default '0',
				  PRIMARY KEY  (`id_translation`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `core_lang_language` ADD `lang_direction` ENUM( 'ltr', 'rtl' ) NOT NULL DEFAULT 'ltr';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `core_lang_translation` ADD `id_text` INT( 11 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `core_lang_translation` CHANGE `save_date` `save_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `core_lang_translation` ADD INDEX ( `lang_code`, `id_text` );";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "UPDATE `core_lang_translation` as t1, `core_lang_text_translation` as t2 SET t1.id_text=t2.id_text WHERE t2.id_translation=t1.id_translation";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DROP TABLE core_lang_text_translation;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>