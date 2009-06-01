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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------


function drawTaskLine($data, $project_id, $can_task, $mod_path, $from_ajax=FALSE) {
	$res ="";

	if ($from_ajax) {
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
	}
	$lang =& DoceboLanguage::createInstance( 'simpleprj', 'cms');

	$id =$data["task_id"];
	$complete =($data["complete"] == 1 ? TRUE : FALSE);
	$status =$data["complete"];
	$description =(!empty($data["description"]) ? $data["description"] : $lang->def("_UNTITLED"));

	$res.=($from_ajax ? "" : '<div id="task_line_'.$id.'">');
	$p_class =($can_task ? "task_desc_small" : "task_desc");

	$res.='<div class="task_actions">';
	$img ='<img src="'.getPathImage().'simpleprj/task_'.($complete ? "complete" : "incomplete").'.gif" ';
	$img_title =($complete ? $lang->def("_TASK_COMPLETE") : $lang->def("_TASK_INCOMPLETE"));
	$img.='alt="'.$img_title.'" title="'.$img_title.'" id="task_status_img_'.$id.'" />';
	if ($can_task) {
		$action_code ='onclick="switchTaskStatus(\''.$status.'\', \''.$id.'\', \''.$project_id.'\'); return false;" ';
		$res.='<a href="#" '.$action_code.' id="task_status_link_'.$id.'">'.$img."</a>";
	}
	else {
		$res.=$img;
	}

	if ($can_task) {

		$img ='<img src="'.getPathImage().'simpleprj/del.png" ';
		$img.='alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />';
		$title =substr($data["description"], 0, 30);
		$action_code ='onclick="delTaskWindow(\''.$id.'\', \''.$project_id.'\', \''.$title.'\'); return false;" ';
		$res.='<a href="#" '.$action_code.'>'.$img."</a>";
	}

	$res.='</div>'; // task_actions


	$res.='<p id="task_'.$id.'" class="'.$p_class.'">'.$description.'</p>';

	if ($can_task) {

		$res.='<script type="text/javascript">';
		$res.="new Ajax.InPlaceEditor('task_".$id."', '".$mod_path."ajax.simpleprj.php?op=inline_editor&task_id=".$id."', ";
		$res.="{okText: '".$lang->def("_SAVE")."', cancelText: '".$lang->def("_UNDO")."', ";
		$res.="onComplete:  function(objReq) { alert(obJReq.responseText); } , ajaxOptions: { method:'post' }});";
		$res.='</script>';
	}

	$res.=($from_ajax ? "" : "</div>"); // task_line

	return $res;
}


?>
