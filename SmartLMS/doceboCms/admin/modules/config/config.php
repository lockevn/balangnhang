<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright (c) 2004 Fabio Pirovano (gishell@tiscali.it)				 */
/* http://www.spaghettilearning.com										 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(isset($_SESSION['sesCmsAdmUser']) && isset($_SESSION['sesCmsAdmLevel'])) {

function config() {
	funAdminAccess('config', 'OP');
	global $prefixCms;

	$dot = '<img src="'.getPathImage().'standard/dot.gif" alt="&gt;" />';
	$opz = importVar('opz', false, -1);

	//find groups
	$reGroups = mysql_query("
	SELECT DISTINCT regroup
	FROM ".$prefixCms."_setting
	WHERE hide_in_modify = '0'
	ORDER BY regroup");

	//area title
	addTitleArea('config');
	echo '<div class="stdBlock">'
		.'<div class="optionTitle">'._SELOPZ.'</div>'
		.'<div class="optionBlock">'
		.'<a'.( $opz == 'server' ? ' class="select"' : '')
		.' href="admin.php?modulename=config&amp;op=config&amp;opz=server">'.$dot.' '._OPZ_SERVER.'</a>';

	while(list($group) = mysql_fetch_row($reGroups)) {

		echo '<a'.( $opz == $group ? ' class="select"' : '')
			.' href="admin.php?modulename=config&amp;op=config&amp;opz='.$group.'">'.$dot
			.' '.constant('_GROUP_'.$group).'</a>';
	}
	/*echo '<a'.( $opz == 'chat' ? ' class="select"' : '')
		.' href="admin.php?modulename=config&amp;op=config&amp;opz=chat">'.$dot.' '._OPZ_CHAT.'</a>';*/
	echo '</div>';
	echo '</div>';

	switch($opz) {
		case "server" : opz_server();break;
		case "chat" : opz_chat();break;
		default : opz_group( $opz );break;
	}
}

function opz_server() {
	funAdminAccess('config', 'OP');
	global $uploadType;
	$php_conf = ini_get_all();

	$intest = '<div class="option_server_line"><span class="option_server_tit">';
	echo '<div class="stdBlock">'
		.'<div class="line_tit">'._SERVERINFO.'</div>'
		.$intest._SERVER_ADDR.' :</span> '.$_SERVER['SERVER_ADDR'].'</div>'
		.$intest._SERVER_PORT.' :</span> '.$_SERVER['SERVER_PORT'].'</div>'
		.$intest._SERVER_NAME.' :</span> '.$_SERVER['SERVER_NAME'].'</div>'
		.$intest._SERVER_ADMIN.' :</span> '.$_SERVER['SERVER_ADMIN'].'</div>'
		.$intest._SERVER_SOFTWARE.' :</span> '.$_SERVER['SERVER_SOFTWARE'].'</div>'

		.'<br />'
		.'<div class="line_tit">'._SERVER_MYSQL.'</div>'
		.$intest._MYSQL_VERS.' :</span> '.mysql_get_server_info().'</div>'

		.'<br />'
		.'<div class="line_tit">'._PHPINFO.'</div>'
		.$intest._PHPVERSION.' :</span> '.phpversion().'</div>'
		.$intest._SAFEMODE.' :</span> '.( $php_conf['safe_mode']['local_value'] ? _ON : _OFF ).'</div>'
		.$intest._REGISTER_GLOBAL.' :</span> '.( $php_conf['register_globals']['local_value'] ? _ON : _OFF ).'</div>'
		.$intest._MAGIC_QUOTES_GPC.' :</span> '.( $php_conf['magic_quotes_gpc']['local_value'] ? _ON : _OFF ).'</div>'
		.$intest._UPLOAD_MAX_FILESIZE.' :</span> '.$php_conf['upload_max_filesize']['local_value'].'</div>'
		.$intest._POST_MAX_SIZE.' :</span> '.$php_conf['post_max_size']['local_value'].'</div>'
		.$intest._MAX_EXECUTION_TIME.' :</span> '.$php_conf['max_execution_time']['local_value'].'s</div>'
		.$intest._DOMXML.' :</span> '.( extension_loaded('domxml') ? _ON :'<span class="fontRed">'._OFF.' ('._NOTSCORM.')</span>' ).'</div>';


	if($uploadType == 'ftp') {
		require_once( 'core/upload.php' );
		$re_con = sl_open_fileoperations();
		echo $intest._UPLOADFTP.' :</span> '.($re_con ? _FTPOK : '<span class="fontRed">'._FTPERR.'</span>') .'</div>';
		if($re_con) sl_close_fileoperations();
	}
	echo '<div class="noFloat"></div><br /></div>';
}

