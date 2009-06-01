<?php

class Upgrade_CmsBase extends Upgrade {
	
	var $platfom = 'cms';
	
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
			case "2.0.4" : {
				
				$fn = $GLOBALS['where_upgrade'].'/data/sql/cms.sql';
				
				$handle = fopen($fn, "r");
				$content = fread($handle, filesize($fn));
				fclose($handle);
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "
				CREATE TABLE `cms_area_block_filter` (
				  `block_id` int(11) NOT NULL default '0',
				  `block_type` varchar(255) NOT NULL default '',
				  `id_type` varchar(60) NOT NULL default '',
				  `id_val` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`block_id`,`block_type`,`id_type`,`id_val`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `cms_tree_perm` (
				  `type` varchar(10) NOT NULL default '',
				  `user_id` int(11) NOT NULL default '0',
				  `node_id` int(11) NOT NULL default '0',
				  `recursive` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`type`,`user_id`,`node_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				ALTER TABLE `cms_area` ADD `show_in_menu` TINYINT( 1 ) DEFAULT '1' NOT NULL ,
				ADD `show_in_macromenu` TINYINT( 1 ) DEFAULT '1' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `cms_media` ADD `media_url` VARCHAR( 255 ) NOT NULL AFTER `fpreview`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				
				$content = "ALTER TABLE `cms_content_dir` DROP INDEX `path`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `cms_docs_dir` DROP INDEX `path`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `cms_links_dir` DROP INDEX `path`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `cms_media_dir` DROP INDEX `path`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `cms_news_dir` DROP INDEX `path`";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `cms_blocktype` VALUES ('calendar', '', '_BLK_CALENDAR');
				INSERT INTO `cms_blocktype` VALUES ('chat_intelligere', 'chat', '_BLK_CHAT_INTELLIGERE');
				INSERT INTO `cms_blocktype` VALUES ('content_sel', 'content', '_BLK_CONTENT_SEL');
				INSERT INTO `cms_blocktype` VALUES ('content_sel_one', 'content', '_BLK_CONTENT_SEL_ONE');
				INSERT INTO `cms_blocktype` VALUES ('course_search', '', '_BLK_COURSE_SEARCH');
				INSERT INTO `cms_blocktype` VALUES ('crm', '', '_BLK_CRM');
				INSERT INTO `cms_blocktype` VALUES ('faq', '', '_BLK_FAQ');
				INSERT INTO `cms_blocktype` VALUES ('guestbook', '', '_BLK_GUESTBOOK');
				INSERT INTO `cms_blocktype` VALUES ('message', '', '_BLK_MESSAGE');
				INSERT INTO `cms_blocktype` VALUES ('myfiles', '', '_BLK_MYFILES');
				INSERT INTO `cms_blocktype` VALUES ('myfriends', '', '_BLK_MYFRIENDS');
				INSERT INTO `cms_blocktype` VALUES ('online_users', '', '_BLK_ONLINE_USERS');
				INSERT INTO `cms_blocktype` VALUES ('profile_search', '', '_BLK_PROFILE_SEARCH');
				INSERT INTO `cms_blocktype` VALUES ('profile_search_teacher', 'profile_search', '_BLK_PROFILE_SEARCH_TEACHER');
				INSERT INTO `cms_blocktype` VALUES ('ticket', '', '_BLK_TICKET');
				INSERT INTO `cms_blocktype` VALUES ('wiki', '', '_BLK_WIKI');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DELETE FROM `cms_menu` WHERE name = '_CMS_STATS'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				INSERT INTO `cms_menu_under` VALUES (NULL, 3, 'faq', '_FAQ', 'main', 'view', NULL, 17, 'class.faq.php', 'Module_Faq');
				INSERT INTO `cms_menu_under` VALUES (NULL, 3, 'wiki', '_WIKI', 'main', 'view', NULL, 18, 'class.wiki.php', 'Module_Wiki');
				INSERT INTO `cms_menu_under` VALUES (NULL, 5, 'calendar', '_CALENDAR', 'main', 'view', NULL, 20, 'class.calendar.php', 'Module_Calendar');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
       			
       			$content = "DELETE FROM `cms_menu_under` WHERE module_name = 'stats'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
       			$content = "INSERT INTO `cms_setting` VALUES ('cms_nl_sendpause', '20', 'int', 3, 1, 1, 1, 0);
				INSERT INTO `cms_setting` VALUES ('cms_nl_sendpercycle', '1', 'int', 4, 1, 0, 1, 0);
				INSERT INTO `cms_setting` VALUES ('cms_use_dropdown_menu', '1', 'check', 1, 0, 11, 1, 0);";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `pack` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES (
				'google_stat_code', '', 'textarea', '65535', 'main', '8', '1', '1', '0', ''
				);";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `core_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `pack` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES (
				'google_stat_in_cms', '1', 'check', '1', 'main', '8', '2', '1', '0', ''
				);";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$content = "INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES ('google_stat_in_lms', '', 'check', '1', 'main', '8', '3', '1', '0', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `cms_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` )
				VALUES ('use_bbclone', '1', 'check', '1', '0', '12', '1', '0')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "DELETE FROM `core_menu_under` WHERE module_name='publication_flow' AND default_op='pubflow'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0" : {
				$i = 0;
				
				$content = "CREATE TABLE `cms_area_block_simpleprj` (
				  `block_id` int(11) NOT NULL default '0',
				  `project_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`block_id`,`project_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `cms_calendar` (
				  `calendar_id` int(11) NOT NULL auto_increment,
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  PRIMARY KEY  (`calendar_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `cms_calendar_item` (
				  `calendar_id` int(11) NOT NULL default '0',
				  `event_id` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`calendar_id`,`event_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "
				CREATE TABLE `cms_comment_ajax` (
				  `id_comment` int(11) NOT NULL auto_increment,
				  `resource_type` varchar(50) NOT NULL default '',
				  `external_key` varchar(200) NOT NULL default '',
				  `id_author` int(11) NOT NULL default '0',
				  `posted_on` datetime NOT NULL default '0000-00-00 00:00:00',
				  `textof` text NOT NULL,
				  `history_tree` varchar(255) NOT NULL default '',
				  `id_parent` int(11) NOT NULL default '0',
				  `moderated` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`id_comment`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `cms_simpleprj` (
				  `project_id` int(11) NOT NULL auto_increment,
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`project_id`)
				) ";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `cms_simpleprj_file` (
				  `file_id` int(11) NOT NULL auto_increment,
				  `project_id` int(11) NOT NULL default '0',
				  `fname` varchar(255) NOT NULL default '',
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `author` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`file_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `cms_simpleprj_task` (
				  `task_id` int(11) NOT NULL auto_increment,
				  `project_id` int(11) NOT NULL default '0',
				  `description` text NOT NULL,
				  `complete` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`task_id`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "INSERT INTO `cms_blocktype` VALUES ('simpleprj', '', '_BLK_SIMPLEPRJ');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "INSERT INTO `cms_menu_under` VALUES (NULL, 3, 'simpleprj', '_SIMPLEPRJ', 'main', 'view', NULL, 21, 'class.simpleprj.php', 'Module_SimplePrj');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$this->end_version = '3.5.0.1';
				return true;
			};break;
			case "3.5.0.4":{
				$i = 0;
				
				$content = "INSERT INTO `cms_blocktype` (`name`, `folder`, `label`) VALUES
							('scanmenu', '', '_BLK_SCANMENU');";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			};break;
			case "3.6.0":{
				$i = 0;
				
				$content = "ALTER TABLE `cms_area` ADD `last_modify` DATETIME NOT NULL ;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0.1';
				return true;
			};break;
			
		}
		return true;
	}
}

?>