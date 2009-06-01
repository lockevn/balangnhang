<?php

class Upgrade_Forum extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'forum';
	
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
				ALTER TABLE `learning_forum` ADD `emoticons` VARCHAR( 255 ) NOT NULL DEFAULT 'blank.gif';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				ALTER TABLE `learning_forum_access` CHANGE `idGroup` `idMember` INT( 11 ) NOT NULL DEFAULT '0';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `learning_forum_notifier` (
				  `id_notify` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `notify_is_a` enum('forum','thread') NOT NULL default 'forum',
				  PRIMARY KEY  (`id_notify`,`id_user`,`notify_is_a`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "ALTER TABLE `learning_forum_sema` DROP `id` ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "ALTER TABLE `learning_forum_sema` ADD PRIMARY KEY ( `iduser` , `idmsg` , `idsema` ) ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "ALTER TABLE `learning_forummessage` 
				ADD `answer_tree` TEXT NOT NULL AFTER `idCourse`, 
				ADD `generator` TINYINT( 1 ) NOT NULL AFTER `author`,
				ADD `modified_by` INT( 11 ) NOT NULL ,
				ADD `modified_by_on` DATETIME NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$query = "ALTER TABLE `learning_forumthread` ADD `emoticons` VARCHAR( 255 ) NOT NULL DEFAULT 'blank.gif';";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				$old_path = '/addons/fckeditor2rc2/editor/images/smiley/msn/';
				$new_path = '../doceboCore/addons/fckeditor/editor/images/smiley/msn/';
				
				$query = "UPDATE learning_forummessage
				SET textof = REPLACE(textof, '$old_path', '$new_path')";
				$this->db_man->query($query);
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "ALTER TABLE `learning_forumthread` ADD `rilevantForum` TINYINT( 1 ) DEFAULT '0' NOT NULL";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
		}
		return true;
	}
}

?>