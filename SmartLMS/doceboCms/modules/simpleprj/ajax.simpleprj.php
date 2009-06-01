<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @category ajax server
 * @author Giovanni Derks
 * @version $Id:$
 *
 */

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');
if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

$path_to_root = '../..';

// prepare refer ------------------------------------------------------------------

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
require_once($GLOBALS['where_config'].'/config.php');

if ($GLOBALS["where_files"] != false) {
	$GLOBALS["where_files"]= str_replace('//', '/', '../'.$path_to_root.'/'.$GLOBALS["where_files"]);
}

ob_start();

// connect to database -------------------------------------------------------------------

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] )
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) )
	die( "Database not found. Check configurations" );

@mysql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@mysql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);


// load lms setting ------------------------------------------------------------------
require_once($GLOBALS['where_framework'].'/setting.php');
require_once($GLOBALS['where_cms'].'/setting.php');

session_name("docebo_session");
session_start();

// load regional setting --------------------------------------------------------------
require_once($GLOBALS['where_framework']."/lib/lib.regset.php");
$GLOBALS['regset'] = new RegionalSettings();

// load current user from session -----------------------------------------------------
require_once($GLOBALS['where_framework'].'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
require_once($GLOBALS['where_framework'].'/lib/lib.template.php');
require_once($GLOBALS['where_framework'].'/lib/lib.utils.php');

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

$GLOBALS['operation_result'] = '';
function docebo_cout($string) { $GLOBALS['operation_result'] .= $string; }

// here all the specific code ==========================================================

$op = importVar('op');

switch($op) {

	case "getLang": {
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( 'simpleprj', 'cms');

 		$lang_obj='{
			"_DEL_TITLE":"'.$lang->def('_DEL_TITLE').'",
			"_DEL_CONFIRM":"'.$lang->def('_DEL_CONFIRM').'",
			"_YES":"'.$lang->def('_CONFIRM').'",
			"_NO":"'.$lang->def('_UNDO').'",
			"_DEL_TITLE_RULE":"'.$lang->def('_DEL_TITLE_RULE').'",
			"_DEL_CONFIRM_RULE":"'.$lang->def('_DEL_CONFIRM_RULE').'",
			"_NEW_RULE":"'.$lang->def('_NEW_RULE').'",
			"_CONFIRM":"'.$lang->def('_CONFIRM').'",
			"_UNDO":"'.$lang->def('_UNDO').'",
			"_HIDE_BOX":"'.$lang->def('_HIDE_BOX').'",
			"_SHOW_BOX":"'.$lang->def('_SHOW_BOX').'"
		}';

  		docebo_cout($lang_obj);
	} break;

	case "addnewcomment" : {

		require_once($GLOBALS["where_framework"]."/lib/lib.ajax_comment.php");

		//$doc_id = importVar('doc_id', true, 0);
		$project_id = importVar('project_id', true, 0);
		$ax_comm = new AjaxComment('simpleprj_prj', 'cms');

		$comment_data = array(
			AJCOMM_EXTKEY 		=> $project_id,
			AJCOMM_AUTHOR		=> getLogUserId(),
			AJCOMM_POSTED 		=> date("Y-m-d H:i:s"),
			AJCOMM_TEXTOF 		=> importVar('text_of'),
			AJCOMM_TREE 		=> '',
			AJCOMM_PARENT 		=> importVar('reply_to'),
			AJCOMM_MODERATED 	=> '0'
		);

		$ax_comm->addComment($comment_data);
	}
	case "comment_it" : {

		require_once($GLOBALS["where_framework"]."/lib/lib.ajax_comment.php");
		require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");

		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( 'simpleprj', 'cms');

		$project_id = importVar('project_id', true, 0);
		$ax_comm = new AjaxComment('simpleprj_prj', 'cms');
		$ax_rend = new AjaxCommentRender('simpleprj', 'cms');

		$perm_name ="comment";
		$role ="/cms/modules/simpleprj/".$project_id."/".$perm_name;
		$can_comment =$GLOBALS["current_user"]->matchUserRole($role);
		$ax_comm->canReply($can_comment);

		$content = '';
		$comments = $ax_comm->getCommentByResourceKey($project_id);
		$ax_rend->setCommentToDisplay($comments);
		while(!$ax_rend->isEnd()) {

			$content .= $ax_rend->nextComment();
		}
		//$content.= $ax_rend->getAddCommentMask($project_id);
		$value = array(
			"next_op" 	=> '',
			"id" 		=> 'prj_comment',
			"title" 	=> $lang->def('_PROJECT_COMMENT'),
			"content" 	=> $content,
			"project_id" => $project_id
		);

		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;

	case "inline_editor": {
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( 'simpleprj', 'cms');

		$value = importVar('value');
		$task_id = importVar('task_id', true, 0);

		if (empty($value)) {
			$value =$lang->def("_UNTITLED");
		}

		require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
		$spm =new SimplePrjManager();

		// TODO: check perm!
		$data =array();
		$spm->saveTask($task_id, $value);

		docebo_cout($value);
	} break;

	case "addTask": {

		require_once($GLOBALS["where_cms"]."/modules/simpleprj/shared.simpleprj.php");

		$project_id = importVar('project_id', true, 0);
		$description = importVar('description');

		$value["project_id"]=$project_id;
		$value["description"]=$description;

		require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
		$spm =new SimplePrjManager();

		// TODO: check perm!
		$can_task =TRUE;
		$task_id =$spm->addTask($project_id, $description);
		$res =($task_id > 0 ? TRUE : FALSE);

		$data =array();
		$data["task_id"]=$task_id;
		$data["project_id"]=$project_id;
		$data["description"]=$description;
		$mod_path =$GLOBALS['where_cms_relative']."/modules/simpleprj/";

		$value["new_item_code"]=drawTaskLine($data, $project_id, $can_task, $mod_path, TRUE);
		$value["task_id"]=$task_id;
		$value["result"] =$res;

		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	} break;

	case "delTask": {

		$task_id = importVar('task_id', true, 0);
		$project_id = importVar('project_id', true, 0);

		$value["task_id"]=$task_id;
		$value["project_id"]=$project_id;

		require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
		$spm =new SimplePrjManager();

		// TODO: check perm!
		$res =$spm->deleteTask($task_id, $project_id);

		$value["result"] =$res;

		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	} break;


	case "switchtaskstatus": {

		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( 'simpleprj', 'cms');

		$task_id = importVar('task_id', true, 0);
		$project_id = importVar('project_id', true, 0);
		$status = importVar('status', true, 0);
		$new_status =($status == 1 ? 0 : 1);

		$complete =($new_status == 1 ? TRUE : FALSE);
		$new_image =getPathImage().'simpleprj/task_'.($complete ? "complete" : "incomplete").'.gif';
		$new_title =($complete ? $lang->def("_TASK_COMPLETE") : $lang->def("_TASK_INCOMPLETE"));

		$value["task_id"]=$task_id;
		$value["project_id"]=$project_id;
		$value["status"]=$new_status;
		$value["new_image"]=$new_image;
		$value["new_title"]=$new_title;

		require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
		$spm =new SimplePrjManager();

		// TODO: check perm!
		$res =$spm->saveTask($task_id, FALSE, $new_status);

		$value["result"] =$res;

		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	} break;
}

// =====================================================================================

// close database connection

mysql_close($GLOBALS['dbConn']);

ob_clean();
echo $GLOBALS['operation_result'];
ob_end_flush();

?>
