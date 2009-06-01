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

class Upgrade_LmsAssessment extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'assessment';
	
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
				CREATE TABLE `learning_assessment_rules` (
				  `id_rule` int(11) NOT NULL auto_increment,
				  `id_assessment` int(11) NOT NULL default '0',
				  `rule_type` varchar(255) NOT NULL default '',
				  `rule_setting` varchar(255) NOT NULL default '',
				  `rule_effect` text NOT NULL,
				  `rule_casualities` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`id_rule`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "
				CREATE TABLE `learning_assessment_user` (
				  `id_assessment` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `type_of` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id_assessment`,`id_user`)
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