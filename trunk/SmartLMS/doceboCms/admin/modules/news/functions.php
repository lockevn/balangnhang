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


if (!defined("_TOPIC_FPATH")) define("_TOPIC_FPATH", "files/topic/");



function get_maintopic_info($idNews) {

	$res=array();
	$sel_lang=getLanguage();

	$fields="t1.topic_id, t1.img_align, t2.label, t2.image";
	$qtxt ="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_news_topic as t1, ".$GLOBALS["prefix_cms"]."_topic as t2 ";
	$qtxt.="WHERE t1.idNews='$idNews' AND t1.main='1' AND t1.topic_id=t2.topic_id AND t2.language='$sel_lang'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$res=$row;
	}

	return $res;
}



function get_related_topics($idNews) {


	$res=array();

	$qtxt="SELECT topic_id FROM ".$GLOBALS["prefix_cms"]."_news_topic WHERE idNews='$idNews' AND main='0'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$res[]=$row["topic_id"];
		}
	}

	return $res;
}



function select_main_topic($name, $title, $sel, & $form) {

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	$sel_lang=getLanguage();

	//$onchange="onchange=\"javascript:image$num.src='files/topic/'+this.value;\"";

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE language='$sel_lang' ORDER BY label";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {

		$arr=array();
		while($row=mysql_fetch_array($q)) {
			$arr[$row["topic_id"]]=$row["label"];
		}

		$out->add($form->getDropdown($title, $name, $name, $arr, $sel));
	}

	/*echo ("<img name=\"image".(int)$num."\" src=\"".getPathImage()."csspreview/css_");
	$out->add((int)$cur.".jpg\" alt=\""._TOPICIMAGE."\" />\n");*/
}



function select_topic_img_align($name, $title, $sel, & $form) {

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_news', 'cms');

	$options=array();
	$options["noimage"]=$lang->def("_NOIMAGE");
	$options["right"]=$lang->def("_RIGHT");
	$options["left"]=$lang->def("_LEFT");

	$out->add($form->getDropdown($title, $name, $name, $options, $sel));

}



function select_related_topic($name, $title, $sel=NULL, & $form) {

	$out=& $GLOBALS['page'];

	$sel_lang=getLanguage();
	if ($sel == NULL) $sel=array();

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE language='$sel_lang' ORDER BY label";
	$q=mysql_query($qtxt);


	if (($q) && (mysql_num_rows($q) > 0)) {


		$out->add($form->getOpenCombo($title));

		while($row=mysql_fetch_array($q)) {
			$chk=(in_array($row["topic_id"], $sel) ? true : false);
			$field_name=$name."[".$row["topic_id"]."]";
			$out->add($form->getCheckbox($row["label"], $field_name, $field_name, $row["topic_id"], $chk));
		}

		$out->add($form->getCloseCombo());

	}

}



function save_topic_info($idNews, $n_main, $n_imgalign, $n_related) {

	$out=& $GLOBALS['page'];

	$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_news_topic WHERE idNews='$idNews'";
	$q=mysql_query($qtxt);


	$related_arr=(array)$_POST[$n_related];

	if ((!in_array($_POST[$n_main], $related_arr)) && ($_POST[$n_main] != 0)) {
		$related_arr[]=$_POST[$n_main];
	}

	foreach($related_arr as $key=>$val) {

		if ($val == $_POST[$n_main]) { // Main topic:
			$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_news_topic (idNews, topic_id, main, img_align) ";
			$qtxt.="VALUES('$idNews', '$val', '1', '".$_POST[$n_imgalign]."')";
		}
		else { // Related topics:
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_news_topic (idNews, topic_id) VALUES('$idNews', '$val')";
		}

		$q=mysql_query($qtxt);
	}

}




?>