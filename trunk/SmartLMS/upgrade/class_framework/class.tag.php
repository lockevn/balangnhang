<?php

class Upgrade_Tag extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'tag';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.5.0.4":
				$i = 0;
				
				$content = "CREATE TABLE `core_tag` (
						  `id_tag` int(11) NOT NULL auto_increment,
						  `tag_name` varchar(255) NOT NULL,
						  `id_parent` int(11) NOT NULL,
						  PRIMARY KEY  (`id_tag`),
						  KEY `tag_name` (`tag_name`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_tag_relation` (
						  `id_tag` int(11) NOT NULL,
						  `id_resource` int(11) NOT NULL,
						  `resource_type` varchar(255) NOT NULL,
						  `id_user` int(11) NOT NULL,
						  `private` tinyint(1) NOT NULL,
						  `id_course` int(11) NOT NULL,
						  PRIMARY KEY  (`id_tag`,`id_resource`,`resource_type`,`id_user`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `core_tag_resource` (
						  `id_resource` int(11) NOT NULL,
						  `resource_type` varchar(255) NOT NULL,
						  `title` varchar(255) NOT NULL,
						  `sample_text` text NOT NULL,
						  `permalink` text NOT NULL,
						  PRIMARY KEY  (`id_resource`,`resource_type`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>