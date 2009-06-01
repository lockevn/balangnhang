<?php

class Upgrade_Poll extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'poll';
	
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
				
				$query = "CREATE TABLE `learning_quest_type_poll` (
				  `type_quest` varchar(255) NOT NULL default '',
				  `type_file` varchar(255) NOT NULL default '',
				  `type_class` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`type_quest`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `learning_quest_type_poll` VALUES ('choice', 'class.choice.php', 'Choice_Question', 1);
				INSERT INTO `learning_quest_type_poll` VALUES ('choice_multiple', 'class.choice_multiple.php', 'ChoiceMultiple_Question', 2);
				INSERT INTO `learning_quest_type_poll` VALUES ('title', 'class.title.php', 'Title_Question', 3);
				INSERT INTO `learning_quest_type_poll` VALUES ('break_page', 'class.break_page.php', 'BreakPage_Question', 4);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "ALTER TABLE `learning_poll` 
				CHANGE `idPoll` `id_poll` INT( 11 ) NOT NULL AUTO_INCREMENT ,
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "
				ALTER TABLE `learning_pollquest` 
				CHANGE `idQuest` `id_quest` INT( 11 ) NOT NULL AUTO_INCREMENT ,
				CHANGE `idPoll` `id_poll` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `idCategory` `id_category` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `title` `title_quest` TEXT NOT NULL ,
				CHANGE `type` `type_quest` VARCHAR( 255 ) NOT NULL DEFAULT '';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "ALTER TABLE `learning_pollquest` DROP `mustdo` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "ALTER TABLE `learning_pollquest` ADD `page` INT( 11 ) NOT NULL DEFAULT '1';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "CREATE TABLE `learning_pollquest_extra` (
				  `id_quest` int(11) NOT NULL default '0',
				  `id_answer` int(11) NOT NULL default '0',
				  `extra_info` text NOT NULL,
				  PRIMARY KEY  (`id_quest`,`id_answer`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$query = "ALTER TABLE `learning_pollanswer` RENAME `learning_pollquestanswer` ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				$query = "ALTER TABLE `learning_pollquestanswer` 
				CHANGE `idAnswer` `id_answer` INT( 11 ) NOT NULL AUTO_INCREMENT ,
				CHANGE `idQuest` `id_quest` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `title` `answer` TEXT NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				
				$query = "ALTER TABLE `learning_pollquestanswer` ADD `sequence` INT( 11 ) NOT NULL AFTER `id_quest` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 9);
				
				$query = "ALTER TABLE `learning_polltrack` 
				CHANGE `idTrack` `id_track` INT( 11 ) NOT NULL AUTO_INCREMENT ,
				CHANGE `idReference` `id_reference` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `idUser` `id_user` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `idPoll` `id_poll` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `data_attempt` `date_attempt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 10);
				
				$query = "ALTER TABLE `learning_polltrack` ADD `status` ENUM( 'valid', 'not_complete' ) DEFAULT 'not_complete' NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 11);
				
				$query = "ALTER TABLE `learning_polltrackanswer` RENAME `learning_polltrack_answer` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 12);
				
				$query = "ALTER TABLE `learning_polltrack_answer` DROP `id` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 13);
				
				$query = "ALTER TABLE `learning_polltrack_answer` CHANGE `idTrack` `id_track` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `idQuest` `id_quest` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `idAnswer` `id_answer` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `text_free` `more_info` LONGTEXT NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 14);
				
				$query = "ALTER TABLE `learning_polltrack_answer` ADD PRIMARY KEY ( `id_track` , `id_quest` , `id_answer` ) ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 15);
				
				$query = "
				UPDATE `learning_pollquest` 
				SET type_quest = 'choice'
				WHERE type_quest = '0'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 16);
				
				$query = "
				UPDATE `learning_pollquest` 
				SET type_quest = 'choice_multiple'
				WHERE type_quest = '1'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 17);
				
				$query = "
				UPDATE `learning_pollquest` 
				SET type_quest = 'textfree'
				WHERE type_quest = '2'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 18);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "
				ALTER TABLE `learning_pollquest` CHANGE `page` `page` INT( 11 ) DEFAULT '1' NOT NULL ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
		}
		return true;
	}
}

?>