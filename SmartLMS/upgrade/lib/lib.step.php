<?php


function sl_open_fileoperations_ftp() {
	$ftpuser = $GLOBALS['ftpuser'];
	$ftppass = $GLOBALS['ftppass'];
	$ftphost = $GLOBALS['ftphost'];
	$ftpport = $GLOBALS['ftpport'];

	$result = FALSE;
	
	if( isset($GLOBALS['ftptimeout']) ) {
		$timeout = $GLOBALS['ftptimeout'];
	} else {
		$timeout = ini_get('max_execution_time');
		if( $timeout == 0 ) {
			$timeout = 20;
		} elseif( $timeout > 60 ) {
			$timeout = 50;
		} else {
			$timeout = round(($timeout*8)/10);
		}
	}
	
	$GLOBALS['ftpConn'] = @ftp_connect( $ftphost, $ftpport, $timeout );
	if( $GLOBALS['ftpConn'] === FALSE ) {
		return FALSE;
	}
	if( @ftp_login($GLOBALS['ftpConn'], $ftpuser, $ftppass) )
		return TRUE;
	else
		return FALSE;
}

function sl_close_fileoperations_ftp() {
	ftp_close($GLOBALS['ftpConn']);
}


function chooseLang() {
	
	echo '<h3 class="title_area">'._CHOOSE_LANG.'</h3>';
	
	echo Form::openForm('choose_lang', 'index.php')
		.Form::getHidden('step', 'step', 'choose_start')
		.Form::openElementSpace()
		
		.Form::getDropdown(_LANG_SELECTION, 'sel_lang', 'sel_lang', DoceboUpgradeGui::getLanguageList("language"))

		.Form::closeElementSpace()
		.Form::openButtonSpace()
		
		.Form::getButton('upgrade', 'upgrade', _NEXTSTEP)
		
		.Form::closeButtonSpace()
		.Form::closeForm();
}

