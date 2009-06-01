<?php

class Upgrade_Settingscs extends Upgrade {
	
	var $platfom = 'scs';
	
	var $mname = 'setting';
	
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
				
				$query = "CREATE TABLE `conference_setting` (
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
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `conference_setting` VALUES ('defaultTemplate', 'standard', 'string', 255, 0, 1, 1, 1, '');
				INSERT INTO `conference_setting` VALUES ('url_checkin_teleskill', 'http://ews.teleskill.it/ews/checkin.asp', 'string', 255, 2, 1, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('url_videoconference_teleskill', '', 'string', 255, 2, 2, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('org_name_teleskill', '', 'string', 255, 2, 4, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('code_teleskill', '', 'string', 255, 2, 3, 1, 0, '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "
				UPDATE `conference_setting` 
				SET param_name = 'url_checkin_teleskill' 
				WHERE param_value = 'http://ews.teleskill.it/ews/server.asp'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.5.0" : {
				$i = 0;
				
				$content = "CREATE TABLE `conference_dimdim` (
				  `id` bigint(20) NOT NULL auto_increment,
				  `idConference` bigint(20) NOT NULL default '0',
				  `confkey` varchar(255) default NULL,
				  `emailuser` varchar(255) default NULL,
				  `displayname` varchar(255) default NULL,
				  `timezone` varchar(255) default NULL,
				  `audiovideosettings` int(11) default NULL,
				  `maxmikes` int(11) default NULL,
				  PRIMARY KEY  (`id`),
				  KEY `idConference` (`idConference`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "CREATE TABLE `conference_room` (
				  `id` bigint(20) NOT NULL auto_increment,
				  `idCal` BIGINT NOT NULL,
				  `idCourse` bigint(20) NOT NULL default '0',
				  `idSt` bigint(20) NOT NULL default '0',
				  `name` varchar(255) default NULL,
				  `room_type` varchar(255) default NULL,
				  `starttime` bigint(20) default NULL,
				  `endtime` bigint(20) default NULL,
				  `meetinghours` int(11) default NULL,
				  `maxparticipants` int(11) default NULL,
				  PRIMARY KEY  (`id`),
				  KEY `idCourse` (`idCourse`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;				
					
				$content = "CREATE TABLE `conference_teleskill` (
				  `id` bigint(20) NOT NULL auto_increment,
				  `idConference` bigint(20) NOT NULL default '0',
				  `roomid` bigint(20) NOT NULL default '0',
				  PRIMARY KEY  (`id`),
				  KEY `idConference` (`idConference`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;	
				
				$content = "
				INSERT INTO `conference_setting` VALUES ('conference_creation_limit_per_user', '1', 'string', 255, 0, 0, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('dimdim_max_mikes', '2', 'string', 255, 6, 5, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('dimdim_max_participant', '', 'string', 255, 6, 4, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('dimdim_max_room', '', 'string', 255, 6, 3, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('dimdim_port', '80', 'string', 255, 6, 2, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('dimdim_server', 'www1.dimdim.com', 'string', 255, 6, 1, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('intelligere_max_participant', '', 'string', 255, 4, 7, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('intelligere_max_room', '', 'string', 255, 4, 6, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('teleskill_max_participant', '', 'string', 255, 2, 6, 1, 0, '');
				INSERT INTO `conference_setting` VALUES ('teleskill_max_room', '', 'string', 255, 2, 5, 1, 0, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
    
				$this->end_version = '3.5.0.1';
				return true;
			};break;
		}
		return true;
	}
}

?>