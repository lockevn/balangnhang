<?php

class Upgrade_Directory extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'directory';
	
	
	function _insert_org_chart($son_of_id, $dir_name) {
		
		if($son_of_id == 0) {
			
			$folder['id'] = 0;
			$folder['parent'] = '/root';
			$folder['level'] = 0;
		} else {
			
			$query = "
			SELECT idOrg, path, lev
			FROM core_org_chart_tree 
			WHERE idOrg = '$son_of_id'";
			list($folder['id'], $folder['parent'], $folder['level']) = $this->db_man->fetchRow($this->db_man->query($query));
		}
		
		$query = "
		SELECT DISTINCT path 
		FROM core_org_chart_tree 
		WHERE path LIKE  '".$folder['parent']. "%' 
		ORDER BY path DESC
		LIMIT 0,1";
		list($folder_name) = $this->db_man->fetchRow($this->db_man->query($query));
		if($folder_name != '') {
			
			$pathTokens = explode( '/', $folder_name ); 
			// verify level
			if( count($pathTokens) == ($folder['level'] + 3) ) {
				
				$last = array_pop($pathTokens);
				$last = substr('0000000'.($last + 1) , -8) ;
				
				$folder_name = implode('/', $pathTokens).'/'.$last;
			} else {
				$folder_name = $folder['parent'].'/00000001';
			}
		} else {
			$folder_name = '/root/00000001';
		}
		
		$query = "
		INSERT INTO core_org_chart_tree " 
		." ( idOrg, idParent, path, lev ) "
		."VALUES ( '', '".$folder['id']."', '".$folder_name."' ,'".($folder['level'] + 1)."' );";
		$this->db_man->query($query);
		$query = "
		SELECT LAST_INSERT_ID()";
		list($id_dir) = $this->db_man->fetchRow($this->db_man->query($query));
		
		$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/ocd_".$son_of_id."'";
		list($idst_parent) = $this->db_man->fetchRow($this->db_man->query($query));
		
		$query = "
		INSERT INTO core_st ( idst ) VALUES ( '' );
		INSERT INTO core_group "
		." (idst, groupid, description, hidden ) "
		."VALUES ( LAST_INSERT_ID(), '/oc_$id_dir', ' ' ,'true' );";
		$this->db_man->query($query);
		
		$query = "
		INSERT INTO core_st ( idst ) VALUES ( '' );
		INSERT INTO core_group " 
		." (idst, groupid, description, hidden ) "
		."VALUES ( LAST_INSERT_ID(), '/ocd_$id_dir', ' ' ,'true' );";
		$this->db_man->query($query);
		$query = "
		SELECT LAST_INSERT_ID()";
		list($idSTD) = $this->db_man->fetchRow($this->db_man->query($query));
		
		$query = "INSERT INTO core_org_chart "
					."( id_dir, lang_code, translation ) "
					."VALUES ('".$id_dir."','".DoceboUpgradeGui::getLanguage()."','".addslashes($dir_name)."')";
		$this->db_man->query($query);
		
		return $id_dir;
	}
	
	function _add_to_orgchart($id_user, $id) {
		
		$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/oc_".$id."'";
		list($idst) = $this->db_man->fetchRow($this->db_man->query($query));
		
		$query = "INSERT INTO core_group_members "
					."( idst, idstMember ) "
					."VALUES ( '".$idst."','".$id_user."' )";
		$this->db_man->query($query);
		
		$query = "SELECT idst "
				." FROM core_group "
				." WHERE groupid = '/ocd_".$id."'";
		list($idst) = $this->db_man->fetchRow($this->db_man->query($query));
		
		$query = "INSERT INTO core_group_members "
					."( idst, idstMember ) "
					."VALUES ( '".$idst."','".$id_user."' )";
		$this->db_man->query($query);
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
			case "2.0.4" : {
				
				$i = 1;
				
				list($number_of_user) = $this->db_man->fetchRow($this->db_man->query("
				SELECT MAX(idUser) + 1
				FROM learning_user"));
				
				list($number_of_group) = $this->db_man->fetchRow($this->db_man->query("
				SELECT MAX(idGroup) + 1
				FROM learning_group"));
				// create st
				
				$query = "CREATE TABLE `core_st` (
				  `idst` int(11) unsigned NOT NULL auto_increment,
				  PRIMARY KEY  (`idst`)
				) TYPE=MyISAM AUTO_INCREMENT=".($number_of_user + $number_of_group) ;
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// group transform
				$query = "
				ALTER TABLE `learning_group` RENAME `core_group`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				ALTER TABLE `core_group` 
				CHANGE `idGroup` `idst` INT( 11 ) NOT NULL, 
				CHANGE `name` `groupid` VARCHAR( 255 ) NOT NULL, 
				CHANGE `description` `description` TEXT NOT NULL ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				ALTER TABLE `core_group` 
				ADD `hidden` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER `description`,
				ADD `type` ENUM( 'free', 'moderate', 'private', 'invisible', 'course' ) NOT NULL DEFAULT 'free' AFTER `hidden`,
				ADD `show_on_platform` TEXT NOT NULL AFTER `type`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "ALTER TABLE `core_group` ADD UNIQUE (`groupid`)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_group_fields` (
				  `idst` int(11) NOT NULL default '0',
				  `id_field` int(11) NOT NULL default '0',
				  `mandatory` enum('true','false') NOT NULL default 'false',
				  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
				  PRIMARY KEY  (`idst`,`id_field`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_group_user_waiting` (
				  `idst_group` int(11) NOT NULL default '0',
				  `idst_user` int(11) NOT NULL default '0',
				  PRIMARY KEY  (`idst_group`,`idst_user`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_pwd_recover` (
				  `idst_user` int(11) NOT NULL default '0',
				  `random_code` varchar(255) NOT NULL default '',
				  `request_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`idst_user`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_user_file` (
				  `id` int(11) NOT NULL auto_increment,
				  `user_idst` int(11) NOT NULL default '0',
				  `type` varchar(20) NOT NULL default '',
				  `fname` varchar(255) NOT NULL default '',
				  `real_fname` varchar(255) NOT NULL default '',
				  `size` int(11) NOT NULL default '0',
				  `uldate` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_user_log_attempt` (
				  `id` int(11) NOT NULL auto_increment,
				  `userid` varchar(255) NOT NULL default '',
				  `attempt_at` datetime NOT NULL default '0000-00-00 00:00:00',
				  `attempt_number` int(5) NOT NULL default '0',
				  `user_ip` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_user_temp` (
				  `idst` int(11) NOT NULL default '0',
				  `userid` varchar(255) NOT NULL default '',
				  `firstname` varchar(100) NOT NULL default '',
				  `lastname` varchar(100) NOT NULL default '',
				  `pass` varchar(50) NOT NULL default '',
				  `email` varchar(255) NOT NULL default '',
				  `language` varchar(50) NOT NULL default '',
				  `request_on` datetime default '0000-00-00 00:00:00',
				  `random_code` varchar(255) NOT NULL default '',
				  `create_by_admin` int(11) NOT NULL default '0',
				  `confirmed` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`idst`),
				  UNIQUE KEY `userid` (`userid`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				UPDATE `core_group` 
				SET `idst` = idst + ".$number_of_user.",
					type = 'private'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				UPDATE `learning_groupuser` 
				SET `idGroup` = idGroup + ".$number_of_user."";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// table for group
				
				$query = "
				ALTER TABLE `learning_groupuser` RENAME `core_group_members`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				ALTER TABLE `core_group_members` 
				CHANGE `idGroup` `idst` INT( 11 ) NOT NULL,
				CHANGE `idUser` `idstMember` INT( 11 ) NOT NULL, 
				ADD `filter` VARCHAR( 50 ) NOT NULL AFTER `idstMember`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "ALTER TABLE `core_group_members` DROP PRIMARY KEY ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				ALTER TABLE `core_group_members` ADD UNIQUE (
				`idst` ,
				`idstMember`
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "ALTER TABLE `core_group_members` ADD INDEX ( `idst` ) ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "ALTER TABLE `core_group_members` ADD INDEX ( `filter` ) ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				UPDATE `learning_forum_access` 
				SET `idGroup` = idGroup + ".$number_of_user."";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				
				// root tree
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), 0x2f6f635f30, 'Root of organization chart', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				SELECT LAST_INSERT_ID()";
				if(!$re_id = $this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				list($idst_all) = $this->db_man->fetchRow($re_id);
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), 0x2f6f63645f30, 'Root of organization chart and descendants', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$query = "
				SELECT LAST_INSERT_ID()";
				if(!$re_id = $this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				list($idst_all_d) = $this->db_man->fetchRow($re_id);
				
				// god admin
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), 0x2f6672616d65776f726b2f6c6576656c2f676f6461646d696e, 'Group of godadmins', 'true', 'free', 'framework, ');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				SELECT LAST_INSERT_ID()";
				if(!$re_id = $this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				list($idst_godadmin) = $this->db_man->fetchRow($re_id);
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), 0x2f6672616d65776f726b2f6c6576656c2f75736572, 'Group of normal users', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				$query = "
				SELECT LAST_INSERT_ID()";
				if(!$re_id = $this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				list($idst_user) = $this->db_man->fetchRow($re_id);
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), 0x2f6672616d65776f726b2f6c6576656c2f61646d696e, 'Group of administrators', 'true', 'free', 'framework, ');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				$query = "
				SELECT LAST_INSERT_ID()";
				if(!$re_id = $this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				list($idst_admin) = $this->db_man->fetchRow($re_id);
				
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_group` VALUES (LAST_INSERT_ID(), 0x2f6672616d65776f726b2f6f726763686172742f6669656c6473, 'Used for orgchart field assignement', 'true', 'free', 'framework,');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// role
				$query = "
				CREATE TABLE `core_role` (
				  `idst` int(11) NOT NULL default '0',
				  `roleid` varchar(255) NOT NULL default '',
				  `description` varchar(255) default NULL,
				  PRIMARY KEY  (`idst`),
				  KEY `roleid` (`roleid`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_role_members` (
				  `idst` int(11) NOT NULL default '0',
				  `idstMember` int(11) NOT NULL default '0',
				  UNIQUE KEY `idst` (`idst`,`idstMember`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// create base role
				$query = "
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/admin_manager/view', 'Can operate with admin configuration');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/configuration/view', 'Configuration module');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/approve_waiting_user', 'Approve waiting user');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/associate_group', 'Associate users to groups');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/creategroup', 'Group insert');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/createuser_org_chart', 'User insert');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/delgroup', 'Group remove');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/deluser_org_chart', 'User remove');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/editgroup', 'Edit group');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/edituser_org_chart', 'Edit user');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/view', 'View user managment');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/view_group', 'View groups list');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/view_org_chart', 'You can see the organization_chart module');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/directory/view_user', 'View users list');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/event_manager/view_event_manager', 'Show event managment');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/field_manager/add', 'You can add custom field');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/field_manager/del', 'You can add remove field');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/field_manager/mod', 'You can add modify field');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/field_manager/view', 'You can see the custom field');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/lang/importexport', 'Import language');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/lang/view', 'You can use the language module');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/lang/view_org_chart', 'You can see the org chart');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/newsletter/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/publication_flow/view', 'You can see the pubblication flow');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/framework/admin/regional_settings/view', 'You can see the regional setting');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/scs/admin/admin_configuration/view', 'You can modify the confiuration');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/scs/admin/room/mod', 'You can operate with room');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/scs/admin/room/view', 'You can see the rooms');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/kms/admin/news/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/kms/admin/news/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/kms/admin/webpages/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/kms/admin/webpages/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/banners/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/banners/cat_view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/banners/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/banners/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/banners/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/content/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/content/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/content/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' ); 
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/content/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' ); 
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/docs/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' ); 
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/docs/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' ); 
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/docs/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' ); 	
				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/docs/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/form/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/form/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/form/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/form/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/forum/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/forum/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/forum/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/forum/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/links/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/links/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/links/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/links/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/manpage/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/manpage/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/manpage/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/manpage/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/mantopic/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/mantopic/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/mantopic/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/mantopic/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/media/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/media/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/media/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/media/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/news/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/news/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/news/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/news/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/poll/add', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/poll/del', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/poll/mod', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/poll/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				INSERT INTO core_st ( idst ) VALUES ( '' );
 				INSERT INTO `core_role` VALUES (LAST_INSERT_ID(), '/cms/admin/stats/view', '');
				INSERT INTO core_role_members ( idst, idstMember ) VALUES ( LAST_INSERT_ID(), '$idst_godadmin' );
				
				";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				// users
				
				$query = "
				ALTER TABLE `learning_user` RENAME `core_user`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				ALTER TABLE `core_user` 
				CHANGE `idUser` `idst` INT( 11 ) NOT NULL,
				CHANGE `userid` `userid` VARCHAR( 255 ) NOT NULL,  
				CHANGE `name` `firstname` VARCHAR( 255 ) NOT NULL, 
				CHANGE `surname` `lastname` VARCHAR( 255 ) NOT NULL ";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				
				$query = "
				UPDATE `core_user` 
				SET `userid` = CONCAT('/' , `userid`)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// add user to appropiate level
				
				$query = "
				SELECT idst 
				FROM `core_user` 
				WHERE level = '8'";
				$re_god = $this->db_man->query($query);
				while(list($id_user) = $this->db_man->fetchRow($re_god)) {
					
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_godadmin', '$id_user' )");
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_all', '$id_user' )");
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_all_d', '$id_user' )");
				}
				
				$query = "
				SELECT idst 
				FROM `core_user` 
				WHERE level = '7'";
				$re_admin = $this->db_man->query($query);
				while(list($id_user) = $this->db_man->fetchRow($re_admin)) {
					
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_admin', '$id_user' )");
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_all', '$id_user' )");
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_all_d', '$id_user' )");
				}
				$query = "
				SELECT idst 
				FROM `core_user` 
				WHERE level <= '6'";
				$re_user = $this->db_man->query($query);
				while(list($id_user) = $this->db_man->fetchRow($re_user)) {
					
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_user', '$id_user' )");
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_all', '$id_user' )");
					$this->db_man->query("INSERT INTO core_group_members ( idst, idstMember ) VALUES ( '$idst_all_d', '$id_user' )");
				}
				
				// create org_chart
				$query = "
				CREATE TABLE `core_org_chart` (
				  `id_dir` int(11) NOT NULL default '0',
				  `lang_code` varchar(50) NOT NULL default '',
				  `translation` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id_dir`,`lang_code`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_org_chart_field` (
				  `idst` int(11) NOT NULL default '0',
				  `id_field` varchar(11) NOT NULL default '0',
				  `mandatory` enum('true','false') NOT NULL default 'false',
				  `useraccess` enum('readonly','readwrite','noaccess') NOT NULL default 'readonly',
				  PRIMARY KEY  (`idst`,`id_field`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_org_chart_fieldentry` (
				  `id_common` varchar(11) NOT NULL default '',
				  `id_common_son` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0',
				  `user_entry` text NOT NULL,
				  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);	
				$i++;	
				
				$query = "
				CREATE TABLE `core_org_chart_tree` (
				  `idOrg` int(11) NOT NULL auto_increment,
				  `idParent` int(11) NOT NULL default '0',
				  `path` text NOT NULL,
				  `lev` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`idOrg`)
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_org_chart_user` (
				  `id_org` int(11) NOT NULL default '0',
				  `id_user` int(11) NOT NULL default '0'
				) TYPE=MyISAM";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// recovering field and create field for catalogation
				$re_fields = $this->db_man->query("
				SELECT idLabel_one, name 
				FROM learning_user_labelone");
				while(list($id_l, $name) = $this->db_man->fetchRow($re_fields)) {
					
					$id_inserted = $this->_insert_org_chart(0, $name);
					$labels_one[$id_l] = $id_inserted;
				}
				$re_fields = $this->db_man->query("
				SELECT idLabel_two, idLabel_one, name 
				FROM learning_user_labeltwo");
				while(list($id_l, $id_one, $name) = $this->db_man->fetchRow($re_fields)) {
					
					$id_inserted = $this->_insert_org_chart($labels_one[$id_one], $name);
					$labels_two[$id_l] = $id_inserted;
				}
				$re_fields = $this->db_man->query("
				SELECT idLabel_three, idLabel_two, name 
				FROM learning_user_labelthree");
				while(list($id_l, $id_two, $name) = $this->db_man->fetchRow($re_fields)) {
					
					$id_inserted = $this->_insert_org_chart($labels_two[$id_two], $name);
					$labels_three[$id_l] = $id_inserted;
				}
				
				$query = "CREATE TABLE `core_setting_user` (
				  `path_name` varchar(255) NOT NULL default '',
				  `id_user` int(11) NOT NULL default '0',
				  `value` text NOT NULL,
				  PRIMARY KEY  (`path_name`,`id_user`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// assign user to org chart and save language
				$re_fields = $this->db_man->query("
				SELECT idst, `label_one`, `label_two`, `label_three`, language
				FROM core_user");
				while(list($id_user, $id_one, $id_two, $id_three, $language) = $this->db_man->fetchRow($re_fields)) {
					
					$id = 0;
					if($id_three > 0 ) $id = $labels_three[$id_three];
					elseif($id_two > 0 ) $id = $labels_two[$id_two];
					elseif($id_one > 0 ) $id = $labels_one[$id_one];
					
					if($id != 0) {
						
						$this->_add_to_orgchart($id_user, $id);
					}
					$query_user = "
					INSERT INTO core_setting_user ( path_name, id_user, value ) VALUES (
						'ui.language', 
						'".$id_user."', 
						'".$language."' 
					)";
					$this->db_man->query($query_user);
				}
				
				// delete old field
				$query = "
				ALTER TABLE `core_user` 
				DROP `label_one`,
				DROP `label_two`, 
				DROP `label_three`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				DROP TABLE `learning_user_labelone`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				DROP TABLE `learning_user_labeltwo`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				DROP TABLE `learning_user_labelthree`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				CREATE TABLE `core_admin_tree` (
				  `idst` varchar(11) NOT NULL default '',
				  `idstAdmin` varchar(11) NOT NULL default '',
				  PRIMARY KEY  (`idst`,`idstAdmin`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				DROP TABLE `learning_user_temp`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$query = "
				DROP TABLE `learning_userlost`";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "ALTER TABLE `core_user` ADD `register_date` DATETIME NOT NULL ;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "ALTER TABLE `core_user`
							  DROP `templatename`,
							  DROP `language`;";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "ALTER TABLE `core_event_property` ADD `property_date` DATE DEFAULT '0000-00-00' NOT NULL";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
		}
		return true;
	}
}

?>