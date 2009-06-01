<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

class Upgrade_LmsEportfolio extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'eportfolio';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.0.6" : {
				$i = 0;
				
				$content = "		
				CREATE TABLE `learning_eportfolio` (
				  `id_portfolio` int(11) NOT NULL auto_increment,
				  `title` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `custom_pdp_descr` text NOT NULL,
				  `custom_competence_descr` text NOT NULL,
				  PRIMARY KEY  (`id_portfolio`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `learning_eportfolio_competence` (
				  `id_competence` int(11) NOT NULL auto_increment,
				  `id_portfolio` int(11) NOT NULL default '0',
				  `textof` text NOT NULL,
				  `min_score` int(5) NOT NULL default '0',
				  `max_score` int(5) NOT NULL default '0',
				  `sequence` int(5) NOT NULL default '0',
				  `block_competence` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`id_competence`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_competence_invite` (
				  `invited_user` int(11) NOT NULL default '0',
				  `sender` int(11) NOT NULL default '0',
				  `id_portfolio` int(11) NOT NULL default '0',
				  `message_text` text NOT NULL,
				  `refused` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`invited_user`,`sender`,`id_portfolio`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_competence_score` (
				  `id_portfolio` int(11) NOT NULL default '0',
				  `id_competence` int(11) NOT NULL default '0',
				  `estimated_user` int(11) NOT NULL default '0',
				  `from_user` int(11) NOT NULL default '0',
				  `score` int(11) NOT NULL default '0',
				  `comment` text NOT NULL,
				  `status` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`id_portfolio`,`id_competence`,`estimated_user`,`from_user`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_curriculum` (
				  `id_portfolio` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `curriculum_file` varchar(255) NOT NULL default '',
				  `update_date` date NOT NULL default '0000-00-00',
				  PRIMARY KEY  (`id_portfolio`,`id_user`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_member` (
				  `id_portfolio` int(11) NOT NULL default '0',
				  `idst_member` int(11) NOT NULL default '0',
				  `user_is_admin` enum('false','true') NOT NULL default 'false',
				  PRIMARY KEY  (`id_portfolio`,`idst_member`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_pdp` (
				  `id_pdp` int(11) NOT NULL auto_increment,
				  `id_portfolio` int(11) NOT NULL default '0',
				  `textof` text NOT NULL,
				  `allow_answer` enum('true','false') NOT NULL default 'true',
				  `max_answer` int(11) NOT NULL default '0',
				  `answer_mod_for_day` int(11) NOT NULL default '0',
				  `sequence` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_pdp`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_pdp_answer` (
				  `id_answer` int(11) NOT NULL auto_increment,
				  `id_user` int(11) NOT NULL default '0',
				  `id_pdp` int(11) NOT NULL default '0',
				  `textof` text NOT NULL,
				  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_answer`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_presentation` (
				  `id_presentation` int(11) NOT NULL auto_increment,
				  `id_portfolio` int(11) NOT NULL default '0',
				  `title` varchar(255) NOT NULL default '',
				  `textof` text NOT NULL,
				  `owner` int(11) NOT NULL default '0',
				  `show_pdp` tinyint(1) NOT NULL default '0',
				  `show_competence` tinyint(1) NOT NULL default '0',
				  `show_curriculum` tinyint(1) NOT NULL default '0',
				  `pubblication_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_presentation`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_presentation_attach` (
				  `id_presentation` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `id_file` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_presentation`,`id_user`,`id_file`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_eportfolio_presentation_invite` (
				  `id_presentation` int(11) NOT NULL default '0',
				  `recipient_mail` varchar(255) NOT NULL default '',
				  `security_code` varchar(255) NOT NULL default '',
				  `send_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id_presentation`,`recipient_mail`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>