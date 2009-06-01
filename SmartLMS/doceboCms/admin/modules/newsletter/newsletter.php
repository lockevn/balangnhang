<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(isset($_SESSION['sesCmsAdmUser']) && isset($_SESSION['sesCmsAdmLevel'])) {

function newsletter() {
	//access control
	global $prefixCms, $prefix;
	include('admin/modules/group/groupUtils.php');
	funAdminAccess('OP');

	addTitleArea('newsletter');

	echo("<div class=\"stdBlock\">\n");

	$qtxt="SELECT email FROM ".$prefix."_user WHERE idUser='".$_SESSION["sesCmsAdmUser"]."';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$myemail=$row["email"];
	}

	if ($err != "") echo("<b><span class=\"fontRed\">$err</span><br />\n");
	echo("<form action=\"admin.php?modulename=newsletter&amp;op=initsend\" method=\"post\">\n");

	echo("<br /><div class=\"title\">"._SENDER.":</div>\n");
	echo("<input type=\"text\" id=\"fromemail\" name=\"fromemail\" size=\"35\" value=\"$myemail\" /><br />\n");

	echo("<br /><div class=\"title\">"._SUBJECT.":</div>\n");
	echo("<input type=\"text\" id=\"sub\" name=\"sub\" size=\"35\" /><br />\n");

	echo("<br /><div class=\"title\">"._TEXT.":</div>\n");
	echo("<textarea rows=\"10\" cols=\"70\" id=\"msg\" name=\"msg\"></textarea><br />\n");

	echo("<br /><div class=\"title\">"._SENDTO.":</div>\n");
	$groups = listGroup();
	echo("<div class=\"groupsperm\">");
	while(list($idCommon, $nameGroup) = each($groups)) {
		if ($idCommon >= 0) {
			echo("<input type=\"checkbox\" id=\"idGroups[".$idCommon."]\" name=\"idGroups[".$idCommon."]\" value=\"".$idCommon."\" />\n");
			echo("<label for=\"idGroups[".$idCommon."]\">".$nameGroup."</label><br />\n");
		}
	}
	echo '</div>'."\n\n";

	echo("<br /><div class=\"title\">"._LANGUAGE.":\n");
	$lang_list = get_lang_list();
	echo("<select id=\"sel_lang\" name=\"sel_lang\">\n");
	foreach ($lang_list as $key=>$val) {
		echo("<option value=\"$val\">$val</option>\n");
	}
	echo("</select></div>\n");

	echo("<br /><br /><input class=\"button\" type=\"submit\" value=\""._SEND."\" />\n");
	echo("</form>\n");

	echo("</div>\n");
}


