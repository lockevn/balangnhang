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


	//field
	$eleDrop = array();
	$eleFree = array();
	//category dropdown
	$reCategory = mysql_query("
	SELECT idCommon, nameCategory
	FROM ".$GLOBALS["prefix_cms"]."_groupcategory
	WHERE language = '".getLanguage()."'
	ORDER BY nameCategory");

	while(list($idC, $nameC) = mysql_fetch_row($reCategory))
		$eleDrop[$idC] = $nameC;

	//freetext field
	$reFreetext = mysql_query("
	SELECT idCommon, nameFreetext, isDate
	FROM ".$GLOBALS["prefix_cms"]."_groupfreetext
	WHERE language = '".getLanguage()."'
	ORDER BY nameFreetext");

	while(list($idF, $nameF, $isDate) = mysql_fetch_row($reFreetext)) {
		$eleFree[$idF]['name'] = $nameF;
		$eleFree[$idF]['is_date'] = $isDate;
	}

	//textarea field
	$reFreetext = mysql_query("
	SELECT idCommon, nameTextarea
	FROM ".$GLOBALS["prefix_cms"]."_form_textarea
	WHERE language = '".getLanguage()."'
	ORDER BY nameTextarea");

	while(list($idF, $nameF) = mysql_fetch_row($reFreetext)) {
		$eleText[$idF]['name'] = $nameF;
	}


	//field assigned at this form
	$reField = mysql_query("
	SELECT id, idField, type, comp
	FROM ".$GLOBALS["prefix_cms"]."_form_items
	WHERE idForm = '$idForm'
	ORDER BY ord");


	echo '<div class="feedback_form">';
	while(list($idF, $idR, $type, $mandatory) = mysql_fetch_row($reField)) {

		//write field of the form
		echo '<div class="form_line_l">';
		if($type == 'dropdown') {

			//input type -> select
			echo '<label class="floating" for="formfield'.$idF.'">';
			if($mandatory) echo '<span class="fontRed">*</span>';
			if($mandatory) echo '<input type="hidden" id="mandField'.$idF.'" name="mandField['.$idF.']" value="'.$idF.'" />';
			echo $eleDrop[$idR].':</label> <select class="dropdown" id="formfield'.$idF.'" name="formfield['.$idF.']">';

			//finding category values
			$reVoice = mysql_query("SELECT idCommonVoice, nameVoice FROM ".$GLOBALS["prefix_cms"]."_groupcategory_voice
			WHERE idCommon = '$idR' AND language = '".getCmsAdmLang()."'");
			echo '<option value="">'._NOVALUE.'</option>';
			while(list($idCommonVoice, $voice) = mysql_fetch_row($reVoice)) {
				echo '<option value="'.$idCommonVoice.'"';
				if($idCommonVoice == $valueIns[$idF]) echo ' selected="selected"';
				echo '>'.$voice.'</option>';
			}
			echo '</select>';
			echo '<input type="hidden" id="field_label'.$idF.'" name="field_label['.$idF.']" value="'.$eleDrop[$idR].'" />';
		}
		elseif($type == 'freetext') {

			//input type -> textfield
			if($mandatory) echo '<input type="hidden" id="mandField'.$idF.'" name="mandField['.$idF.']" value="'.$idF.'" />';
			if($eleFree[$idR]['is_date'] == 1) {

				//calendar
				echo '<label class="floating" for="date'.$idF.'">';
				if($mandatory) echo '<span class="fontRed">*</span>';
				echo $eleFree[$idR]['name'].":</label> ";
				echo '<input type="hidden" name="date['.$idF.']" value="'.$idF.'" />';
				init_calendar();
				//make_cal($valueIns[$idF], $idF);
				echo '<input class="textfield" type="text" id="date'.$idF.'" name="formfield['.$idF.']" maxlength="10" value="'.$valueIns[$idF].'" /> ';
				echo("<button id=\"trigger$idF\" name=\"trigger$idF\" class=\"calbtn\"></button><br />\n");
				setup_cal($idF);

			}
			else {
				echo '<label class="floating" for="formfield'.$idF.'">';
				if($mandatory) echo '<span class="fontRed">*</span>';
				echo $eleFree[$idR]['name'].':</label>'
					.' <input class="textfield" type="text" id ="formfield'.$idF.'" name="formfield['.$idF.']" maxlength="255" value="'.$valueIns[$idF].'" />';
			}
			echo '<input type="hidden" id="field_label'.$idF.'" name="field_label['.$idF.']" value="'.$eleFree[$idR]['name'].'" />';
		}
		elseif($type == 'textarea') {

			//input type -> textarea
			echo '<label class="label_bold" for="formfield'.$idF.'">';
			if($mandatory) echo '<span class="fontRed">*</span>';
			if($mandatory) echo '<input type="hidden" name="mandField['.$idF.']" value="'.$idF.'" />';
			echo $eleText[$idR]['name'].':</label><br />'
				.'<textarea class="textarea" id="formfield'.$idF.'" name="formfield['.$idF.']" rows="6" cols="40">'
				.$valueIns[$idF].'</textarea>'
				.'<input type="hidden" id="field_label'.$idF.'" name="field_label['.$idF.']" value="'.$eleText[$idR]['name'].'" />';
		}


		echo '</div>';
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


function get_field_names() {

	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

	$fl=new FieldList();
	$all_fields=$fl->getAllFields();

	$names=array();

	foreach($all_fields as $key=>$val) {
		$names[$val[FIELD_INFO_TYPE]][$val[FIELD_INFO_ID]]=$val[FIELD_INFO_TRANSLATION];
	}

	/*$sel_lang=getLanguage();

	$qtxt="SELECT idCommon, nameCategory FROM ".$GLOBALS["prefix_cms"]."_groupcategory WHERE language='$sel_lang';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$names["dropdown"][$row["idCommon"]]=$row["nameCategory"];
		}
	}

	$qtxt="SELECT idCommon, nameFreetext, isDate FROM ".$GLOBALS["prefix_cms"]."_groupfreetext WHERE language='$sel_lang';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			if ($row["isDate"]) {
				$names["datefield"][$row["idCommon"]]=$row["nameFreetext"];
			}
			else {
				$names["freetext"][$row["idCommon"]]=$row["nameFreetext"];
			}
		}
	}

	$qtxt="SELECT idCommon, nameTextarea FROM ".$GLOBALS["prefix_cms"]."_form_textarea WHERE language='$sel_lang';";
	$q=mysql_query($qtxt);
	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$names["textarea"][$row["idCommon"]]=$row["nameTextarea"];
		}
	} */

	return $names;
}


?>
