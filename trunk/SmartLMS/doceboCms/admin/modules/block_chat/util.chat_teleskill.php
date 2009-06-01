<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/



function chat_teleskillBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

 	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));


	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

	$url="index.php?modname=manpage&amp;op=modblock";
	$url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$url.=$sub_id."&amp;blk_op=selmodperm";
	$out->add("<a href=\"".$url."\">".$lang->def("_MODPERMSEL")."</a>\n");
}



function chat_teleskillBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);


	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function chat_teleskillBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}




function chat_teleskillBlockOption(& $out, & $lang, & $form, $block_id, $sub_id, $blk_op) {

	$backurl="index.php?modname=manpage&amp;op=modblock";
	$backurl.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$backurl.=$sub_id;

	switch ($blk_op) { // ------------------------------------------------

		case "selmodperm" : {
			if (!isset($_POST["undo"])) {
				$out->add(selModPerm($out, $lang, $form, $block_id, $sub_id));
			}
			else {
				jumpTo($backurl);
			}
		} break;

	}

}

function selModPerm(& $out, & $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS['where_cms']."/lib/lib.simplesel.php");

	$acl=$GLOBALS["current_user"]->getAcl();
	$role_id="/cms/chat/teleskill/block/".$block_id."/moderate";
	$st_id=$acl->getRoleST($role_id);
	if (($st_id === FALSE) && ($block_id > 0)) {
		$acl_manager=$acl->getACLManager();
		$st_id=$acl_manager->registerRole($role_id, "");
	}


	$ssel=new SimpleSelector(true, $lang);

	$perm=array();
	$perm["moderate"]["img"]=getPathImage('fw')."standard/modadmin.gif";
	$perm["moderate"]["alt"]=$lang->def("_ALT_MODERATE");

	$ssel->setPermList($perm);


	$back_url="index.php?modname=manpage&amp;op=modblock";
	$back_url.="&amp;write=1&amp;block_id=".$block_id."&amp;sub_id=";
	$back_url.=$sub_id;
	$url=$back_url."&amp;blk_op=selmodperm";
	$ssel->setLinks($url, $back_url);

	$op=$ssel->getOp();

	if (($op == "main") || ($op == "manual_init") ) {
		$acl_manager=$GLOBALS["current_user"]->getACLManager();
		$saved_data["moderate"]=array_flip($acl_manager->getRoleMembers($st_id));
	}

	$page_body="";
	$full_page="";

	$title=$lang->def("_SEL_TELESKILL_CHAT_MODERATORS");

	switch($op) {

		case "main": {
			$ssel->setSavedData($saved_data);
			$page_body=$ssel->loadSimpleSelector();
		} break;

		case "manual_init":{

			// Saving permissions of simple selector
			$save_info=$ssel->getSaveInfo();
			//saveForumPerm($idForum, $save_info["selected"], $save_info["database"]);

			$ssel->setSavedData($saved_data);
			$full_page=$ssel->loadManualSelector($title);
		} break;
		case "manual": {
			$full_page=$ssel->loadManualSelector($title);
		} break;

		case "save_manual": {

			// Saving permissions of manual selector
			$save_info=$ssel->getSaveInfo();
			saveTeleskillChatModPerm($st_id, $save_info["selected"], $save_info["database"]);

			jumpTo(str_replace("&amp;", "&", $url));
		} break;

		case "save": {

			// Saving permissions of simple selector
			$save_info=$ssel->getSaveInfo();
			saveTeleskillChatModPerm($st_id, $save_info["selected"], $save_info["database"]);

			jumpTo(str_replace("&amp;", "&", $back_url));
		} break;

	}

	if (!empty($full_page))
		$out->add($full_page);

	if (!empty($page_body)) {
		$out->add($page_body);

	}

}




function saveTeleskillChatModPerm($st_id, $selected, $database) {

	if (!($st_id > 0)) return 0;

	$arr_selection=$selected["moderate"];
	$db_tmp=array_flip($database["moderate"]);
	$arr_unselected=array_diff($db_tmp, $arr_selection);
	unset($db_tmp);

	$acl_manager=$GLOBALS["current_user"]->getACLManager();

	foreach($arr_unselected as $idstMember) {
		$acl_manager->removeFromRole($st_id, $idstMember );
	}

	foreach($arr_selection as $idstMember) {
		$acl_manager->addToRole($st_id, $idstMember );
	}

}


?>