function send_newsletter($send_id) {
	//access control
	global $prefixCms, $prefix, $cms_nl_sendpercycle;
	include('admin/modules/group/groupUtils.php');
	funAdminAccess('OP');

	//@set_time_limit(60*15); // 15 minutes!


	addTitleArea('newsletter');

	echo("<div class=\"stdBlock\">\n");

	//echo("<pre>\n"); print_r($_POST); echo("</pre>\n");

	/*$sel_lang=$_POST["sel_lang"];
	$sel_groups=(count($_POST["idGroups"]) > 0 ? implode(",", $_POST["idGroups"]) : "''");*/


	$info=get_send_info($send_id);

	$sel_groups=$info["sel_groups"];
	$sel_lang=$info["sel_lang"];
	$tot=$info["tot"];

	$sub=$info["sub"];
	$msg=$info["msg"];
	$fromemail=$info["fromemail"];

	//echo ($sel_groups."   ".$sel_lang."   ".$tot); die();


	$cycle=(int)$_GET["cycle"];

	// Items per cycle
	$ipc=$cms_nl_sendpercycle;


	if (($cycle+1)*$ipc < $tot) {
		$sendcomplete=0;
	}
	else {
		$sendcomplete=1;
	}


	$fields="t1.idUser, t2.email";
	$qtxt ="SELECT $fields FROM ".$prefixCms."_groupuser as t1, ".$prefix."_user as t2 ";
	$qtxt.="WHERE t1.idGroup IN ($sel_groups) AND t2.idUser=t1.idUser AND ";
	$qtxt.="t2.email!='' AND t2.valid='1' AND t2.language='".$sel_lang."' GROUP BY t1.idUser ORDER BY t1.idUser ";
	$qtxt.="LIMIT ".$cycle*$ipc.", ".$ipc;

	$q=mysql_query($qtxt); //echo($qtxt); die();



	if (($q) && (mysql_num_rows($q) > 0)) {

		//$cnt=0;
		//$old_per=0;
		while ($row=mysql_fetch_array($q)) {
			//$perc=(int)($cnt*100/$tot);

			//if ($perc != $old_perc) { // Shows the progress..
				//if ($perc < 10) echo("0");
				//echo ($perc."% &nbsp;&nbsp;\n");
				//flush();
				//if (($perc > 0) && ($perc % 20 == 0)) echo("<br />");
			//}

			/*if (($cnt > 0) && ($cnt % 100 == 0)) { // Does a 5 second pause every 100 email sent to allow the server to breath
				echo ("<br /> &nbsp;&nbsp;\n["._PAUSE."] &nbsp;&nbsp;\n<br />");
				flush();
				sleep(5);
				$sleep_cnt=0;
			} */

			// Send the email: ------------------------------

			mail($row["email"], $sub, $msg,
				     "From: $fromemail\r\n".
				     "Reply-To: $fromemail\r\n".
				     "X-Mailer: DoceboCMS");

			// ----------------------------------------------

			//$cnt++;
			//$old_perc=$perc;
		}
		//$perc=$cnt*100/$tot;


		//echo("$perc%<br /><br /><b>"._SEND_COMPLETE."</b>\n");
	}
	else {
		//echo("<br /><br /><b>"._SEND_COMPLETE."</b>\n");
	}


	if ($sendcomplete) {

		$url="./admin.php?modulename=newsletter&op=complete";
		header("location: ".$url);

	}
	else {

		$url="./admin.php?modulename=newsletter&op=pause&ipc=".$ipc."&cycle=".($cycle+1)."&id=".$send_id;
		header("location: ".$url);

	}



	echo("</div><br />\n");

	echo("<form action=\"admin.php?modulename=newsletter&amp;op=newsletter\" method=\"post\">\n");
	echo("<div class=\"stdBlock\">\n");
	echo("<input class=\"button\" type=\"submit\" value=\""._BACK."\" />\n");
	echo("</div>\n");
	echo("</form>\n");
}



function nl_pause() {
	global $delay;

	addTitleArea("newsletter");

	echo("<div class=\"stdBlock\">\n");

	$cycle=(int)$_GET["cycle"];
	$ipc=(int)$_GET["ipc"];

	echo("<br />Stanno per essere inviati i messaggi da ".($cycle*$ipc)." a ".($cycle*$ipc+$ipc)."<br />\n");

	echo("<br /><br />...".$delay." secondi di pausa...\n");
	echo("<br />Non chiudere la pagina finch&eacute; non compare la scritta \"Operazione completata\"<br /><br />\n");

	echo("</div>\n");
}



function nl_sendcomplete() {

	funAdminAccess('OP');

	addTitleArea("newsletter");

	echo("<div class=\"stdBlock\">\n");

	echo("<br /><b>"._SEND_COMPLETE."</b><br /><br />\n");

	echo("</div><br />\n");

	echo("<form action=\"admin.php?modulename=newsletter&amp;op=newsletter\" method=\"post\">\n");
	echo("<div class=\"stdBlock\">\n");
	echo("<input class=\"button\" type=\"submit\" value=\""._BACK."\" />\n");
	echo("</div>\n");
	echo("</form>\n");
}