function chooseStart() {
	
	require_once($GLOBALS['where_upgrade'].'/lib/lib.upgrade_management.php');
	
	
	echo '<h3 class="title_area">'._TITLE_1OF2.'</h3>';
	
	$lang = DoceboUpgradeGui::getLanguage();
	$db_sorg = new DoceboSql($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass'], $GLOBALS['dbname']);
	
	$query = "
	SELECT param_value
	FROM ".$GLOBALS['prefix_fw']."_setting
	WHERE param_name = 'core_version'";
	list($version) = $db_sorg->fetchRow($db_sorg->querySingle($query));
	if($version =='3.5') $version = '3.5.0';
	$db_sorg->closeConn();
	if(version_compare($version, '3.5.0.2', '<=')) {
		
		echo 'In order to upgrade please copy this 3 rows in .config.php file before the ?'.'&gt; symbol:<br />'
			.'<br />'
			.'$GLOBALS[\'db_conn_names\'] = \'utf8\';<br />'
			.'$GLOBALS[\'db_conn_char_set\'] = \'utf8\';<br />'
			.'$GLOBALS[\'mail_br\'] = "\\r\\n";<br /><br /><br />';
	}
	if (version_compare(phpversion(), "5.0.0", "<")) {
		if(!function_exists('overload')) {
			
			echo '<strong class="error">'._YOU_DONT_HAVE_FUNCTION_OVERLOAD.'</strong><br />';
			return;
		}
		if (!extension_loaded('domxml')) {
			echo '<strong class="error">'._DOMXML_REQUIRED.'</strong><br />';
			return;
		}
	}
	
	$upd_man = new UpdateManager();
	if(!$upd_man->openXmlDescription($GLOBALS['where_upgrade'].'/lib/upgrade_info.xml')) {
		
		echo 'Error reading xml instructions : '.$GLOBALS['where_upgrade'].'/lib/upgrade_info.xml';
		return ;
	}
	$start_version_list = $upd_man->getStartVersionList();
	
	// Check some thigs
	$exists_old_dir 	= array();
	$dir_mustnot_exists = array('addons', 'admin', 'class.module', 'core', 'fileCourses', 
		'menu', 'modules', 'patch', 'templates');
	
	foreach($dir_mustnot_exists as $dir_name) {
		
		if(is_dir($GLOBALS['where_upgrade'].'/../'.$dir_name.'/')) {
			$exists_old_dir[] = $dir_name;
		}
	}
	if(!empty($exists_old_dir)) {
		
		echo '<strong class="error">'._IS_PRESENT_DIRECTORIES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$exists_old_dir).'</li></ul><br />';
		return;
	}
	
	// Check some thigs
	$lacking_dir 	= array();
	$dir_must_exists = array('doceboCore', 'doceboCms', 'doceboLms', 'doceboScs');
	foreach($dir_must_exists as $dir_name) {
		
		if(!is_dir($GLOBALS['where_upgrade'].'/../'.$dir_name.'/')) {
			$lacking_dir[] = $dir_name;
		}
	}
	if(!empty($lacking_dir)) {
		
		echo '<strong class="error">'._LACKING_DIRECTORIES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$lacking_dir).'</li></ul><br />';
		return;
	}
	
	// Write permission 
	$checked_dir 	= array();
	$dir_to_check = array(
		'files/common/users', 
		'files/doceboCore/photo', 
		'files/doceboLms/course',
		'files/doceboLms/forum', 
		'files/doceboLms/item', 
		'files/doceboLms/message', 
		'files/doceboLms/project', 
		'files/doceboLms/scorm', 
		'files/doceboLms/test' );
	foreach($dir_to_check as $dir_name) {
		
		if(!is_dir($GLOBALS['where_upgrade'].'/../'.$dir_name.'/')) {
			$checked_dir[] = $dir_name;
		} elseif(!is_writable($GLOBALS['where_upgrade'].'/../'.$dir_name.'/')) {
			$checked_dir[] = $dir_name;
		}
	}
	if(!empty($checked_dir)) {
		
		echo '<strong class="error">'._CHECKED_DIRECTORIES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$checked_dir).'</li></ul><br /><br />';
	}
	
	// Empty dir
	$empty_dir 	= array();
	$dir_to_check = array('files/doceboLms/course',
		'files/doceboLms/scorm');
	foreach($dir_to_check as $dir_name) {
		
		if(is_dir($GLOBALS['where_upgrade'].'/../'.$dir_name.'/')) {
			
			$i = 0;
			$tmp = dir($GLOBALS['where_upgrade'].'/../'.$dir_name.'/');
			while($elem = $tmp->read()) {
				
				if(($elem != ".") && ($elem != "..") && ($elem != "index.htm")) ++$i;
			}
			closedir($tmp->handle);
			if($i < 2) $empty_dir[] = $dir_name;
		}
	}
	if(!empty($empty_dir)) {
		
		echo '<strong class="error">'._EMPTY_DIRECTORIES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$empty_dir).'</li></ul><br />';
	}
	
	// control db connection
	$db_sorg = new DoceboSql($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass'], $GLOBALS['dbname'], false);
	if( !$db_sorg->isConnected() || !$db_sorg->isDbSelected() ) {
		
		echo '<strong class="error">'._CANT_CONNECT_WITH_DB.'</strong><br />';
		return;
	}
	// control ftp connectio
	if($GLOBALS['uploadType'] == 'ftp') {
		if(!sl_open_fileoperations_ftp()) {
			
			echo '<strong class="error">'._CANT_CONNECT_WITH_FTP.'</strong><br />';
			return;
		}
		sl_close_fileoperations_ftp();
	}
	
	
	require(dirname(__FILE__).'/iso_to_utf.php');
	$get_prev_econdig = PMA_getDbCollation($db_sorg, $GLOBALS['dbname']);
	$db_sorg->closeConn();
	
	if(substr($get_prev_econdig[0],0,4) != 'utf8' && $upd_man->getMaxEndVersion() == $version) {
	
		echo Form::openForm('upgrade', 'index.php')
			.Form::getHidden('step', 'step', 'changeutf' )
			
			.Form::openElementSpace()
			
			.Form::getLineBox(_START_VERSION, $version)
			.Form::getLineBox(_END_VERSION, $upd_man->getMaxEndVersion())
			
			.Form::closeElementSpace()
			
			.Form::openButtonSpace()
			.Form::getButton('next', 'next', 'convert in utf8')
			.Form::closeButtonSpace()
			.Form::closeForm();
	
	} elseif($upd_man->getMaxEndVersion() == $version) { 
	
		// Select from to
		echo Form::openForm('step2of3_toend', 'index.php')
			
			.Form::openElementSpace()
			.Form::getLineBox(_START_VERSION, $version)
			.Form::getLineBox(_END_VERSION, $upd_man->getMaxEndVersion())
			.Form::closeElementSpace()
			
			.Form::openButtonSpace()
			.Form::getHidden('step', 'step', 'loadlang')
			.Form::getButton('upgrade_add', 'upgrade_add', _NEXT_ONLY_ADD)
			.Form::closeButtonSpace()
			.Form::closeForm();
		
		echo Form::openForm('step2of3_tolang', 'index.php')
			
			.Form::openButtonSpace()
			.Form::getHidden('step', 'step', 'loadlang')
			.Form::getButton('upgrade_over', 'upgrade_over', _NEXT_OVERWRITELANG)
			.Form::closeButtonSpace()
			.Form::closeForm();
	} else {
	
		// Select from to
		echo Form::openForm('step1of2', 'index.php')
			.Form::getHidden('step', 'step', 'update')
			.Form::openElementSpace()
			
			.Form::getDropdown(_START_VERSION, 'start_version', 'start_version', $start_version_list, $version)
			.Form::getLineBox(_END_VERSION, $upd_man->getMaxEndVersion())
			
			.Form::closeElementSpace()
			.Form::openButtonSpace()
			
			.Form::getButton('upgrade', 'upgrade', _DOUPGRADE)
			
			.Form::closeButtonSpace()
			.Form::closeForm();
	}
}

