<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* & Fabio Pirovano <gishell[AT]tiscali-it>                              */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

function formMask($idForm, $loadSaved = false, $idUser = 0) {
	//REQUIRES: idCommon valid and included in a div class=userBlock, defined _FORMNAME and _NOVALUE
	//EFFECTS : construct the mask for form field filling
	global $prefixCms;
	require_once("core/manDateTime.php");


	//field
	$eleDrop = array();
	$eleFree = array();
	//category dropdown
	$reCategory = mysql_query("
	SELECT idCommon, nameCategory
	FROM ".$prefixCms."_groupcategory
	WHERE language = '".get_lang()."'
	ORDER BY nameCategory");

	while(list($idC, $nameC) = mysql_fetch_row($reCategory))
		$eleDrop[$idC] = $nameC;

	//freetext field
	$reFreetext = mysql_query("
	SELECT idCommon, nameFreetext, isDate
	FROM ".$prefixCms."_groupfreetext
	WHERE language = '".get_lang()."'
	ORDER BY nameFreetext");

	while(list($idF, $nameF, $isDate) = mysql_fetch_row($reFreetext)) {
		$eleFree[$idF]['name'] = $nameF;
		$eleFree[$idF]['is_date'] = $isDate;
	}

	//textarea field
	$reFreetext = mysql_query("
	SELECT idCommon, nameTextarea
	FROM ".$prefixCms."_form_textarea
	WHERE language = '".get_lang()."'
	ORDER BY nameTextarea");

	while(list($idF, $nameF) = mysql_fetch_row($reFreetext)) {
		$eleText[$idF]['name'] = $nameF;
	}


	//field assigned at this form
	$reField = mysql_query("
	SELECT id, idField, type, comp
	FROM ".$prefixCms."_form_items
	WHERE idForm = '$idForm'
	ORDER BY ord");


	echo '<div class=\"feedback_form\">';
	while(list($idF, $idR, $type, $mandatory) = mysql_fetch_row($reField)) {
		echo '<label>';
		if($mandatory) echo '<span class="fontRed">*</span>';
		if($type == 'dropdown') {
			if($mandatory) echo '<input type="hidden" name="mandField['.$idF.']" value="'.$idF.'" />';
			echo $eleDrop[$idR].':</label> <select name="formfield['.$idF.']">';

			//finding category values
			$reVoice = mysql_query("SELECT idCommonVoice, nameVoice FROM ".$prefixCms."_groupcategory_voice
			WHERE idCommon = '$idR' AND language = '".getCmsAdmLang()."'");
			echo '<option value="">'._NOVALUE.'</option>';
			while(list($idCommonVoice, $voice) = mysql_fetch_row($reVoice)) {
				echo '<option value="'.$idCommonVoice.'"';
				if($idCommonVoice == $valueIns[$idF]) echo ' selected="selected"';
				echo '>'.$voice.'</option>';
			}
			echo '</select>';
			echo '<input type="hidden" id="field_label['.$idF.']" name="field_label['.$idF.']" value="'.$eleDrop[$idR].'" />';
		}
		elseif($type == 'freetext') {
			if($mandatory) echo '<input type="hidden" name="mandField['.$idF.']" value="'.$idF.'" />';

			if($eleFree[$idR]['is_date'] == 1) {
				//calendar

				echo '<input type="hidden" name="date['.$idF.']" value="'.$idF.'" />';
				echo $eleFree[$idR]['name'].":</label> ";
				init_calendar();
				//make_cal($valueIns[$idF], $idF);
				echo '<input type="text" id="date'.$idF.'" name="formfield['.$idF.']" maxlength="10" value="'.$valueIns[$idF].'" readonly="readonly" /> ';
				echo("<button id=\"trigger$idF\" name=\"trigger$idF\" class=\"calbtn\"></button><br />\n");
				setup_cal($idF);

			}
			else {
				echo $eleFree[$idR]['name'].':</label> <input type="text" name="formfield['.$idF.']" maxlength="255" value="'.$valueIns[$idF].'" /><br />';
			}
			echo '<input type="hidden" id="field_label['.$idF.']" name="field_label['.$idF.']" value="'.$eleFree[$idR]['name'].'" />';
		}
		elseif($type == 'textarea') {
			if($mandatory) echo '<input type="hidden" name="mandField['.$idF.']" value="'.$idF.'" />';
			echo $eleText[$idR]['name'].':</label><br /><textarea id="formfield['.$idF.']" name="formfield['.$idF.']" rows="6" cols="40">'.$valueIns[$idF].'</textarea><br />';
			echo '<input type="hidden" id="field_label['.$idF.']" name="field_label['.$idF.']" value="'.$eleText[$idR]['name'].'" />';
		}


		echo '<br />';
	}
	echo '</div>';

}



function controlMandatoryFormField() {
	//REQUIRES: true
	//EFFECTS : if a mandatory is empty return false else return true
	//control mandatory field
	if(isset($_POST['mandField'])) {
		while(list($idF) = each($_POST['mandField']))
			if($_POST['formfield'][$idF] == '') return false;
	}
	return true;
}

?>
