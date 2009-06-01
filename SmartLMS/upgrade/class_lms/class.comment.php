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

class Upgrade_LmsComment extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'comment';
	
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
				
				$content = "CREATE TABLE `learning_comment_ajax` (
				  `id_comment` int(11) NOT NULL auto_increment,
				  `resource_type` varchar(50) NOT NULL default '',
				  `external_key` varchar(200) NOT NULL default '',
				  `id_author` int(11) NOT NULL default '0',
				  `posted_on` datetime NOT NULL default '0000-00-00 00:00:00',
				  `textof` text NOT NULL,
				  `history_tree` varchar(255) NOT NULL default '',
				  `id_parent` int(11) NOT NULL default '0',
				  `moderated` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`id_comment`)
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