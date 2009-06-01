<?php

/*************************************************************************/
/* DOCEBO - Content Management System                                    */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks                                  */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function change_item_order($type, $direction, $id) {

	switch($type) {

		case "news": {
			$table_name=$GLOBALS["prefix_cms"]."_news";
			$table_id="idNews";
		} break;

		case "content": {
			$table_name=$GLOBALS["prefix_cms"]."_content";
			$table_id="idContent";
		} break;

	}


	$qtxt="SELECT idFolder, ord FROM $table_name WHERE $table_id='$id'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$folder=$row["idFolder"];
		$current_ord=$row["ord"];
	}
	else {
		return 0;
	}


	$qtxt="SELECT * FROM $table_name WHERE idFolder='$folder' AND ord='$current_ord'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 1)) { // Something wrong..
		fix_item_order($table_name, $table_id, $folder);
	}
	else if (($q) && (mysql_num_rows($q) == 1)) { // Ok, let's do it..
		switch($direction) {
			case "down": {
				$qtxt="SELECT $table_id FROM $table_name WHERE idFolder='$folder' AND ord>'$current_ord' ORDER BY ord ASC";
				$switch_to_ord=$current_ord+1;
			} break;
			case "up": {
				$qtxt="SELECT $table_id FROM $table_name WHERE idFolder='$folder' AND ord<'$current_ord' ORDER BY ord DESC";
				$switch_to_ord=$current_ord-1;
			} break;
		}
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$switch_to_id=$row[$table_id];

			$qtxt="UPDATE $table_name SET ord='$switch_to_ord' WHERE $table_id='$id'";
			$q=mysql_query($qtxt);

			$qtxt="UPDATE $table_name SET ord='$current_ord' WHERE $table_id='$switch_to_id'";
			$q=mysql_query($qtxt);

		}
		else {
			return 0;
		}

	}
	else { // "nooooooooooo"
		return 0;
	}

}


function fix_item_order($table_name, $table_id, $idFolder) {

	$qtxt="SELECT $table_id FROM $table_name WHERE idFolder='$idFolder' ORDER BY ord ASC, publish_date DESC";
	$q=mysql_query($qtxt);

	$i=1;
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$id=$row[$table_id];

			$qtxt="UPDATE $table_name SET ord='$i' WHERE $table_id='$id'";
			$update_q=mysql_query($qtxt);

			$i++;
		}
	}

}



function has_attach($tab, $idName, $id) {
	// restituisce true se una news, un contenuto, ...
	// ha degli allegati.


	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"].$tab." WHERE $idName='$id';";
	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) $res=1;

	return $res;
}


?>
