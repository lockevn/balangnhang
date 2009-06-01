<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2002 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


class Module_Manlang extends Module {
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op;
		
		
		echo '<script type="text/javascript" src="addons/fckeditor/fckeditor.js">'
			.'</script>'."\n";
		
		
		echo '<link href="templates/'.getTemplate().'/style/style_treeview.css" rel="stylesheet" type="text/css" />';
		echo '<link href="templates/'.getTemplate().'/style/style_manpage.css" rel="stylesheet" type="text/css" />';
		
		
		return;
		
		
	}
}


//create class istance
$module_cfg = new Module_Manlang();

?>
