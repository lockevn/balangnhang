<?php

class Upgrade_Role extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'role';
	
	function add_roles($roles_to_add, $idst, $start_version) {
		
		while(list(, $roleid) = each($roles_to_add)) {
			
			// role exists ??
			$query = "SELECT idst FROM `core_role` WHERE roleid = '".$roleid."' ";
			$re = $this->db_man->query($query);
			if(mysql_num_rows($re) == 0) {
			
				$query = "INSERT INTO core_st ( idst ) VALUES ( '' );";
				$this->db_man->query($query);
				$id = $this->db_man->lastInsertId();
				
				$query = "INSERT INTO `core_role` VALUES (".$id.", '".$roleid."', NULL);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "INSERT INTO `core_role_members` (`idst`, `idstMember`) VALUES (".$id.", '".$idst."');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
			}
		}
	}
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "3.0.2" : {
				
				$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/framework/level/godadmin' ";
				list($god) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "INSERT INTO core_st ( idst ) VALUES ( '' );";
				$this->db_man->query($query);
				$query = "SELECT LAST_INSERT_ID()";
				list($id_1) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "INSERT INTO core_st ( idst ) VALUES ( '' );";
				$this->db_man->query($query);
				$query = "SELECT LAST_INSERT_ID()";
				list($id_2) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "INSERT INTO `core_role` VALUES (".$id_1.", '/framework/admin/feedreader/view', NULL);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				 $query = "INSERT INTO `core_role` VALUES (".$id_2.", '/framework/admin/dashboard/view', NULL);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$query = "INSERT INTO `core_role_members` VALUES (".$id_1.", ".$god.");";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 5);
				
				$query = "INSERT INTO `core_role_members` VALUES (".$id_2.", ".$god.");";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 6);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.0.6" : {
				
				$roles_to_add = array( '/cms/admin/calendar/add',
					'/cms/admin/calendar/del',
					'/cms/admin/calendar/mod',
					'/cms/admin/calendar/view',
					'/cms/admin/faq/add',
					'/cms/admin/faq/del',
					'/cms/admin/faq/mod',
					'/cms/admin/faq/view',
					'/cms/admin/wiki/add',
					'/cms/admin/wiki/del',
					'/cms/admin/wiki/mod',
					'/cms/admin/wiki/view',
					'/crm/admin/company/add',
					'/crm/admin/company/del',
					'/crm/admin/company/mod',
					'/crm/admin/company/view',
					'/crm/admin/companystatus/mod',
					'/crm/admin/companystatus/view',
					'/crm/admin/companytype/add',
					'/crm/admin/companytype/del',
					'/crm/admin/companytype/mod',
					'/crm/admin/companytype/view',
					'/crm/admin/contactreason/add',
					'/crm/admin/contactreason/del',
					'/crm/admin/contactreason/mod',
					'/crm/admin/contactreason/view',
					'/crm/admin/storedform/view',
					'/crm/admin/ticketstatus/add',
					'/crm/admin/ticketstatus/del',
					'/crm/admin/ticketstatus/mod',
					'/crm/admin/ticketstatus/view',
					'/crm/module/abook/view',
					'/crm/module/company/view',
					'/crm/module/contacthistory/view',
					'/crm/module/project/view',
					'/crm/module/task/view',
					'/crm/module/ticket/view',
					'/crm/module/todo/view',
					'/ecom/admin/bought/mod',
					'/ecom/admin/bought/view',
					'/ecom/admin/payaccount/mod',
					'/ecom/admin/payaccount/view',
					'/ecom/admin/reservation/mod',
					'/ecom/admin/reservation/view',
					'/ecom/admin/taxcatgod/mod',
					'/ecom/admin/taxcatgod/view',
					'/ecom/admin/taxcountry/mod',
					'/ecom/admin/taxcountry/view',
					'/ecom/admin/taxrate/mod',
					'/ecom/admin/taxrate/view',
					'/ecom/admin/taxzone/mod',
					'/ecom/admin/taxzone/view',
					'/ecom/admin/transaction/mod',
					'/ecom/admin/transaction/view',
					'/framework/admin/bugtracker/view',
					'/framework/admin/dashboard/view_event_manager',
					'/framework/admin/iotask/view',
					'/lms/admin/certificate/mod',
					'/lms/admin/certificate/view',
					'/lms/admin/reservation/mod',
					'/lms/admin/reservation/view',
					'/lms/admin/classevent/mod',
					'/lms/admin/classevent/view',
					'/lms/admin/classlocation/mod',
					'/lms/admin/classlocation/view',
					'/lms/admin/classroom/mod',
					'/lms/admin/classroom/view',
					'/lms/admin/eportfolio/mod',
					'/lms/admin/eportfolio/view',
					'/lms/admin/preassessment/mod',
					'/lms/admin/preassessment/subscribe',
					'/lms/admin/preassessment/view',
					'/lms/admin/report_certificate/view',
					'/lms/course/private/calendar/mod',
					'/lms/course/private/calendar/personal',
					'/lms/course/private/calendar/view',
					'/lms/course/private/htmlfront/mod',
					'/lms/course/private/htmlfront/view',
					'/lms/course/private/light_repo/mod',
					'/lms/course/private/light_repo/view',
					'/lms/course/private/newsletter/view',
					'/lms/course/private/teleskill_room/moderate',
					'/lms/course/private/teleskill_room/view',
					'/lms/course/private/wiki/admin',
					'/lms/course/private/wiki/edit',
					'/lms/course/private/wiki/mod',
					'/lms/course/private/wiki/view' );
				$roles_to_add_2 = array(
					'/lms/course/public/eportfolio/view',
					'/lms/course/public/message/send_all',
					'/lms/course/public/message/view',
					'/lms/course/public/mycertificate/view',
					'/lms/course/public/myfiles/view',
					'/lms/course/public/myfriends/view',
					'/lms/course/public/tprofile/view',
					'/lms/course/public/course_autoregistration/view');
				
				$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/framework/level/godadmin' ";
				list($idst_god) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/oc_0' ";
				list($idst_public) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$this->add_roles($roles_to_add, $idst_god, $start_version);
				$this->add_roles($roles_to_add_2, $idst_public, $start_version);
				
				return true;
			};break;
			case "3.5.0.2" : {
				$i = 0;
				
				$admin_roles= array('/cms/admin/banners/add',
							'/cms/admin/banners/cat_view',
							'/cms/admin/banners/del',
							'/cms/admin/banners/mod',
							'/cms/admin/banners/view',
							'/cms/admin/calendar/add',
							'/cms/admin/calendar/del',
							'/cms/admin/calendar/mod',
							'/cms/admin/calendar/view',
							'/cms/admin/content/add',
							'/cms/admin/content/del',
							'/cms/admin/content/mod',
							'/cms/admin/content/view',
							'/cms/admin/docs/add',
							'/cms/admin/docs/del',
							'/cms/admin/docs/mod',
							'/cms/admin/docs/view',
							'/cms/admin/faq/add',
							'/cms/admin/faq/del',
							'/cms/admin/faq/mod',
							'/cms/admin/faq/view',
							'/cms/admin/form/add',
							'/cms/admin/form/del',
							'/cms/admin/form/mod',
							'/cms/admin/form/view',
							'/cms/admin/forum/add',
							'/cms/admin/forum/del',
							'/cms/admin/forum/mod',
							'/cms/admin/forum/view',
							'/cms/admin/links/add',
							'/cms/admin/links/del',
							'/cms/admin/links/mod',
							'/cms/admin/links/view',
							'/cms/admin/manpage/add',
							'/cms/admin/manpage/del',
							'/cms/admin/manpage/mod',
							'/cms/admin/manpage/view',
							'/cms/admin/mantopic/add',
							'/cms/admin/mantopic/del',
							'/cms/admin/mantopic/mod',
							'/cms/admin/mantopic/view',
							'/cms/admin/media/add',
							'/cms/admin/media/del',
							'/cms/admin/media/mod',
							'/cms/admin/media/view',
							'/cms/admin/news/add',
							'/cms/admin/news/del',
							'/cms/admin/news/mod',
							'/cms/admin/news/view',
							'/cms/admin/poll/add',
							'/cms/admin/poll/del',
							'/cms/admin/poll/mod',
							'/cms/admin/poll/view',
							'/cms/admin/simpleprj/view',
							'/cms/admin/stats/view',
							'/cms/admin/wiki/add',
							'/cms/admin/wiki/del',
							'/cms/admin/wiki/mod',
							'/cms/admin/wiki/view',
							'/crm/admin/company/add',
							'/crm/admin/company/del',
							'/crm/admin/company/mod',
							'/crm/admin/company/view',
							'/crm/admin/companystatus/mod',
							'/crm/admin/companystatus/view',
							'/crm/admin/companytype/add',
							'/crm/admin/companytype/del',
							'/crm/admin/companytype/mod',
							'/crm/admin/companytype/view',
							'/crm/admin/contactreason/add',
							'/crm/admin/contactreason/del',
							'/crm/admin/contactreason/mod',
							'/crm/admin/contactreason/view',
							'/crm/admin/storedform/view',
							'/crm/admin/ticketstatus/add',
							'/crm/admin/ticketstatus/del',
							'/crm/admin/ticketstatus/mod',
							'/crm/admin/ticketstatus/view',
							'/ecom/admin/bought/mod',
							'/ecom/admin/bought/view',
							'/ecom/admin/payaccount/mod',
							'/ecom/admin/payaccount/view',
							'/ecom/admin/reservation/mod',
							'/ecom/admin/reservation/view',
							'/ecom/admin/taxcatgod/mod',
							'/ecom/admin/taxcatgod/view',
							'/ecom/admin/taxcountry/mod',
							'/ecom/admin/taxcountry/view',
							'/ecom/admin/taxrate/mod',
							'/ecom/admin/taxrate/view',
							'/ecom/admin/taxzone/mod',
							'/ecom/admin/taxzone/view',
							'/ecom/admin/transaction/mod',
							'/ecom/admin/transaction/view',
							'/framework/admin/admin_manager/view',
							'/framework/admin/bugtracker/view',
							'/framework/admin/configuration/view',
							'/framework/admin/dashboard/view',
							'/framework/admin/dashboard/view_event_manager',
							'/framework/admin/directory/approve_waiting_user',
							'/framework/admin/directory/associate_group',
							'/framework/admin/directory/creategroup',
							'/framework/admin/directory/createuser_org_chart',
							'/framework/admin/directory/delgroup',
							'/framework/admin/directory/deluser_org_chart',
							'/framework/admin/directory/editgroup',
							'/framework/admin/directory/edituser_org_chart',
							'/framework/admin/directory/view',
							'/framework/admin/directory/view_group',
							'/framework/admin/directory/view_org_chart',
							'/framework/admin/directory/view_user',
							'/framework/admin/event_manager/view_event_manager',
							'/framework/admin/feedreader/view',
							'/framework/admin/field_manager/add',
							'/framework/admin/field_manager/del',
							'/framework/admin/field_manager/mod',
							'/framework/admin/field_manager/view',
							'/framework/admin/iotask/view',
							'/framework/admin/lang/importexport',
							'/framework/admin/lang/view',
							'/framework/admin/lang/view_org_chart',
							'/framework/admin/newsletter/view',
							'/framework/admin/publication_flow/view',
							'/framework/admin/regional_settings/view',
							'/kms/admin/news/mod',
							'/kms/admin/news/view',
							'/kms/admin/webpages/mod',
							'/kms/admin/webpages/view',
							'/lms/admin/catalogue/associate',
							'/lms/admin/catalogue/mod',
							'/lms/admin/catalogue/view',
							'/lms/admin/certificate/mod',
							'/lms/admin/certificate/view',
							'/lms/admin/classevent/mod',
							'/lms/admin/classevent/view',
							'/lms/admin/classlocation/mod',
							'/lms/admin/classlocation/view',
							'/lms/admin/classroom/mod',
							'/lms/admin/classroom/view',
							'/lms/admin/course/add',
							'/lms/admin/course/del',
							'/lms/admin/course/mod',
							'/lms/admin/course/moderate',
							'/lms/admin/course/subscribe',
							'/lms/admin/course/view',
							'/lms/admin/coursepath/mod',
							'/lms/admin/coursepath/moderate',
							'/lms/admin/coursepath/subscribe',
							'/lms/admin/coursepath/view',
							'/lms/admin/eportfolio/mod',
							'/lms/admin/eportfolio/view',
							'/lms/admin/internal_news/mod',
							'/lms/admin/internal_news/view',
							'/lms/admin/manmenu/mod',
							'/lms/admin/manmenu/view',
							'/lms/admin/middlearea/view',
							'/lms/admin/news/mod',
							'/lms/admin/news/view',
							'/lms/admin/preassessment/mod',
							'/lms/admin/preassessment/subscribe',
							'/lms/admin/preassessment/view',
							'/lms/admin/questcategory/mod',
							'/lms/admin/questcategory/view',
							'/lms/admin/report/view',
							'/lms/admin/report_certificate/view',
							'/lms/admin/reservation/mod',
							'/lms/admin/reservation/view',
							'/lms/admin/webpages/mod',
							'/lms/admin/webpages/view',
							'/scs/admin/admin_configuration/view',
							'/scs/admin/room/mod',
							'/scs/admin/room/view');
					
				$public_roles = array('/crm/module/abook/view',
					'/crm/module/company/view',
					'/crm/module/contacthistory/view',
					'/crm/module/project/view',
					'/crm/module/task/view',
					'/crm/module/ticket/view',
					'/crm/module/todo/view',
					'/lms/course/public/coursecatalogue/view',
					'/lms/course/public/course_autoregistration/view',
					'/lms/course/public/eportfolio/view',
					'/lms/course/public/message/send_all',
					'/lms/course/public/message/view',
					'/lms/course/public/mycertificate/view',
					'/lms/course/public/myfiles/view',
					'/lms/course/public/myfriends/view',
					'/lms/course/public/mygroup/view',
					'/lms/course/public/profile/mod',
					'/lms/course/public/profile/view',
					'/lms/course/public/public_forum/add',
					'/lms/course/public/public_forum/del',
					'/lms/course/public/public_forum/mod',
					'/lms/course/public/public_forum/moderate',
					'/lms/course/public/public_forum/upload',
					'/lms/course/public/public_forum/view',
					'/lms/course/public/public_forum/write',
					'/lms/course/public/tprofile/view',
					'/lms/course/public/userevent/view');
				
				$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/framework/level/godadmin' ";
				list($idst_god) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/oc_0' ";
				list($idst_public) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$this->add_roles($admin_roles, $idst_god, $start_version);
				$this->add_roles($public_roles, $idst_public, $start_version);
				
				// --------------------------------------------------------------------------
				
				$content = "UPDATE `core_role` 
				SET `roleid` = '/lms/course/private/conference/view' 
				WHERE `roleid` = '/lms/course/private/teleskill_room/view'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				$content = "UPDATE `core_role` 
				SET `roleid` = '/lms/course/private/conference/mod' 
				WHERE `roleid` = '/lms/course/private/teleskill_room/moderate'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				$i++;
				
				return true;
			};break;
			case "3.5.0.4":
				$i = 0;
				
				$query =	"SELECT idst "
							." FROM core_group "
							." WHERE groupid = '/framework/level/godadmin' ";
				
				list($idst_god) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$query =	"SELECT idst "
							." FROM core_group "
							." WHERE groupid = '/oc_0' ";
				
				list($idst_public) = $this->db_man->fetchRow($this->db_man->query($query));
				
				$admin_roles = array(	'/framework/admin/public_admin_manager/view',
										'/lms/course/public/public_user_admin/view_org_chart',
										'/lms/course/public/public_user_admin/createuser_org_chart',
										'/lms/course/public/public_user_admin/edituser_org_chart',
										'/lms/course/public/public_user_admin/deluser_org_chart',
										'/lms/course/public/public_user_admin/approve_waiting_user',
										'/lms/course/public/public_user_admin/view_user',
										'/lms/course/public/public_course_admin/view',
										'/lms/course/public/public_course_admin/add',
										'/lms/course/public/public_course_admin/mod',
										'/lms/course/public/public_course_admin/del',
										'/lms/course/public/public_course_admin/subscribe',
										'/lms/course/public/public_course_admin/moderate',
										'/lms/course/public/public_subscribe_admin/view_org_chart',
										'/lms/course/public/public_subscribe_admin/createuser_org_chart',
										'/lms/course/public/public_subscribe_admin/edituser_org_chart',
										'/lms/course/public/public_subscribe_admin/deluser_org_chart',
										'/lms/course/public/public_subscribe_admin/approve_waiting_user',
										'/lms/course/public/public_report_admin/view',
										'/lms/course/public/public_newsletter_admin/view',
										'/lms/admin/meta_certificate/view',
										'/lms/admin/meta_certificate/mod',
										'/lms/admin/competences/view',
										'/lms/admin/competences/mod',
										'/lms/admin/report/view',
										'/lms/admin/report/mod');
				
				$public_roles = array(	'/lms/course/public/mycompetences/view');
				
				$this->add_roles($admin_roles, $idst_god, $start_version);
				$this->add_roles($public_roles, $idst_public, $start_version);
			break;
		}
		return true;
	}
}

?>