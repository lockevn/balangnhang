<?php

class Upgrade_CmsMenu extends Upgrade {
	
	var $platfom = 'cms';
	
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
			
			case "3.0.2" : {
				
				$query = "DROP TABLE `cms_menu`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "DROP TABLE `cms_menu_under`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `cms_menu` (
				  `idMenu` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  `collapse` enum('true','false') NOT NULL default 'false',
				  PRIMARY KEY  (`idMenu`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `cms_menu` VALUES (1, '', '', 1, 'true');
				INSERT INTO `cms_menu` VALUES (2, '_NEWS', '', 2, 'false');
				INSERT INTO `cms_menu` VALUES (4, '_CMS_BANNER', '', 4, 'false');
				INSERT INTO `cms_menu` VALUES (3, '_CMS_CONTENT', '', 3, 'false');
				INSERT INTO `cms_menu` VALUES (5, '_CMS_CONFIG', '', 5, 'false');
				INSERT INTO `cms_menu` VALUES (6, '_CMS_STATS', 'area_stats', 6, 'false');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `cms_menu_under` (
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
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "
				INSERT INTO `cms_menu_under` VALUES (1, 1, 'manpage', '_MANPAGE', 'manpage', 'view', NULL, 1, 'class.manpage.php', 'Module_Manpage');
				INSERT INTO `cms_menu_under` VALUES (2, 2, 'news', '_NEWS', 'news', 'view', NULL, 2, 'class.news.php', 'Module_News');
				INSERT INTO `cms_menu_under` VALUES (3, 2, 'mantopic', '_MANTOPIC', 'mantopic', 'view', NULL, 3, 'class.mantopic.php', 'Module_Mantopic');
				INSERT INTO `cms_menu_under` VALUES (4, 3, 'content', '_CONTENT', 'content', 'view', NULL, 4, 'class.content.php', 'Module_Content');
				INSERT INTO `cms_menu_under` VALUES (5, 3, 'docs', '_DOCS', 'docs', 'view', NULL, 5, 'class.docs.php', 'Module_Docs');
				INSERT INTO `cms_menu_under` VALUES (6, 3, 'media', '_MEDIA', 'media', 'view', NULL, 6, 'class.media.php', 'Module_Media');
				INSERT INTO `cms_menu_under` VALUES (7, 3, 'links', '_LINKS', 'links', 'view', NULL, 7, 'class.links.php', 'Module_Links');
				INSERT INTO `cms_menu_under` VALUES (8, 4, 'banners', '_BANNER_CAT', 'viewcat', 'view', NULL, 8, '', '');
				INSERT INTO `cms_menu_under` VALUES (9, 4, 'banners', '_BANNER', 'banners', 'view', NULL, 9, 'class.banners.php', 'Module_Banners');
				INSERT INTO `cms_menu_under` VALUES (10, 5, 'forum', '_FORUM', 'forum', 'view', NULL, 10, 'class.forum.php', 'Module_Forum');
				INSERT INTO `cms_menu_under` VALUES (11, 5, 'poll', '_POLL', 'poll', 'view', NULL, 11, '', '');
				INSERT INTO `cms_menu_under` VALUES (12, 5, 'form', '_FORM', 'form', 'view', NULL, 12, 'class.form.php', 'Module_Form');
				INSERT INTO `cms_menu_under` VALUES (13, 6, 'stats', '_STATS_MAIN', 'stats', 'view', NULL, 14, 'class.stats.php', 'Module_Stats');
				INSERT INTO `cms_menu_under` VALUES (14, 6, 'stats', '_STATS_DETAILS', 'statsdetails', 'view', NULL, 15, '', '');
				INSERT INTO `cms_menu_under` VALUES (15, 6, 'stats', '_STATS_TEMPORAL', 'statstemporal', 'view', NULL, 16, '', '');
				INSERT INTO `cms_menu_under` VALUES (16, 2, 'feedreader', '_FEEDREADER', 'feedreader', 'view', 'framework', 9, 'class.feedreader.php', 'Module_FeedReader');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			
		}
		return true;
	}
}

?>