function update() {
	
	require_once($GLOBALS['where_upgrade'].'/lib/lib.docebosql.php');
	require_once($GLOBALS['where_upgrade'].'/lib/class.upgrade_def.php');
	require_once($GLOBALS['where_upgrade'].'/lib/lib.upgrade_management.php');
	
	// open database connection
	$db_sorg = new DoceboSql($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass'], $GLOBALS['dbname']);
	
	$upd_man = new UpdateManager();
	if(!$upd_man->openXmlDescription($GLOBALS['where_upgrade'].'/lib/upgrade_info.xml')) {
		
		echo 'Error reading xml instructions : '.$GLOBALS['where_upgrade'].'/lib/upgrade_info.xml';
	}
	
	echo '<h3 class="title_area">'._TITLE_2OF2.'</h3>';
	// Module description 
	$modules = $upd_man->getModulesDescriptors();
	
	// version to travel with
	$version_list = $upd_man->getVersionListFrom($_POST['start_version']);
	
	foreach($version_list as $version) {
		
		$step_sequence = $upd_man->getVersionStepSequence($version);
		
		echo '<h4 class="title_area">'._UPGRADING_VERSION.$version.'</h4>'
			.'<ul>';
		$error_occurred = false;
		foreach($step_sequence as $module_id) {
			
			require_once($GLOBALS['where_upgrade'].'/class_'.$modules[$module_id]['platform'].'/'.$modules[$module_id]['class_file']);
			
			$class_name = $modules[$module_id]['class_name'];
			$upd = new $class_name();
			
			$upd->setDbMan($db_sorg);
			$re = $upd->oneStepUpgrade($version);
			
			if(is_array($re)) {
				
				echo '<li><strong class="upd_error">'._FAILED_OPERATION.'</strong>'.$re['error_code'].'<br />'
					.$re['error_msg']
					.'</li>'
					.'</ul>';
				$error_occurred = true;
			} else {
				
				echo '<li><strong class="upd_ok">'._SUCCESSFULL_OPERATION.'</strong>'.$module_id.'</li>';
			}
			
		}
		echo '</ul>';
	}
	if(!$error_occurred) {
			
		require(dirname(__FILE__).'/iso_to_utf.php');
		$db_conn = new DoceboSql($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass'], $GLOBALS['dbname']);
		$get_prev_econdig = PMA_getDbCollation($db_conn, $GLOBALS['dbname']);
		$db_conn->closeConn();
		
		if( substr($get_prev_econdig[0],0,4) != 'utf8' ) {
		
			echo Form::openForm('upgrade', 'index.php')
				.Form::getHidden('step', 'step', 'changeutf' )
				
				.Form::openButtonSpace()
				.Form::getButton('next', 'next', 'convert in utf8')
				.Form::closeButtonSpace()
				.Form::closeForm();
		} else {
		
  		echo Form::openForm('step2of3_toend', 'index.php')
  			.Form::getHidden('step', 'step', 'loadlang')
  			
  			.Form::openButtonSpace()
  			.Form::getButton('upgrade_add', 'upgrade_add', _NEXT_ONLY_ADD)
  			.Form::closeButtonSpace()
  			.Form::closeForm();
  		
  		echo Form::openForm('step2of3_tolang', 'index.php')
  			.Form::getHidden('step', 'step', 'loadlang')
  			
  			.Form::openButtonSpace()
  			.Form::getButton('upgrade_over', 'upgrade_over', _NEXT_OVERWRITELANG)
  			.Form::closeButtonSpace()
  			.Form::closeForm();
	   }
	}
}

function changeutf() {

	require(dirname(__FILE__).'/iso_to_utf.php');

	echo '<h3 class="title_area">'._CONVERT_TO_UTF.'</h3>'
			.'<p>'._CONVERT_TO_UTF_COMMENT.'</p>';
	
	ob_end_flush();
	$db_conn = new DoceboSql($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass'], $GLOBALS['dbname']);
	
	/* 
	
	$get_prev_econdig = PMA_getDbCollation($db_conn, $GLOBALS['dbname']);
	if( substr($get_prev_econdig[0],0,4) == 'utf8' ) {
		
		$report = convert_utf(	$db_conn, 
								$GLOBALS['dbname'],
								
								'latin1_swedish_ci',
								'latin1',
								true );
		
	}
	*/
	$report = convert_utf(	$db_conn, 
							$GLOBALS['dbname'],
							
							'utf8_general_ci',
							'utf8' );
	
	$db_conn->closeConn();
	ob_start();
	echo $report;
	
	echo Form::openForm('step2of3_toend', 'index.php')
		.Form::getHidden('step', 'step', 'loadlang')
		.Form::openButtonSpace()
		
		.Form::getButton('upgrade_add', 'upgrade_add', _NEXT_ONLY_ADD)
		
		.Form::closeButtonSpace()
		.Form::closeForm();
	
	echo Form::openForm('step2of3_tolang', 'index.php')
		.Form::getHidden('step', 'step', 'loadlang')
		
			
		.Form::openButtonSpace()
		
		.Form::getButton('upgrade_over', 'upgrade_over', _NEXT_OVERWRITELANG)
		
		.Form::closeButtonSpace()
		.Form::closeForm();
}

function loadLang() {
	
	require_once($GLOBALS['where_upgrade'].'/lib/lib.docebosql.php');
	require_once($GLOBALS['where_upgrade'].'/lib/lib.lang.php');
	require(dirname(__FILE__).'/iso_to_utf.php');
	
	$langlist = DoceboUpgradeGui::getLanguageList();
	
	$flipped = array_flip($langlist);
	
	$text = '<h3 class="title_area">'._TITLE_STEP3.'</h3>';
	$text.= '<script type="text/javascript">';
	$text.= "	var language = new Array('".implode("','", $langlist)."');";
	$text.= "	var platform = new Array('framework', 'scs', 'lms', 'ecom', 'cms');";
	
	if(isset($_POST['upgrade_over'])) $text.= " var overwrite = 'yes'; ";
	else $text.= " var overwrite = 'no'; ";
	
	$text.= "	YAHOO.util.Event.onDOMReady(setup, true);";
	$text.= "	var next_lang = '"._NEXT."';";
	$text.= '</script>';
	
	$text.= '<div>'
			.'<button type="submit" '
				."\n\t".'class="button" '
				."\n\t".'id="start" '
				."\n\t".'name="start" '
				."\n\t".'onclick="load_lang(); return false;" '
				."\n\t".'value="Start">Start</button>'."\n"
			.'</div>';
	
	$text.= '<table id="table_list" class="my_table">'
		.'	<tbody id="lang-list">'
		.'		<tr>'
		.'		    <th class="image"></th>'
		.'		    <th class="colum">Language</th>'
		.'		    <th class="colum">Platform</th>'
		.'		    <th>Status</th>'
		.'		</tr>'
		.'	</tbody>'
		.'</table>';
	
	$text.= Form::openForm('step3of3_utf', 'index.php')
		.Form::getHidden('step', 'step', 'end')
		.Form::openButtonSpace()
		
		.'<div id="next_button">'
		.'<button type="submit" '
				."\n\t".'class="button" '
				."\n\t".'id="start" '
				."\n\t".'name="start" '
				."\n\t".'onclick="load_lang(); return false;" '
				."\n\t".'value="Start">Start</button>'."\n"
		.'</div>'
		
		//.Form::getButton('next', 'next', _NEXT, 'button')
		
		.Form::closeButtonSpace()
		.Form::closeForm();
		
	echo $text;
}

function endstep() {
	
	$text ='<h3 class="title_area">'._ENDSTEP.'</h3>'
			.'<p>'._END_PHRASE.'</p>';
	echo $text;
}

// XXX: switch
function dispatchStep($step) {
	
	switch($step) {
		case "end" : endstep();break;
		case "changeutf" : changeutf();break;
		case "loadlang" : loadLang();break;
		case "update" : update();break;
		case "choose_start" : chooseStart();break;
		default : chooseLang();break;
	}
}

/*

OLD LOAD LANG

	$goto_next_step = FALSE;
	
	$platform_code 	= end(array_keys($_SESSION["lang_to_load"]));
	$lang 			= array_pop($_SESSION["lang_to_load"][$platform_code]);
	
	if(count($_SESSION["lang_to_load"][$platform_code]) < 1) {
		
		array_pop($_SESSION["lang_to_load"]);
	}
	if (count($_SESSION["lang_to_load"]) < 1) {
		// all language loaded
		$goto_next_step = TRUE;
	}
	
	$text ='<h3 class="title_area">'._TITLE_STEP3.'</h3>'
		.Form::openForm('upgrade', 'index.php')
		.Form::openElementSpace()
		
		._LANG_INSTALLED." ("._LANGUAGE.": <b>".$lang."</b>, "._PLATFORM." : <b>".$platform_code."</b>)";
	
	$fn=$GLOBALS['where_upgrade'].'/../xml_language/platform['.$platform_code.']_lang['.$lang.'].xml';
	
	if(file_exists($fn)) lang_importXML($fn, $overwrite);
	else $text .= '<p>'._LANGUAGE_NOT_FOUND.'<p>';

	$GLOBALS["db"]->closeConn();

	if(isset($_POST['lastutf']) && $goto_next_step) {
		
		$last_step = 'changeutf';
		
		$text .= Form::getHidden('step', 'step', 'changeutf' )
			.( isset($_POST['upgrade_over'])
				? Form::getHidden('upgrade_over', 'upgrade_over', '1' )
				: '' )
			.( isset($_POST['lastutf'])
				? Form::getHidden('lastutf', 'lastutf', 'true' )
				: '' )
			.Form::closeElementSpace()
			
			.Form::openButtonSpace()
			.Form::getButton('next', 'next', 'convert in utf8')
			.Form::closeButtonSpace()
			.Form::closeForm();
			
		$text .= Form::openForm('goto_toend', 'index.php')
			.Form::getHidden('step', 'step', 'end' )
			.( isset($_POST['upgrade_over'])
				? Form::getHidden('upgrade_over', 'upgrade_over', '1' )
				: '' )
			.Form::closeElementSpace()
			
			.Form::openButtonSpace()
			.Form::getButton('next', 'next', _NEXT)
			.Form::closeButtonSpace()
			.Form::closeForm();
		
	} else {
		
		$text .= Form::getHidden('step', 'step', ( !$goto_next_step ? 'loadlang' : 'end' ) )
			.( isset($_POST['upgrade_over'])
				? Form::getHidden('upgrade_over', 'upgrade_over', '1' )
				: '' )
			.( isset($_POST['lastutf'])
				? Form::getHidden('lastutf', 'lastutf', 'true' )
				: '' )
			.Form::closeElementSpace()
			
			.Form::openButtonSpace()
			.Form::getButton('next', 'next', _NEXT)
			.Form::closeButtonSpace()
			.Form::closeForm();
	}
*/

?>
