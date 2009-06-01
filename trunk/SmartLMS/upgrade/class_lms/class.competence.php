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

class Upgrade_LmsCompetence extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'competence';
	
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
				
				$content = "CREATE TABLE `learning_competence` (
						  `id_competence` int(10) unsigned NOT NULL auto_increment,
						  `id_category` int(10) unsigned NOT NULL default '0',
						  `type` enum('score','flag') NOT NULL default 'score',
						  `score` float NOT NULL default '0',
						  `competence_type` enum('skill','attitude','_unknown') NOT NULL default 'skill',
						  `score_min` float NOT NULL default '0',
						  PRIMARY KEY  (`id_competence`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_category` (
						  `id_competence_category` int(10) unsigned NOT NULL auto_increment,
						  PRIMARY KEY  (`id_competence_category`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_category_text` (
						  `id_category` int(10) unsigned NOT NULL default '0',
						  `id_text` int(10) unsigned NOT NULL auto_increment,
						  `lang_code` varchar(255) NOT NULL,
						  `text_name` varchar(255) NOT NULL,
						  `text_desc` text,
						  PRIMARY KEY  (`id_text`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_course` (
						  `id_competence` int(10) unsigned NOT NULL default '0',
						  `id_course` int(10) unsigned NOT NULL default '0',
						  `score` float NOT NULL default '0',
						  PRIMARY KEY  (`id_competence`,`id_course`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_required` (
						  `id_competence` int(10) unsigned NOT NULL default '0',
						  `idst` int(10) unsigned NOT NULL default '0',
						  `type_of` varchar(255) NOT NULL,
						  PRIMARY KEY  (`id_competence`,`idst`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_text` (
						  `id_competence` int(10) unsigned NOT NULL default '0',
						  `id_text` int(10) unsigned NOT NULL auto_increment,
						  `lang_code` varchar(255) NOT NULL,
						  `text_name` varchar(255) NOT NULL,
						  `text_desc` text,
						  PRIMARY KEY  (`id_text`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_track` (
						  `id_track` int(10) unsigned NOT NULL auto_increment,
						  `id_competence` int(10) unsigned NOT NULL default '0',
						  `id_user` int(10) unsigned NOT NULL default '0',
						  `source` varchar(255) NOT NULL default '',
						  `date_assignment` datetime NOT NULL default '0000-00-00 00:00:00',
						  `score` float NOT NULL default '0',
						  PRIMARY KEY  (`id_track`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_competence_user` (
						  `id_competence` int(10) unsigned NOT NULL default '0',
						  `id_user` int(10) unsigned NOT NULL default '0',
						  `score_init` float NOT NULL default '0',
						  `score_got` float NOT NULL default '0',
						  PRIMARY KEY  (`id_competence`,`id_user`)
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