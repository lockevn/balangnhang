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

class Upgrade_LmsCertificate extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'certificate';
	
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
				
				$content = "CREATE TABLE `learning_certificate` (
				  `id_certificate` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL default '',
				  `description` text NOT NULL,
				  `base_language` varchar(255) NOT NULL default '',
				  `cert_structure` text NOT NULL,
				  PRIMARY KEY  (`id_certificate`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_certificate_assign` (
				  `id_certificate` int(11) NOT NULL default '0',
				  `id_course` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `on_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `cert_file` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id_certificate`,`id_course`,`id_user`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_certificate_course` (
				  `id_certificate` int(11) NOT NULL default '0',
				  `id_course` int(11) NOT NULL default '0',
				  `available_for_status` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`id_certificate`,`id_course`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_certificate_tags` (
				  `file_name` varchar(255) NOT NULL default '',
				  `class_name` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`file_name`)
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_certificate_tags` VALUES ('certificate.course.php', 'CertificateSubs_Course');
				INSERT INTO `learning_certificate_tags` VALUES ('certificate.user.php', 'CertificateSubs_User');
				INSERT INTO `learning_certificate_tags` VALUES ('certificate.userstat.php', 'CertificateSubs_UserStat');
				INSERT INTO `learning_certificate_tags` VALUES ('certificate.misc.php', 'CertificateSubs_Misc')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$content = "ALTER TABLE `learning_certificate` ADD `meta` TINYINT( 1 ) NOT NULL DEFAULT '0';";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_certificate_meta` (
						  `idMetaCertificate` int(11) NOT NULL auto_increment,
						  `idCertificate` int(11) NOT NULL default '0',
						  `title` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
						  `description` longtext character set utf8 collate utf8_unicode_ci NOT NULL,
						  PRIMARY KEY  (`idMetaCertificate`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_certificate_meta_assign` (
						  `idUser` int(11) NOT NULL default '0',
						  `idMetaCertificate` int(11) NOT NULL default '0',
						  `idCertificate` int(11) NOT NULL default '0',
						  `on_date` datetime NOT NULL default '0000-00-00 00:00:00',
						  `cert_file` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
						  PRIMARY KEY  (`idUser`,`idMetaCertificate`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "CREATE TABLE `learning_certificate_meta_course` (
						  `id` int(11) NOT NULL auto_increment,
						  `idMetaCertificate` int(11) NOT NULL default '0',
						  `idUser` int(11) NOT NULL default '0',
						  `idCourse` int(11) NOT NULL default '0',
						  `idCourseEdition` int(11) NOT NULL default '0',
						  PRIMARY KEY  (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `learning_menu_under` ( `idUnder` , `idMenu` , `module_name` , `default_name` , `default_op` , `associated_token` , `of_platform` , `sequence` , `class_file` , `class_name` )
							VALUES (
							'', '7', 'meta_certificate', '_META_CERTIFICATE', 'meta_certificate', 'view', NULL , '3', 'class.meta_certificate.php', 'Module_Meta_Certificate'
							);";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			break;
		}
		return true;
	}
}

?>