function opz_group( $group ) {
	funAdminAccess('config', 'OP');
	global $prefixCms;

	$group = (int)$group;
	if($group < 0) return;

	$reSetting = mysql_query("
	SELECT param_name, param_value, value_type, max_size
	FROM ".$prefixCms."_setting
	WHERE regroup = '$group' AND hide_in_modify = '0'
	ORDER BY sequence");

	echo '<form method="post" action="admin.php?modulename=config&amp;op=savegroup">'
		.'<div class="stdBlock">'
		.'<input type="hidden" name="group_sel" value="'.$group.'" />';

	if(isset($_GET['is_ok']) && ($_GET['is_ok'] == 1)) echo '<div class="operation_ok"><span>'._SAVEOK.'</span></div>';
	elseif(isset($_GET['is_ok']) && ($_GET['is_ok'] == 0)) {
		echo errorCommunication(_ERR_SAVE);
	}

	while(list( $var_name, $var_value, $value_type, $max_size ) = mysql_fetch_row( $reSetting ) ) {

		echo '<div class="line_tit"><label for="'.$var_name.'">'.constant('_'.strtoupper($var_name)).'</label></div>';
		switch( $value_type ) {
			case "language" : {
				//drop down language
				echo '<select class="dropSelect" id="'.$var_name.'" name="option['.$var_name.']">';
				$lang = get_lang_list();
				while( list( ,$name_l) = each($lang) ) {
					echo '<option value="'.$name_l.'"'.( $name_l == $var_value ? ' selected="selected"' : '' ).'>'
						.$name_l.'</option>';
				}
				echo '</select><br />';
			};break;
			case "template" : {
				//drop down template
				echo '<select class="dropSelect" id="'.$var_name.'" name="option['.$var_name.']">';
				$templ = getTemplateList();
				while( list( ,$name_t) = each($templ) ) {
					echo '<option value="'.$name_t.'"'.( $name_t == $var_value ? ' selected="selected"' : '' ).'>'
						.$name_t.'</option>';
				}
				echo '</select><br />';
			};break;
			case "hteditor" : {
				//drop down hteditor
				echo '<select class="dropSelect" id="'.$var_name.'" name="option['.$var_name.']">';
				$ht_edit = getHTMLEditorList();
				while( list($val_ht ,$name_ht) = each($ht_edit) ) {
					echo '<option value="'.$val_ht.'"'.( $val_ht == $var_value ? ' selected="selected"' : '' ).'>'
						.$name_ht.'</option>';
				}
				echo '</select><br />';
			};break;
			case "enum" : {
				//on off
				echo '<input type="radio" id="'.$var_name.'" name="option['.$var_name.']" value="on" '
					.( $var_value == 'on' ? ' checked="checked"' : '' ).' />'
					.'&nbsp;<label for="'.$var_name.'">'._ON.'</label>&nbsp;'

					.'<label for="'.$var_name.'off">'
					.'<input type="radio" id="'.$var_name.'off" name="option['.$var_name.']" value="off" '
					.( $var_value == 'off' ? ' checked="checked"' : '' ).' />'
					.'&nbsp;<label for="'.$var_name.'off">'._OFF.'</label>&nbsp;'
					.'<br />';
			};break;
			//string or int
			default : {
				echo '<input class="textfield" type="text" id="'.$var_name.'" name="option['.$var_name.']"'
					.' value="'.$var_value.'" maxlength="'.$max_size.'" size="40" /><br />';
			}
		}
	}
	echo '<br /><input class="button" type="submit" value="'._SAVE.'" />'
		.'</div>'
		.'</form>';
}

// XXX: opz_chat
function opz_chat() {
	funAdminAccess('config', 'OP');
	global $prefixCms;

	$type_sel = '2';
	$reChat = mysql_query("
	SELECT room_type, active_room, audio, video, lv_moderator,
		upload, lv_upload, external_upload,
		public_subroom, lv_public_subroom, external_public_subroom,
		private_subroom, lv_private_subroom, external_private_subroom,
		lv_can_talk
	FROM ".$prefixCms."_config_chat
	WHERE room_type = '$type_sel'
	ORDER BY room_type LIMIT 0,1");
	$opz_param = mysql_fetch_array($reChat);

	//-public-----------
	$lv_available = array(); //getLevelList();
	echo '<form method="post" action="admin.php?modname=config&amp;op=savechat">'
		.'<div class="stdBlock">';
	if(isset($_GET['is_err'])) errorCommunication(_ERRSAVECHAT);
	if(isset($_GET['is_ok'])) echo '<div class="operation_ok"><span>'._SAVEOK.'</span></div>';
	echo '<input type="hidden" name="type" value="'.$type_sel.'" />'
		.'<fieldset class="option"><legend>';
	switch($type_sel) {
		case "0" : echo _PUBLICROOM;break;
		case "1" : echo _SCHOOLROOM;break;
		case "2" : echo _COURSEROOM;break;
	}
	echo '</legend>'
		.'<label><input type="checkbox" name="param[active_room]" value="1"'
			.($opz_param['active_room'] == '1' ? ' checked="checked"' : '' ).' />'._ACTIVEROOM.'</label><br />'
		.'<label><input type="checkbox" name="param[audio]" value="1"'
			.($opz_param['audio'] == '1' ? ' checked="checked"' : '' ).' />'._AUDIO.'</label><br />'
		.'<label><input type="checkbox" name="param[video]" value="1"'
			.($opz_param['video'] == '1' ? ' checked="checked"' : '' ).' />'._VIDEO.'</label><br /><br />';

	//-moderator-------------------------------------------------------------------------------------

	echo '<fieldset class="levels"><legend>'._LV_MODERATOR.'</legend>';
	level_field('lv_moderator', $opz_param['lv_moderator'] , $lv_available);
	echo '</fieldset><br />';

	//-can-talk--------------------------------------------------------------------------------------

	echo '<fieldset class="levels"><legend>'._LV_CANTALK.'</legend>';
	level_field('lv_can_talk', $opz_param['lv_can_talk'] , $lv_available);
	echo '</fieldset>';

	echo '<br />'
		.'<input class="button" type="submit" value="'._SAVE.'" /><br /><br />';

	//-upload----------------------------------------------------------------------------------------

	echo '<fieldset class="levels"><legend>'
		.'<label><input type="checkbox" name="param[upload]" value="1"'
		.($opz_param['upload'] == '1' ? ' checked="checked"' : '' ).' />'._UPLOAD.'</label>'
		.'</legend>';
	level_field('lv_upload', $opz_param['lv_upload'] , $lv_available);
	if($type_sel == 0) {
		echo '<label><input type="checkbox" name="param[external_upload]" value="1"'
			.($opz_param['external_upload'] == '1' ? ' checked="checked"' : '' ).' />'._EXT_LV.'</label><br />';
	}
	echo '</fieldset><br />';

	//-public room-----------------------------------------------------------------------------------

	echo '<fieldset class="levels"><legend>'
		.'<label><input type="checkbox" name="param[public_subroom]" value="1"'
		.($opz_param['public_subroom'] == '1' ? ' checked="checked"' : '' ).' />'._PUBLICROOM2.'</label>'
		.'</legend>';
	level_field('lv_public_subroom', $opz_param['lv_public_subroom'] , $lv_available);
	if($type_sel == 0) {
		echo '<label><input type="checkbox" name="param[external_public_subroom]" value="1"'
			.($opz_param['external_public_subroom'] == '1' ? ' checked="checked"' : '' ).' />'._EXT_LV.'</label>';
	}
	echo '</fieldset><br />';

	//-private room-----------------------------------------------------------------------------------

	echo '<fieldset class="levels"><legend>'
		.'<label><input type="checkbox" name="param[private_subroom]" value="1"'
		.($opz_param['private_subroom'] == '1' ? ' checked="checked"' : '' ).' />'._PRIVATEROOM.'</label>'
		.'</legend>';
	level_field('lv_private_subroom', $opz_param['lv_private_subroom'] , $lv_available);
	if($type_sel == 0) {
		echo '<label><input type="checkbox" name="param[external_private_subroom]" value="1"'
			.($opz_param['external_private_subroom'] == '1' ? ' checked="checked"' : '' ).' />'._EXT_LV.'</label>';
	}
	echo '</fieldset>'

		.'</fieldset><br />'
		.'<input class="button" type="submit" value="'._SAVE.'" />';
	echo '</div>'
		.'</form>';
}

// XXX: level_field
function level_field( $name, $lv_perm, &$lv_available ) {

	reset($lv_available);
	while(list($num_lv, $name_lv) = each($lv_available)) {
		echo '<label><input type="checkbox" name="param['.$name.']['.$num_lv.']" value="1"'
			.( ( $lv_perm & (1 << $num_lv) ) ? ' checked="checked"' : '' ).' />'.$name_lv.'</label><br />';
	}
}

// XXX: calc_level
function calc_level( &$levels ) {
	funAdminAccess('modconfig', 'MOD');
	$final_num = 0;
	reset($levels);
	while( list($num_lv) = each($levels) ) {
		$final_num |=  (1 << $num_lv);
	}
	return $final_num;
}

// XXX: savechat
function savechat() {
	funAdminAccess('modconfig', 'MOD');
	global $prefixCms;

	$type_sel = (int)$_POST['type'];

	$lv_moderator = calc_level($_POST['param']['lv_moderator']);
	$lv_upload = calc_level($_POST['param']['lv_upload']);
	$lv_public = calc_level($_POST['param']['lv_public_subroom']);
	$lv_private = calc_level($_POST['param']['lv_private_subroom']);
	$lv_can_talk = calc_level($_POST['param']['lv_can_talk']);

	//print_r($_POST);

	if(!mysql_query("
	UPDATE ".$prefixCms."_config_chat
	SET active_room = '".(int)$_POST['param']['active_room']."',
		audio = '".(int)$_POST['param']['audio']."',
		video = '".(int)$_POST['param']['video']."',
		lv_moderator = '".$lv_moderator."',
		upload = '".(int)$_POST['param']['upload']."',
		lv_upload = '".$lv_upload."',
		external_upload = '"
	.( isset($_POST['param']['external_upload']) ? (int)$_POST['param']['external_upload'] : 0 )."',
		public_subroom = '".(int)$_POST['param']['public_subroom']."',
		lv_public_subroom = '".$lv_public."',
		external_public_subroom = '"
	.( isset($_POST['param']['external_public_subroom']) ? (int)$_POST['param']['external_public_subroom'] : 0 )."',
		private_subroom = '".(int)$_POST['param']['private_subroom']."',
		lv_private_subroom = '".$lv_private."',
		external_private_subroom = '"
	.( isset($_POST['param']['external_private_subroom']) ? (int)$_POST['param']['external_private_subroom'] : 0 )."',
		lv_can_talk = '".$lv_can_talk."'
	WHERE room_type = '$type_sel'
	LIMIT 1")) {
		jumpTo('admin.php?modname=config&op=config&opz=chat&type='.$type_sel.'&is_err=1');
	}
	jumpTo('admin.php?modname=config&op=config&opz=chat&type='.$type_sel.'&is_ok=1');
}

// XXX: savechat
function savegroup() {
	funAdminAccess('modconfig', 'MOD');
	global $prefixCms;

	$group_sel = (int)$_POST['group_sel'];

	$reSetting = mysql_query("
	SELECT param_name, value_type
	FROM ".$prefixCms."_setting
	WHERE regroup = '$group_sel' AND hide_in_modify = '0'");

	$re = true;
	while( list( $var_name, $value_type ) = mysql_fetch_row( $reSetting ) ) {

		switch( $value_type ) {
			//if is int cast it
			case "int" : {
				$new_value = (int)$_POST['option'][$var_name];
			};break;
			//if is enum switch value to on or off
			case "enum" : {
				if( $_POST['option'][$var_name] == 'on' ) $new_value = 'on';
				else $new_value = 'off';
			};break;
			//else simple assignament
			default : {
				$new_value = $_POST['option'][$var_name];
			}
		}

		if(!mysql_query("
		UPDATE ".$prefixCms."_setting
		SET param_value = '$new_value'
		WHERE param_name = '$var_name' AND regroup = '$group_sel'")) {
			$re = false;
		}
	}
	Header('Location:admin.php?modulename=config&op=config&opz='.$group_sel.'&is_ok='.( $re ? 1 : 0 ));
}

// XXX: switch
switch($op) {
	case "config" : {
		config();
	};break;

	case "savechat" : {
		savechat();
	};break;
	case "savegroup" : {
		savegroup();
	};break;
}

}


// -------------------- TEMP:



/**
 * function jumpTo
 *
 * @return string	 relative destination url (eg. index.php?...)
 * @return nothing
 *
 * @author Fabio Pirovano (fabio@docebo.com)
 **/
function jumpTo( $relative_url ) {

	Header('Location: http://'.$_SERVER['HTTP_HOST']
            .( strlen(dirname($_SERVER['PHP_SELF'])) != 1 ? dirname($_SERVER['PHP_SELF']) : '' )
			.'/'.trim($relative_url));
}



?>