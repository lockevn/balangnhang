<?php

class Upgrade_Event extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'event';
	
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
				
				$query = "CREATE TABLE `core_event` (
				  `idEvent` int(11) NOT NULL auto_increment,
				  `idClass` int(11) NOT NULL default '0',
				  `module` varchar(50) NOT NULL default '',
				  `section` varchar(50) NOT NULL default '',
				  `priority` smallint(1) unsigned NOT NULL default '1289',
				  `description` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idEvent`),
				  KEY `idClass` (`idClass`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `core_event_class` (
				  `idClass` int(11) NOT NULL auto_increment,
				  `class` varchar(50) NOT NULL default '',
				  `platform` varchar(50) NOT NULL default '',
				  `description` varchar(255) default NULL,
				  PRIMARY KEY  (`idClass`),
				  UNIQUE KEY `class_2` (`class`),
				  KEY `class` (`class`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "INSERT INTO `core_event_class` VALUES (1, 'UserNew', 'framework', '_EVENT_USERNEW');
				INSERT INTO `core_event_class` VALUES (2, 'UserMod', 'framework', '_EVENT_USERMOD');
				INSERT INTO `core_event_class` VALUES (3, 'UserDel', 'framework', '_EVENT_USERDEL');
				INSERT INTO `core_event_class` VALUES (4, 'UserNewModerated', 'framework', '_EVENT_USERNEWMODERATED');
				INSERT INTO `core_event_class` VALUES (5, 'UserGroupModerated', 'framework', '_EVENT_USERGROUPMODERATED');
				INSERT INTO `core_event_class` VALUES (6, 'UserGroupInsert', 'framework', '_EVENT_USERGROUPINSERTED');
				INSERT INTO `core_event_class` VALUES (7, 'UserGroupRemove', 'framework', '_EVENT_USERGROUPREMOVED');
				INSERT INTO `core_event_class` VALUES (8, 'UserCourseInsertModerate', 'lms-a', '_EVENT_USERCOURSEINSERTMODEATE');
				INSERT INTO `core_event_class` VALUES (9, 'UserCourseInserted', 'lms-a', '_EVENT_USERCOURSEINSERTED');
				INSERT INTO `core_event_class` VALUES (10, 'UserCourseRemoved', 'lms-a', '_EVENT_USERCOURSEREMOVED');
				INSERT INTO `core_event_class` VALUES (11, 'UserCourseLevelChanged', 'lms-a', '_EVENT_USERCOURSELEVELCHANGED');
				INSERT INTO `core_event_class` VALUES (12, 'UserCourseEnded', 'lms-a', '_EVENT_USERCOURSEENDED');
				INSERT INTO `core_event_class` VALUES (13, 'CoursePorpModified', 'lms-a', '_EVENT_COURSEPROPMODIFIED');
				INSERT INTO `core_event_class` VALUES (14, 'AdviceNew', 'lms', '_EVENT_ADVICENEW');
				INSERT INTO `core_event_class` VALUES (15, 'MsgNewReceived', 'lms', '_EVENT_MSGNEWRECEIVED');
				INSERT INTO `core_event_class` VALUES (16, 'ForumNewCategory', 'lms', '_EVENT_FORUMNEWCATEGORY');
				INSERT INTO `core_event_class` VALUES (17, 'ForumNewThread', 'lms', '_EVENT_FORUMNEWTHERAD');
				INSERT INTO `core_event_class` VALUES (18, 'ForumNewResponse', 'lms', '_EVENT_FORUMNEWRESPONSE');
				INSERT INTO `core_event_class` VALUES (19, 'NewsCreated', 'cms-a', '_EVENT_NEWSCREATED');
				INSERT INTO `core_event_class` VALUES (20, 'MediaCreated', 'cms-a', '_EVENT_MEDIACREATED');
				INSERT INTO `core_event_class` VALUES (21, 'DocumentCreated', 'cms-a', '_EVENT_DOCUMENTCREATED');
				INSERT INTO `core_event_class` VALUES (22, 'ContentCreated', 'cms-a', '_EVENT_CONTENTCREATED');
				INSERT INTO `core_event_class` VALUES (23, 'PageCreated', 'cms-a', '_EVENT_PAGECREATED');
				INSERT INTO `core_event_class` VALUES (24, 'NewsModified', 'cms-a', '_EVENT_NEWSMODIFIED');
				INSERT INTO `core_event_class` VALUES (25, 'MediaModified', 'cms-a', '_EVENT_MEDIAMODIFIED');
				INSERT INTO `core_event_class` VALUES (26, 'DocumentModified', 'cms-a', '_EVENT_DOCUMENTMODIFIED');
				INSERT INTO `core_event_class` VALUES (27, 'ContentModified', 'cms-a', '_EVENT_CONTENTMODIFIED');
				INSERT INTO `core_event_class` VALUES (28, 'PageModified', 'cms-a', '_EVENT_PAGEMODIFIED');
				INSERT INTO `core_event_class` VALUES (29, 'FlowApprovation', 'cms-a', '_EVENT_FLOWAPPROVATION');
				INSERT INTO `core_event_class` VALUES (30, 'NewletterReceived', 'cms-a', '_EVENT_NEWSLETTERRECEIVED');
				INSERT INTO `core_event_class` VALUES (31, 'CmsForumNewCategory', 'cms', '_EVENT_CMSFORUMNEWCATEGORY');
				INSERT INTO `core_event_class` VALUES (32, 'CmsForumNewThread', 'cms', '_EVENT_CMSFORUMNEWTHREAD');
				INSERT INTO `core_event_class` VALUES (33, 'CmsForumNewResponse', 'cms', '_EVENT_CMSFORUMNEWRESPONSE');
				INSERT INTO `core_event_class` VALUES (34, 'KmsDocumentCreated', 'kms', '_EVENT_KMSDOCUMENTCREATED');
				INSERT INTO `core_event_class` VALUES (35, 'KmsDocumentModified', 'kms', '_EVENT_KMSDOCUMENTMODIFIED');
				INSERT INTO `core_event_class` VALUES (36, 'KmsDocumentCommented', 'kms', '_EVENT_KMSDOCUMENTCOMMENTED');
				INSERT INTO `core_event_class` VALUES (37, 'KmsFlowOperation', 'kms', '_EVENT_KMSFLOWOPERATION');
				INSERT INTO `core_event_class` VALUES (38, 'UserApproved', 'framework', '_EVENT_USERAPPROVED');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "CREATE TABLE `core_event_consumer` (
				  `idConsumer` int(11) NOT NULL auto_increment,
				  `consumer_class` varchar(50) NOT NULL default '',
				  `consumer_file` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`idConsumer`),
				  UNIQUE KEY `consumer_class` (`consumer_class`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "INSERT INTO `core_event_consumer` VALUES (1, 'DoceboUserNotifier', '/lib/lib.usernotifier.php');
				INSERT INTO `core_event_consumer` VALUES (2, 'DoceboCourseNotifier', '/lib/lib.coursenotifier.php');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "CREATE TABLE `core_event_consumer_class` (
				  `idConsumer` int(11) NOT NULL default '0',
				  `idClass` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idConsumer`,`idClass`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$query = "INSERT INTO `core_event_consumer_class` VALUES (1, 1);
				INSERT INTO `core_event_consumer_class` VALUES (1, 2);
				INSERT INTO `core_event_consumer_class` VALUES (1, 3);
				INSERT INTO `core_event_consumer_class` VALUES (1, 4);
				INSERT INTO `core_event_consumer_class` VALUES (1, 5);
				INSERT INTO `core_event_consumer_class` VALUES (1, 6);
				INSERT INTO `core_event_consumer_class` VALUES (1, 7);
				INSERT INTO `core_event_consumer_class` VALUES (1, 8);
				INSERT INTO `core_event_consumer_class` VALUES (1, 9);
				INSERT INTO `core_event_consumer_class` VALUES (1, 10);
				INSERT INTO `core_event_consumer_class` VALUES (1, 11);
				INSERT INTO `core_event_consumer_class` VALUES (1, 12);
				INSERT INTO `core_event_consumer_class` VALUES (1, 13);
				INSERT INTO `core_event_consumer_class` VALUES (1, 14);
				INSERT INTO `core_event_consumer_class` VALUES (1, 15);
				INSERT INTO `core_event_consumer_class` VALUES (1, 16);
				INSERT INTO `core_event_consumer_class` VALUES (1, 17);
				INSERT INTO `core_event_consumer_class` VALUES (1, 18);
				INSERT INTO `core_event_consumer_class` VALUES (1, 19);
				INSERT INTO `core_event_consumer_class` VALUES (1, 20);
				INSERT INTO `core_event_consumer_class` VALUES (1, 21);
				INSERT INTO `core_event_consumer_class` VALUES (1, 22);
				INSERT INTO `core_event_consumer_class` VALUES (1, 23);
				INSERT INTO `core_event_consumer_class` VALUES (1, 24);
				INSERT INTO `core_event_consumer_class` VALUES (1, 25);
				INSERT INTO `core_event_consumer_class` VALUES (1, 26);
				INSERT INTO `core_event_consumer_class` VALUES (1, 27);
				INSERT INTO `core_event_consumer_class` VALUES (1, 28);
				INSERT INTO `core_event_consumer_class` VALUES (1, 29);
				INSERT INTO `core_event_consumer_class` VALUES (1, 30);
				INSERT INTO `core_event_consumer_class` VALUES (1, 31);
				INSERT INTO `core_event_consumer_class` VALUES (1, 32);
				INSERT INTO `core_event_consumer_class` VALUES (1, 33);
				INSERT INTO `core_event_consumer_class` VALUES (1, 34);
				INSERT INTO `core_event_consumer_class` VALUES (1, 35);
				INSERT INTO `core_event_consumer_class` VALUES (1, 36);
				INSERT INTO `core_event_consumer_class` VALUES (1, 37);
				INSERT INTO `core_event_consumer_class` VALUES (1, 38);
				INSERT INTO `core_event_consumer_class` VALUES (2, 3);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 7);
				
				
				$query = "CREATE TABLE `core_event_manager` (
				  `idEventMgr` int(11) NOT NULL auto_increment,
				  `idClass` int(11) NOT NULL default '0',
				  `permission` enum('not_used','mandatory','user_selectable') NOT NULL default 'not_used',
				  `channel` set('email','sms') NOT NULL default 'email',
				  `recipients` varchar(255) NOT NULL default '',
				  `show_level` set('godadmin','admin','user') NOT NULL default '',
				  PRIMARY KEY  (`idEventMgr`),
				  UNIQUE KEY `idClass` (`idClass`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 8);
				
				
				$query = "
				INSERT INTO `core_event_manager` VALUES (1, 1, 'mandatory', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (2, 2, 'mandatory', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (3, 3, 'mandatory', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (4, 4, 'user_selectable', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (5, 5, 'user_selectable', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (6, 6, 'user_selectable', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (7, 7, 'user_selectable', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (8, 8, 'user_selectable', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (9, 9, 'user_selectable', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (10, 10, 'user_selectable', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (11, 11, 'user_selectable', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (12, 12, 'user_selectable', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (13, 13, 'user_selectable', 'email', '_EVENT_RECIPIENTS_TEACHER_GOD', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (14, 14, 'user_selectable', 'email', '_EVENT_RECIPIEMTS_COURSEUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (15, 15, 'user_selectable', 'email', '_EVENT_RECIPIEMTS_COURSEUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (16, 16, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (17, 17, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (18, 18, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (19, 19, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (20, 20, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (21, 21, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (22, 22, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (23, 23, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (24, 24, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (25, 25, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (26, 26, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (27, 27, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (28, 28, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (29, 29, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin');
				INSERT INTO `core_event_manager` VALUES (30, 30, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (31, 31, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (32, 32, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (33, 33, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (34, 34, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (35, 35, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (36, 36, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (37, 37, 'user_selectable', 'email', '_EVENT_RECIPIENTS_FLOWMANAGER_OPERATOR', 'godadmin,admin,user');
				INSERT INTO `core_event_manager` VALUES (38, 38, 'mandatory', 'email,sms', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 9);
				
				$query = "CREATE TABLE `core_event_property` (
				  `idEvent` int(11) NOT NULL default '0',
				  `property_name` varchar(50) NOT NULL default '',
				  `property_value` text NOT NULL,
				  PRIMARY KEY  (`idEvent`,`property_name`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 10);
				
				$query = "CREATE TABLE `core_event_user` (
				  `idEventMgr` int(11) NOT NULL default '0',
				  `idst` int(11) NOT NULL default '0',
				  `channel` set('email','sms') NOT NULL default '',
				  PRIMARY KEY  (`idEventMgr`,`idst`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 11);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>