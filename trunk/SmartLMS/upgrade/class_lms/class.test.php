<?php

class Upgrade_Test extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'test';
	
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
				
				$query = "CREATE TABLE `learning_quest_type` (
				  `type_quest` varchar(255) NOT NULL default '',
				  `type_file` varchar(255) NOT NULL default '',
				  `type_class` varchar(255) NOT NULL default '',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`type_quest`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "INSERT INTO `learning_quest_type` VALUES ('choice', 'class.choice.php', 'Choice_Question', 1);
				INSERT INTO `learning_quest_type` VALUES ('choice_multiple', 'class.choice_multiple.php', 'ChoiceMultiple_Question', 2);
				INSERT INTO `learning_quest_type` VALUES ('extended_text', 'class.extended_text.php', 'ExtendedText_Question', 3);
				INSERT INTO `learning_quest_type` VALUES ('text_entry', 'class.text_entry.php', 'TextEntry_Question', 4);
				INSERT INTO `learning_quest_type` VALUES ('upload', 'class.upload.php', 'Upload_Question', 7);
				INSERT INTO `learning_quest_type` VALUES ('title', 'class.title.php', 'Title_Question', 9);
				INSERT INTO `learning_quest_type` VALUES ('break_page', 'class.break_page.php', 'BreakPage_Question', 10);
				INSERT INTO `learning_quest_type` VALUES ('inline_choice', 'class.inline_choice.php', 'InlineChoice_Question', 5);
				INSERT INTO `learning_quest_type` VALUES ('associate', 'class.associate.php', 'Associate_Question', 8);
				INSERT INTO `learning_quest_type` VALUES ('hot_text', 'class.hot_text.php', 'HotText_Question', 6);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				
				$query = "ALTER TABLE `learning_test` 
				CHANGE `title` `title` VARCHAR( 255 ) NOT NULL ,
				CHANGE `typeValue` `point_type` TINYINT( 1 ) NOT NULL DEFAULT '0',
				CHANGE `showSolution` `show_solution` TINYINT( 1 ) NOT NULL DEFAULT '0',
				CHANGE `showResult` `show_score` TINYINT( 1 ) NOT NULL DEFAULT '1',
				
				ADD `point_required` DOUBLE NOT NULL DEFAULT '0' AFTER `point_type` ,
				ADD `display_type` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `point_required` ,
				ADD `order_type` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `display_type` ,
				ADD `shuffle_answer` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `order_type` ,
				ADD `question_random_number` INT( 4 ) NOT NULL DEFAULT '0' AFTER `shuffle_answer` ,
				ADD `save_keep` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `question_random_number` ,
				ADD `mod_doanswer` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `save_keep` ,
				ADD `can_travel` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `mod_doanswer` ,
				ADD `show_only_status` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `can_travel`,
				
				ADD `show_score_cat` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `show_score` ,
				ADD `show_doanswer` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `show_score_cat` ,
				
				ADD `time_dependent` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `show_solution` ,
				ADD `time_assigned` INT( 6 ) NOT NULL DEFAULT '0' AFTER `time_dependent` ,
				ADD `penality_test` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `time_assigned` ,
				ADD `penality_time_test` DOUBLE NOT NULL DEFAULT '0' AFTER `penality_test` ,
				ADD `penality_quest` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `penality_time_test` ,
				ADD `penality_time_quest` DOUBLE NOT NULL DEFAULT '0' AFTER `penality_quest` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "ALTER TABLE `learning_testquest` CHANGE `titleQuest` `title_quest` TEXT NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "ALTER TABLE `learning_testquest` 
				ADD `type_quest` VARCHAR( 255 ) NOT NULL AFTER `idCategory` ,
				ADD `difficult` INT( 1 ) DEFAULT '3' NOT NULL AFTER `title_quest` ,
				ADD `time_assigned` INT( 5 ) NOT NULL AFTER `difficult` ,
				ADD `page` INT( 11 ) DEFAULT '0' NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "
				SELECT idTest, typeOfTest 
				FROM learning_test ";
				$re_quest = $this->db_man->query($query);
				while(list($idTest, $type_of) = $this->db_man->fetchRow($re_quest)) {
					
					$query = "
					UPDATE learning_testquest 
					SET type_quest = '".( $type_of == 0 ? 'choice' : 'choice_multiple' )."'
					WHERE idTest = '".$idTest."'";
					$this->db_man->query($query);
				}
				
				$query = "ALTER TABLE `learning_test` DROP `typeOfTest`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$query = "CREATE TABLE `learning_testquest_extra` (
				  `idQuest` int(11) NOT NULL default '0',
				  `idAnswer` int(11) NOT NULL default '0',
				  `extra_info` text NOT NULL,
				  PRIMARY KEY  (`idQuest`,`idAnswer`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				$query = "ALTER TABLE `learning_testquestanswer` 
				CHANGE `isCorrect` `is_correct` INT( 11 ) NOT NULL DEFAULT '0',
				CHANGE `pointAdd` `score_correct` DOUBLE NOT NULL DEFAULT '0',
				CHANGE `pointToggle` `score_incorrect` DOUBLE NOT NULL DEFAULT '0';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				
				$query = "ALTER TABLE `learning_testquestanswer` ADD `sequence` INT( 11 ) NOT NULL AFTER `idQuest` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 9);
				
				$query = "CREATE TABLE `learning_testquestanswer_associate` (
				  `idAnswer` int(11) NOT NULL auto_increment,
				  `idQuest` int(11) NOT NULL default '0',
				  `answer` text NOT NULL,
				  PRIMARY KEY  (`idAnswer`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 10);
				
				$query = "ALTER TABLE `learning_testtrack` 
				CHANGE `idUser` `idUser` INT( 11 ) NOT NULL DEFAULT '0', 
				CHANGE `data_attempt` `date_attempt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
				CHANGE `result` `score` DOUBLE NULL ,
				
				ADD `date_attempt_mod` DATETIME NULL DEFAULT NULL AFTER `date_attempt` ,
				ADD `date_end_attempt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `date_attempt_mod` ,
				ADD `last_page_seen` INT( 11 ) NOT NULL DEFAULT '0' AFTER `date_end_attempt` ,
				ADD `last_page_saved` INT( 11 ) NOT NULL DEFAULT '0' AFTER `last_page_seen` ,
				ADD `number_of_save` INT( 11 ) NOT NULL DEFAULT '0' AFTER `last_page_saved` ,
				ADD `bonus_score` DOUBLE NOT NULL DEFAULT '0' AFTER `score` ,
				ADD `score_status` ENUM( 'valid', 'not_checked', 'not_passed', 'passed', 'not_complete', 'doing' ) NOT NULL DEFAULT 'not_complete' AFTER `bonus_score` ,
				ADD `comment` TEXT NOT NULL AFTER `score_status` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 11);
				
				$query = "ALTER TABLE `learning_testtrack` DROP `type_of_result` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 12);
				
				$query = "ALTER TABLE `learning_testtrackanswer` RENAME `learning_testtrack_answer` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 13);
				
				$query = "ALTER TABLE `learning_testtrack_answer` ADD `score_assigned` DOUBLE NOT NULL ,
				ADD `more_info` LONGTEXT NOT NULL ,
				ADD `manual_assigned` TINYINT( 1 ) NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 14);
				
				$query = "CREATE TABLE `learning_testtrack_page` (
				  `idTrack` int(11) NOT NULL default '0',
				  `page` int(3) NOT NULL default '0',
				  `display_from` datetime default NULL,
				  `display_to` datetime default NULL,
				  `accumulated` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idTrack`,`page`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 15);
				
				$query = "CREATE TABLE `learning_testtrack_quest` (
				  `idTrack` int(11) NOT NULL default '0',
				  `idQuest` int(11) NOT NULL default '0',
				  `page` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idTrack`,`idQuest`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 16);
				
				$query = "ALTER TABLE `learning_testpollcategory` RENAME `learning_quest_category` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 17);
				
				$query = "UPDATE `learning_testquest` SET page = 1";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 18);

				$this->end_version = '3.0';
				return true;
			};break;
			case "3.5.0.2" : {
				$i = 0;
				
				$content = "CREATE TABLE `learning_testtrack_times` (
				  `idTrack` int(11) NOT NULL default '0',
				  `idReference` int(11) NOT NULL default '0',
				  `idTest` int(11) NOT NULL default '0',
				  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
				  `number_time` tinyint(4) NOT NULL default '0',
				  `score` double NOT NULL default '0',
				  `score_status` varchar(50) NOT NULL default '',
				  PRIMARY KEY  (`idTrack`,`number_time`,`idTest`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "ALTER TABLE `learning_test` ADD `max_attempt` INT( 11 ) NOT NULL ;";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "ALTER TABLE `learning_testtrack` ADD `number_of_attempt` INT( 11 ) NOT NULL AFTER `number_of_save` ;";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$this->end_version = '3.5.0.3';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `learning_quest_category` ADD `author` INT( 11 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_test` ADD `hide_info` TINYINT( 1 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "ALTER TABLE `learning_testquest` ADD `shuffle` TINYINT( 1 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
			
		}
		return true;
	}
}

?>