<?

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

class Module_Newsletter extends CmsAdminModule {


	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op, $delay, $cms_nl_sendpause;

		switch($op) {
			case "pause": {
			
				$delay=$cms_nl_sendpause;
			
				$cycle=(int)$_GET["cycle"];
				$id=(int)$_GET["id"];
				
				$url="./admin.php?modulename=newsletter&amp;op=send&amp;cycle=".$cycle."&amp;id=".$id;
				echo("<meta http-equiv=\"refresh\" content=\"".$delay.";url=".$url."\">\n");				
			
			} break;

		}

		return;
	}
}

$module_cfg=new Module_Newsletter();

?>