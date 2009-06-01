<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package  admin-library
 * @subpackage user
 * @version 	$Id:$
 */
 
if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework'].'/modules/org_chart/tree.org_chart.php');

define("ORG_CHART_NORMAL", 1);
define("ORG_CHART_WITH_DESCENDANTS", 2);

class OrgChartManager {

	var $tree_db	= false;
	var $tree_view 	= false;

	function OrgChartManager() {
		
		$this->tree_db 		= new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');
		$this->tree_view 	= new TreeView_OrgView($this->tree_db, 'organization_chart', $GLOBALS['title_organigram_chart']);
	}

	function getFolderFormIdst(&$arr_idst) {
		
		$acl_man 	=& $GLOBALS['current_user']->getAclManager();
		$groups_id = $acl_man->getGroupsId($arr_idst);
		
		$folder_name = $this->tree_db->getFoldersCurrTranslationDoubleCheck($groups_id);
		
		$branch_name = array();
		while(list($id, $groupid) = each($groups_id)) {
			
			$id_dir = split('_', $groupid);
			$branch_name[$id]['name'] = $folder_name[$id_dir[1]];
			$branch_name[$id]['type_of_folder'] = ( $id_dir[0] == '/oc' ? ORG_CHART_NORMAL : ORG_CHART_WITH_DESCENDANTS );
		}
		return $branch_name;
	}
	
	function getAllGroupIdFolder() {
		
		return $this->tree_db->getAllGroupST();
	}
	
}

?>
