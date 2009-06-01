<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

class MenuManager {
	
	var $acl;
	
	var $acl_man;
	
	function MenuManager() {
		
		$this->acl		=& $GLOBALS['current_user']->getAcl();
		$this->acl_man	=& $GLOBALS['current_user']->getAclManager();
	}
	
	function addPerm($groupid, $roleid) {
		
		$group 		= $this->acl_man->getGroup(false, $groupid);
		$idst_group	= $group[ACL_INFO_IDST];
		$role 		= $this->acl_man->getRole(false, $roleid);
		$id_role 	= $role[ACL_INFO_IDST];
		$this->acl_man->addToRole($id_role, $idst_group);
	}
	
	function removePerm($groupid, $roleid) {
	
		$group 		= $this->acl_man->getGroup(false, $groupid);
		$idst_group	= $group[ACL_INFO_IDST];
		$role 		= $this->acl_man->getRole(false, $roleid);
		$id_role 	= $role[ACL_INFO_IDST];
		$this->acl_man->removeFromRole($id_role, $idst_group);
	}
}


?>