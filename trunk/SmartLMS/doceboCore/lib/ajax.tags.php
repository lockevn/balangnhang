<?php defined("IN_DOCEBO") or die('You can\'t access directly');

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2008													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package course management
 * @subpackage course catalogue
 * @category ajax server
 * @version $Id:$
 *
 */

if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

require_once($GLOBALS['where_framework'].'/lib/lib.tags.php');

$op = get_req('op', DOTY_ALPHANUM, '');	
switch($op) {
	case "get_platform_cloud" : {
		
		$tags = new Tags('*');
		
		$cloud = $tags->getPlatformTagCloud();
		docebo_cout($cloud); 
	};break;
	case "get_course_cloud" : {
		
		$tags = new Tags('*');
		$cloud = $tags->getCourseTagCloud();
		docebo_cout($cloud); 
	};break;
	case "get_user_cloud" : {
		
		$tags = new Tags('*');
		
		$cloud = $tags->getUserTagCloud( getLogUserId() );
		docebo_cout($cloud); 
	};break;
	case "save_tag" : {
		
		$compiled_tags 	= get_req('tags', DOTY_STRING, '');
		$id_resource 	= get_req('id_resource', DOTY_INT, '');
		$resource_type 	= get_req('resource_type', DOTY_ALPHANUM, '');
		
		$title 		 	= get_req('title', DOTY_STRING, '');
		$sample 		= get_req('sample_text', DOTY_STRING, '');
		$permalink 		= get_req('permalink', DOTY_STRING, '');
		
		$private = false;
		$req_private = get_req('private', DOTY_INT, '0');
		if($req_private) {
			// requested to save as private, check if the user can do this operation
			if(isset($_SESSION['levelCourse']) && $_SESSION['levelCourse'] > 3) {
				$private = true;
			}
			if($GLOBALS['current_user']->getUserLevelId() == ADMIN_GROUP_GODADMIN) {

				$private = true;
            }
        }
		
		$tags = new Tags($resource_type);
		$updated_tags = $tags->updateTagResource($id_resource, getLogUserId(), $compiled_tags, $title, $sample, $permalink, $private);
		
		docebo_cout($updated_tags); 
	};break;
	default : {
		
		$query = get_req('query', DOTY_STRING, '');
		
		$tags = new Tags('*');
		$suggestion = $tags->getAutoComplete($query);
		
		$output = implode ($suggestion , "\n");
		
  		docebo_cout($output);
	};break;
}

?>