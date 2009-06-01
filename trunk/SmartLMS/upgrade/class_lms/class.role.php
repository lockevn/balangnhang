<?php

class Upgrade_Role extends Upgrade {
	
	var $platfom = 'lms';
	
	var $mname = 'role';
	
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
				SELECT idst 
				FROM core_group 
				WHERE groupid = '/framework/level/godadmin'";
				list($idst_godadmin) = $this->db_man->fetchRow($this->db_man->query($query));
				$query = "
				
				SELECT idst
				FROM core_group
				WHERE  groupid = '/oc_0'";
				list($idst_all) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/catalogue/mod', 'You can operate with the catalogues');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/catalogue/view', 'You can operate see the catalogues');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/course/add', 'You can add courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/course/del', 'You can remove courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/course/mod', 'You can modify courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/course/moderate', 'Modertae user tocourse subscription');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/course/subscribe', 'You can subscribe users to courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/course/view', 'You can see the courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/coursepath/mod', 'You can operate with the bounded courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/coursepath/moderate', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/coursepath/subscribe', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/coursepath/view', 'You can see the bounded courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/manmenu/mod', 'You can modify the custom menu for courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/manmenu/view', 'You can see the custom menu for courses');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/news/mod', 'You can modify the news');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/news/view', 'You can see the news');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/questcategory/mod', NULL);
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/questcategory/view', NULL);
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/report/view', NULL);
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/webpages/mod', 'You can modify the webpages');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/admin/webpages/view', 'You can see the news');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/course/public/course/view', 'Logged people can see list of subscribed course');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_all' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/course/public/coursecatalogue/view', 'Logged people can see the course catalogue');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_all' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/course/public/mygroup/view', 'View my group management');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_all' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/course/public/profile/mod', 'User can modify the own profile');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_all' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/course/public/profile/view', 'User can see the own profile');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_all' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/lms/course/public/userevent/view', 'Show the module for my alert management');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_all' );";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0';
				return true;
			};break;
			
		}
		return true;
	}
}

?>