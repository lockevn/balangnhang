<?php

class Upgrade_Rules extends Upgrade {
	
	var $platfom = 'scs';
	
	var $mname = 'rules';
	
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
				
				$query = "CREATE TABLE `conference_chat_msg` (
				  `msg_id` int(11) NOT NULL auto_increment,
				  `id_user` int(11) NOT NULL default '0',
				  `id_room` int(11) NOT NULL default '0',
				  `userid` varchar(255) NOT NULL default '',
				  `send_to` int(11) default NULL,
				  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `text` text NOT NULL,
				  PRIMARY KEY  (`msg_id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `conference_rules_admin` (
				  `server_status` enum('yes','no') NOT NULL default 'yes',
				  `enable_recording_function` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_advice_insert` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_write` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_chat_recording` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_private_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_public_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_drawboard_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_drawboard_write` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_audio` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_webcam` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_stream_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_strem_write` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_remote_desktop` enum('admin','alluser','noone') NOT NULL default 'noone',
				  PRIMARY KEY  (`server_status`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "INSERT INTO `conference_rules_admin` VALUES ('yes', 'admin', 'admin', 'admin', 'admin', 'admin', 'admin', 'alluser', 'admin', 'alluser', 'alluser', 'alluser', 'admin', 'admin');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "CREATE TABLE `conference_rules_room` (
				  `id_room` int(11) NOT NULL auto_increment,
				  `enable_recording_function` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_advice_insert` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_write` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_chat_recording` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_private_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_public_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_drawboard_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_drawboard_write` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_audio` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_webcam` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_stream_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_strem_write` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `enable_remote_desktop` enum('admin','alluser','noone') NOT NULL default 'noone',
				  `room_name` varchar(255) NOT NULL default '',
				  `room_type` enum('course','private','public') NOT NULL default 'course',
				  `id_source` int(11) NOT NULL default '0',
				  `room_parent` int(11) NOT NULL default '0',
				  `advice_one` text,
				  `advice_two` text,
				  `advice_three` text,
				  `room_logo` varchar(255) default NULL,
				  `room_sponsor` varchar(255) default NULL,
				  PRIMARY KEY  (`id_room`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "CREATE TABLE `conference_rules_root` (
				  `system_type` enum('p2p','server') NOT NULL default 'p2p',
				  `server_ip` varchar(255) default NULL,
				  `server_port` int(5) unsigned default NULL,
				  `server_path` varchar(255) default NULL,
				  `max_user_at_time` int(11) unsigned NOT NULL default '0',
				  `max_room_at_time` int(11) unsigned NOT NULL default '0',
				  `max_subroom_for_room` int(11) unsigned NOT NULL default '0',
				  `enable_drawboard` enum('yes','no') NOT NULL default 'no',
				  `enable_livestream` enum('yes','no') NOT NULL default 'no',
				  `enable_remote_desktop` enum('yes','no') NOT NULL default 'no',
				  `enable_webcam` enum('yes','no') NOT NULL default 'no',
				  `enable_audio` enum('yes','no') NOT NULL default 'no',
				  PRIMARY KEY  (`system_type`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$query = "INSERT INTO `conference_rules_root` VALUES ('server', '127.0.0.1', 123, '/', 60, 0, 0, 'yes', 'no', 'no', 'yes', 'yes');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				$query = "CREATE TABLE `conference_rules_user` (
				  `id_user` int(11) NOT NULL auto_increment,
				  `last_hit` int(11) NOT NULL default '0',
				  `id_room` int(11) NOT NULL default '0',
				  `userid` varchar(255) NOT NULL default '',
				  `user_ip` varchar(15) NOT NULL default '',
				  `first_name` varchar(255) NOT NULL default '',
				  `last_name` varchar(255) NOT NULL default '',
				  `level` int(11) NOT NULL default '0',
				  `auto_reload` tinyint(1) NOT NULL default '0',
				  `banned_until` datetime default NULL,
				  `chat_record` enum('yes','no') NOT NULL default 'no',
				  `advice_insert` enum('yes','no') NOT NULL default 'no',
				  `write_in_chat` enum('yes','no') NOT NULL default 'no',
				  `request_to_chat` enum('yes','no') NOT NULL default 'no',
				  `create_public_subroom` enum('yes','no') NOT NULL default 'no',
				  `enable_webcam` enum('yes','no') NOT NULL default 'no',
				  `enable_audio` enum('yes','no') NOT NULL default 'no',
				  `enable_drawboard_watch` enum('yes','no') NOT NULL default 'no',
				  `enable_drawboard_draw` enum('yes','no') NOT NULL default 'no',
				  `enable_livestream_watch` enum('yes','no') NOT NULL default 'no',
				  `enable_livestream_publish` enum('yes','no') NOT NULL default 'no',
				  `accept_private_message` enum('yes','no') NOT NULL default 'no',
				  `picture` varchar(255) default NULL,
				  PRIMARY KEY  (`id_user`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				
				
				
				$query = "
				SELECT idCourse, name
				FROM learning_course ";
				$re_courses = $this->db_man->query($query);
				while(list($id_course, $name) = $this->db_man->fetchRow($re_courses)) {
					
					$query = "INSERT INTO `conference_rules_room` VALUES ('".$id_course."', 'admin', 'admin', 'admin', 'admin', 'admin', 'admin', 'alluser', 'admin', 'alluser', 'alluser', 'alluser', 'admin', 'admin', '".$name."', 'course', '".$id_course."', 0, NULL, NULL, NULL, NULL, NULL);";
					$this->db_man->query($query);
				}
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>