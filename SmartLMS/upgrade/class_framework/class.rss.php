<?php

class Upgrade_Rss extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'rss';
	
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
				
				$query = "CREATE TABLE `core_feed_cache` (
				  `feed_id` int(11) NOT NULL auto_increment,
				  `title` varchar(255) NOT NULL default '',
				  `url` varchar(255) NOT NULL default '',
				  `image` varchar(255) NOT NULL default '',
				  `content` text NOT NULL,
				  `active` tinyint(1) NOT NULL default '0',
				  `refresh_time` int(5) NOT NULL default '0',
				  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
				  `show_on_platform` text NOT NULL,
				  `zone` VARCHAR( 255 ) DEFAULT 'public' NOT NULL,
				  `ord` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`feed_id`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "INSERT INTO `core_feed_cache` (`feed_id`, `title`, `url`, `image`, `content`, `active`, `refresh_time`, `last_update`, `show_on_platform`, `zone`, `ord`)  VALUES (1, 'Docebo Feed', 'http://www.docebo.org/doceboCms/feed.php?alias=news', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 0); 
				INSERT INTO `core_feed_cache` (`feed_id`, `title`, `url`, `image`, `content`, `active`, `refresh_time`, `last_update`, `show_on_platform`, `zone`, `ord`) VALUES (2, 'Bugs Feed', 'http://www.docebo.org/doceboCms/feed.php?alias=fixed_bugs&lang=english', '', '', 0, 1440, '0000-00-00 00:00:00', '', 'dashboard', 1);
				INSERT INTO `core_feed_cache` (`feed_id`, `title`, `url`, `image`, `content`, `active`, `refresh_time`, `last_update`, `show_on_platform`, `zone`, `ord`) VALUES (3, 'Docebo.com Feed', 'http://www.docebo.com/doceboCms/feed.php?alias=news_com', '', '', 0, 1440, '0000-00-00 00:00:00', '', 'dashboard', 2);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "INSERT INTO `cms_blocktype` VALUES ('feedreader', '', '_BLK_FEEDREADER');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			
		}
		return true;
	}
}

?>