function init_send() {
	global $prefixCms, $prefix;
	require_once("core/sqlHtmlControl.php");

	$sel_lang=$_POST["sel_lang"];
	$sel_groups=$_POST["idGroups"];


	$translate_table = getTranslateTable();

	$sub=stripslashes(translateChr($_POST["sub"], $translate_table, true));
	$msg=stripslashes(translateChr($_POST["msg"], $translate_table, true));
	$fromemail=$_POST["fromemail"];


	$sel_groups_list=(count($sel_groups) > 0 ? implode(",", $sel_groups) : "''");

	$fields="COUNT(DISTINCT t1.idUser) as tot";
	$qtxt ="SELECT $fields FROM ".$prefixCms."_groupuser as t1, ".$prefix."_user as t2 ";
	$qtxt.="WHERE t1.idGroup IN ($sel_groups_list) AND t2.idUser=t1.idUser AND ";
	$qtxt.="t2.email!='' AND t2.valid='1' AND t2.language='".$sel_lang."'";
	$q=mysql_query($qtxt);

	$tot=0;
	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$tot=$row["tot"];
	}
	else if (!$q) {
		echo($qtxt."<br /><br />\n");
		echo(mysql_error()."<br />\n");
		die();
	}


	$qtxt="DELETE FROM ".$prefixCms."_newsletter WHERE stime < (DATE_SUB(NOW(), INTERVAL 1 DAY))";
	$q=mysql_query($qtxt);

	$first=1;
	foreach($sel_groups as $key=>$val) {

		if ($first) {

			$qtxt ="INSERT INTO ".$prefixCms."_newsletter (idGroup, sub, msg, fromemail, language, tot, stime) ";
			$qtxt.="VALUES ('$val', '$sub', '$msg', '$fromemail', '$sel_lang', '$tot', NOW())";
			$q=mysql_query($qtxt); echo mysql_error();

			$qtxt="SELECT LAST_INSERT_ID() as last_id FROM ".$prefixCms."_newsletter";
			$q=mysql_query($qtxt);

			$row=mysql_fetch_array($q);
			$last_id=$row["last_id"];

			$qtxt="UPDATE ".$prefixCms."_newsletter SET id_send='".$last_id."' WHERE id='$last_id'";

		}
		else {

			$qtxt ="INSERT INTO ".$prefixCms."_newsletter (id_send, idGroup, sub, msg, fromemail, language, tot, stime) ";
			$qtxt.="VALUES ('$last_id', '$val', '$sub', '$msg', '$fromemail', '$sel_lang', '$tot', NOW())";

		}

		$first=0;
		$q=mysql_query($qtxt); //echo "<br />[$val] -&gt; ".$qtxt."<br />\n";
	}


	send_newsletter($last_id);
}


function get_send_info($send_id) {
	global $prefixCms;

	$sel_lang="";
	$sel_groups=array();
	$res=array();

	$qtxt="SELECT idGroup, sub, msg, fromemail, language, tot FROM ".$prefixCms."_newsletter WHERE id_send='".$send_id."'";
	$q=mysql_query($qtxt); //echo $qtxt;

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			if ($sel_lang == "") $sel_lang=$row["language"];

			$sel_groups[]=$row["idGroup"];

			if ($row["tot"] > 0) $tot=$row["tot"];
			if ($row["sub"] != "") $sub=$row["sub"];
			if ($row["msg"] != "") $msg=$row["msg"];
			if ($row["fromemail"] != "") $fromemail=$row["fromemail"];

		}
	}

	$sel_groups=(count($sel_groups) > 0 ? implode(",", $sel_groups) : "''");


	$res["sel_groups"]=$sel_groups;
	$res["sel_lang"]=$sel_lang;
	$res["tot"]=$tot;
	$res["sub"]=$sub;
	$res["msg"]=$msg;
	$res["fromemail"]=$fromemail;

	return $res;
}



switch($op) {
	case "newsletter" : {
		newsletter();
	};break;

	case "initsend": {
		init_send();
	} break;

	case "send" : {
		$send_id=(int)$_GET["id"];
		send_newsletter($send_id);
	};break;

	case "pause" : {
		nl_pause();
	};break;

	case "complete": {
		nl_sendcomplete();
	}
}







}

?>