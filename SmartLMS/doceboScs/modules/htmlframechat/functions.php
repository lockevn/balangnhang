<?php
/*************************************************************************/
/* DOCEBO FRAMEWORK                                                      */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <giovanni[AT]docebo-com>         */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package  DoceboSCS
 * @version  $Id: functions.php 113 2006-03-08 18:08:42Z ema $
 */


function setRoom(& $out, & $lang) {

	$ri=importVar("ri");

	if ($ri > 0) {
		$_SESSION["chat_room_id"]=$ri;

		$script ="<script type=\"text/javascript\">\n";
		//$script.="parent.chatText.reloadMsg();\n";
		$script.="parent.chatCtl.resetLmi();\n";
		$script.="parent.chatUsers.setTimeout('refreshPage()',1000);\n";
		$script.="</script>\n";
		$out->add($script);
	}

	$out->add(listRooms($out, $lang));

}


// ----------------------------------------------------------------------------

class HtmlChatEmoticons_FrameChat extends HtmlChatEmoticons {
 
	function emoticonList($ext="gif") {
	
		$res="";
		$emo_arr=$this->getEmoticonArr();
		$res.= '<div class="emoticons_container">';
		foreach($emo_arr as $name=>$code) {
			$res.="<a href=\"#\" onclick=\"javascript:addEmo('".$code."')\">";
			$res.=$this->getChatEmoticon($name);
			$res.="</a>\n";
		}
		$res.= '</div>';
	
		/*
		http://groups.google.it/group/it.comp.lang.javascript/browse_thread/thread/86e45294e6f75886/46828e7f1b88f59d?q=text+cursore&rnum=3&hl=it#46828e7f1b88f59d
		*/
	
		return $res;
	}	

}

?>