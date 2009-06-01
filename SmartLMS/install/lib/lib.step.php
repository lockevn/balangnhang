<?php

$GLOBALS["path_to_root"] = "../";
//$GLOBALS["path_to_root"] = "../../";

function sl_open_fileoperations_ftp() {
	$ftpuser = $_SESSION['ftpuser'];
	$ftppass = $_SESSION['ftppass'];
	$ftphost = $_SESSION['ftphost'];
	$ftpport = $_SESSION['ftpport'];

	$result = FALSE;

	if( isset($_SESSION['ftptimeout']) ) {
		$timeout = $_SESSION['ftptimeout'];
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

	if (!function_exists("ftp_connect"))
		return 0;

	$_SESSION['ftpConn'] = @ftp_connect( $ftphost, $ftpport, $timeout );
	if( $_SESSION['ftpConn'] === FALSE ) {
		return FALSE;
	}
	if( @ftp_login($_SESSION['ftpConn'], $ftpuser, $ftppass) )
		return TRUE;
	else
		return FALSE;
}

function sl_close_fileoperations_ftp() {
	if (function_exists("ftp_close"))
		@ftp_close($_SESSION['ftpConn']);
}


function getPlatformArray() {
	return array('framework'=>'doceboCore', 'cms'=>'doceboCms', 'lms'=>'doceboLms', 'scs'=>'doceboScs', 'ecom'=>'doceboEcom', 'crm'=>'doceboCrm');
}

function step_1() {

	$text ='<h3 class="title_area">'._TITLE_STEP1.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step2')
		.Form::openElementSpace();

	$text .="<h3>"._SELECT_LANGUAGE."</h3>\n";

	$text.=Form::getDropdown(_LANGUAGE, 'sel_lang', 'sel_lang', DoceboUpgradeGui::getLanguageList("language"));

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('next', 'next', _NEXT)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}

function check_step_1() {

	$data_ok = true;
	
	$php_conf = ini_get_all();
	
	if (version_compare(phpversion(), "5.0.0", "<")) {
		if(!function_exists('overload')) {

			$msg = _YOU_DONT_HAVE_FUNCTION_OVERLOAD;
			$data_ok = false;
		}
		if (!extension_loaded('domxml')) {
			$data_ok = false;
			$msg=_DOMXML_REQUIRED;
		}
	}
	if (version_compare(phpversion(), "5.2.0", ">"))
	{
		if($php_conf['allow_url_include']['local_value'])
			$msg=_ALLOW_URL_INCLUDE;
	}
	if(!$data_ok) {
		$text ='<h3 class="title_area">'._CRITICAL_ERROR.'</h3>';

		$text.=Form::openForm('installform', 'index.php')
			.Form::getHidden('step', 'step', 'step1')

			.$msg

			.Form::openButtonSpace()

			.Form::getButton('back', 'back', _BACK)

			.Form::closeButtonSpace()
			.Form::closeForm();

		echo $text;
	}
	else {
		step_2();
	}

}

function step_2() {

	$text ='<h3 class="title_area">'._TITLE_STEP2.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step3')
		.Form::openElementSpace();

	$content="";
	$fn = $GLOBALS['where_upgrade']."/data/license/license_".DoceboUpgradeGui::getLanguage().".txt";
	$english_fn = $GLOBALS['where_upgrade']."/data/license/license_english.txt";

	$handle=FALSE;
	if ((!file_exists($fn)) && (file_exists($english_fn))) {
		$fn=$english_fn;
	}

	if (file_exists($fn)) {
		$handle = fopen($fn, "r");
		$content = fread($handle, filesize($fn));
		fclose($handle);
	}

	//$text.=Form::getSimpleTextarea(_SOFTWARE_LICENSE, "license", "license", $content);

	$text.='<label for="license">'._SOFTWARE_LICENSE.'</label><br />';
	$text.="<textarea rows=\"20\" cols=\"72\" id=\"license\" name=\"license\" readonly=\"readonly\">";
	$text.=htmlspecialchars($content)."</textarea>\n";

	$text.=Form::getCheckbox(_AGREE_LICENSE, "agree", "agree", 1, false);

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('next', 'next', _NEXT)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}


function check_step_2()
{
	$data_ok=true;

	if ((!isset($_POST["agree"])) || (empty($_POST["agree"])))
		$data_ok=false;

	$msg=_MUST_ACCEPT_LICENSE;

	if (!$data_ok) {

		$text ='<h3 class="title_area">'.$msg.'</h3>';

		$text.=Form::openForm('installform', 'index.php')
			.Form::getHidden('step', 'step', 'step2')

			.Form::openButtonSpace()

			.Form::getButton('back', 'back', _BACK)

			.Form::closeButtonSpace()
			.Form::closeForm();

		echo $text;
	}
	else {
		step_3();
	}

}


function step_3() {

	echo '<h3 class="title_area">'._TITLE_STEP3.'</h3>'
		.server_info();

	// Check some thigs
	$exists_old_dir 	= array();
	$dir_mustnot_exists = array('addons', 'admin', 'class.module', 'core', 'fileCourses',
		'menu', 'modules', 'patch', 'templates');

	foreach($dir_mustnot_exists as $dir_name) {

		if(is_dir($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/')) {
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
	$dir_must_exists = array('doceboCore', 'doceboScs');
	//array('doceboCore', 'doceboCms', 'doceboKms', 'doceboLms', 'doceboScs');
	foreach($dir_must_exists as $dir_name) {

		if(!is_dir($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/')) {
			$lacking_dir[] = $dir_name;
		}
	}
	if(!empty($lacking_dir)) {

		echo '<strong class="error">'._LACKING_DIRECTORIES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$lacking_dir).'</li></ul><br />';
		return;
	}

	$platform_folders=getPlatformArray();
	$file_to_check=array("config.php");
	$dir_to_check=array();
	$empty_dir_to_check=array();

	foreach($platform_folders as $platform_code=>$dir_name) {

		$specific_file_to_check =array();
		$specific_dir_to_check =array();

		if(!is_dir($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/')) {

			$install[$platform_code]=FALSE;

		}
		else {
			$install[$platform_code]=TRUE;

			$empty_specific_dir_to_check=NULL;

			switch ($platform_code) {

				case "lms": {
					$specific_dir_to_check = array(
						'files/doceboLms/course',
						'files/doceboLms/forum',
						'files/doceboLms/item',
						'files/doceboLms/message',
						'files/doceboLms/project',
						'files/doceboLms/scorm',
						'files/doceboLms/test' );
					$empty_specific_dir_to_check = array('files/doceboLms/course', 'files/doceboLms/scorm');
				} break;

				case "framework": {
					$specific_dir_to_check = array("files/doceboCore/photo", "files/common/users");
				} break;

				case "cms": {
					$specific_dir_to_check = array(
						'doceboCms/addons/bbclone/var',
						'files/doceboCms/banners',
						'files/doceboCms/docs',
						'files/doceboCms/forum',
						'files/doceboCms/links',
						'files/doceboCms/media',
						'files/doceboCms/topic');
					$specific_file_to_check =array(
						'doceboCms/addons/bbclone/var/last.php',
						'doceboCms/addons/bbclone/var/access.php',
						'doceboCms/addons/bbclone/var/.htalock');
					for($i=0; $i<=15; $i++) {
						$specific_file_to_check[]='doceboCms/addons/bbclone/var/counter'.$i.'.inc';
					}
				} break;
			}

			$dir_to_check=array_merge($dir_to_check, $specific_dir_to_check);
			$file_to_check =array_merge($file_to_check , $specific_file_to_check);

			if ((is_array($specific_dir_to_check)) && (count($specific_dir_to_check) > 0) && (is_array($empty_specific_dir_to_check)))
				$empty_dir_to_check=array_merge($empty_dir_to_check, $empty_specific_dir_to_check);
		}
	}

	// Write permission
	$checked_dir 	= array();
	foreach($dir_to_check as $dir_name) {

		if(!is_dir($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/')) {
			$checked_dir[] = $dir_name;
		} elseif(!is_writable($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/')) {
			$checked_dir[] = $dir_name;
		}
	}
	if(!empty($checked_dir)) {

		echo '<strong class="error">'._CHECKED_DIRECTORIES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$checked_dir).'</li></ul><br /><br />';
	}

	$checked_file 	= array();
	foreach($file_to_check as $file_name) {
		if(!is_writable($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$file_name)) {
			$checked_file[] = $file_name;
		}
	}
	if(!empty($checked_file)) {

		echo '<strong class="error">'._CHECKED_FILES.'</strong>'
			.'<ul><li>'.implode('</li><li>',$checked_file).'</li></ul><br /><br />';
	}

	// Empty dir
	/* $empty_dir 	= array();

	foreach($empty_dir_to_check as $dir_name) {

		if(is_dir($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/')) {

			$i = 0;
			$tmp = dir($GLOBALS['where_upgrade'].'/'.$GLOBALS["path_to_root"].$dir_name.'/');
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
	} */

	// -- Refresh button:  -------------------------------
	$text =Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step3');

	$text.=Form::getHidden("agree", "agree", $_POST["agree"]);

	$text.=Form::openButtonSpace()

		.Form::getButton('refresh', 'refresh', _REFRESH)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
	// ---------------------------------------------------

	// Select from to
	echo Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step4')
		.Form::openElementSpace();

	$text ="<h3>"._SELECT_WHATINSTALL."</h3>\n";

	foreach ($install as $platform_code=>$val) {

		if ($val) {

			if (($platform_code == "framework") || ($platform_code == "scs") || ($platform_code == "crm")) {
				$other_param="disabled=\"disabled\"";
				$checked=true;
				$show=false;
			}
			else {
				$other_param="";
				$checked=true;
				$show=true;
			}

			if ($show) {
				$label=constant("_".strtoupper($platform_code));
				$id="install_".$platform_code;
				$name="install[".$platform_code."]";
				$text.=Form::getCheckbox($label, $id, $name, 1, $checked, $other_param);
			}
		}
	}

	$text.="<p>"._WARNING_NOT_INSTALL."</p>";

	echo $text;

	echo Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('next', 'next', _NEXT)

		.Form::closeButtonSpace()
		.Form::closeForm();
}


function check_step_3() {

	$data_ok=true;

	if (version_compare(phpversion(), "5.0.0", "<")) {
		if (!extension_loaded('domxml')) {
			$data_ok=false;
			$msg=_DOMXML_REQUIRED;
		}
	}

	if (!$data_ok) {

		$text ='<h3 class="title_area">'.$msg.'</h3>';

		$text.=Form::openForm('installform', 'index.php')
			.Form::getHidden('step', 'step', 'step2')

			.Form::openButtonSpace()

			.Form::getButton('back', 'back', _BACK)

			.Form::closeButtonSpace()
			.Form::closeForm();

		echo $text;
	}
	else {
		step_4();
	}

}


function step_4() {

	$text ='<h3 class="title_area">'._TITLE_STEP4.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step5')
		.Form::openElementSpace();

	if ((isset($_POST["install"])) && (is_array($_POST["install"]))) {
		$_SESSION["install"]=$_POST["install"];
		$_SESSION["install"]["framework"]="1";
		$_SESSION["install"]["scs"]="1";
	}

	$text .="<h3>"._DATABASE_INFO.":</h3>\n";

	$text.=Form::getTextfield(_DB_HOST, "db_host", "db_host", 255, "localhost");
	$text.=Form::getTextfield(_DB_NAME, "db_name", "db_name", 255);
	$text.=Form::getTextfield(_DB_USERNAME, "db_user", "db_user", 255);
	$text.=Form::getPassword(_DB_PASS, "db_pass", "db_pass", 255);
	$text.=Form::getPassword(_DB_CONFPASS, "db_confpass", "db_confpass", 255);

	$text .="<h3>"._UPLOAD_METHOD.":</h3>\n";

	$safemode_is_on=ini_get("safe_mode");

	if ($safemode_is_on) {
		/* $text.='<input class="radio" type="radio" id="http_upload" name="upload_method" value="http" disabled="disabled" />';
		$text.='<label class="label_bold" for="http_upload">'._HTTP_UPLOAD.' ('._NOTAVAILABLE.')'.'</label>'; */
		$text.=Form::getRadio(_HTTP_UPLOAD.' ('._NOTAVAILABLE.')', "http_upload", "upload_method", "http", !$safemode_is_on);
	}
	else
		$text.=Form::getRadio(_HTTP_UPLOAD, "http_upload", "upload_method", "http", !$safemode_is_on);

	$text.=Form::getRadio(_FTP_UPLOAD, "ftp_upload", "upload_method", "ftp", $safemode_is_on);

	$text .="<h3>"._FTP_INFO.":</h3>\n";
	$text.=Form::getTextBox(_IF_FTP_SELECTED);

	$text.=Form::getTextfield(_FTP_HOST, "ftp_host", "ftp_host", 255, "localhost");
	$text.=Form::getTextfield(_FTP_PORT, "ftp_port", "ftp_port", 255, "21");
	$text.=Form::getTextfield(_FTP_USERNAME, "ftp_user", "ftp_user", 255);
	$text.=Form::getPassword(_FTP_PASS, "ftp_pass", "ftp_pass", 255);
	$text.=Form::getPassword(_FTP_CONFPASS, "ftp_confpass", "ftp_confpass", 255);
	$text.=Form::getTextfield(_FTP_PATH, "ftp_path", "ftp_path", 255, "/");

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('next', 'next', _NEXT)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}


function check_step_4() {

	$data_ok=true;

	//$_POST['upload_method']="http"; // TEMP
	$_SESSION["upload_method"]=$_POST["upload_method"];

	$msg="";
	if ($data_ok) {

		$_SESSION['dbhost']=$_POST["db_host"];
		$_SESSION['dbuname']=$_POST["db_user"];
		$_SESSION['dbpass']=$_POST["db_pass"];
		$_SESSION['dbname']=$_POST["db_name"];

		// control db connection
		$db_sorg = new DoceboSql($_SESSION['dbhost'], $_SESSION['dbuname'], $_SESSION['dbpass'], $_SESSION['dbname'], false);
		if( !$db_sorg->isConnected() || !$db_sorg->isDbSelected() ) {

			$msg='<strong class="error">'._CANT_CONNECT_WITH_DB.'</strong><br />';

			$data_ok=FALSE;
		}
		if ($db_sorg->isConnected())
			$db_sorg->closeConn();

		// control ftp connection
		if($_POST['upload_method'] == 'ftp') {

			$_SESSION["ftphost"]=$_POST["ftp_host"];
			$_SESSION["ftpport"]=$_POST["ftp_port"];
			$_SESSION["ftpuser"]=$_POST["ftp_user"];
			$_SESSION["ftppass"]=$_POST["ftp_pass"];
			$_SESSION["ftppath"]=$_POST["ftp_path"];

			if(!sl_open_fileoperations_ftp()) {

				$msg='<strong class="error">'._CANT_CONNECT_WITH_FTP.'</strong><br />';
				$data_ok=FALSE;
			}
			sl_close_fileoperations_ftp();
		}
	}

	//$data_ok=true; // TEMP

	if (!$data_ok) {

		$text ='<h3 class="title_area">'.$msg.'</h3>';

		$text.=Form::openForm('installform', 'index.php')
			.Form::getHidden('step', 'step', 'step4')

			.Form::openButtonSpace()

			.Form::getButton('back', 'back', _BACK)

			.Form::closeButtonSpace()
			.Form::closeForm();

		echo $text;
	}
	else {
		step_5();
	}

}


function step_5() {

	$text ='<h3 class="title_area">'._TITLE_STEP5.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step6')
		.Form::openElementSpace();

	$text.=Form::getOpenFieldset(_LANG_TO_INSTALL);

	$lang_arr=DoceboUpgradeGui::getLanguageList("language");
	$sel_lang=DoceboUpgradeGui::getLanguage();

	foreach ($lang_arr as $key=>$val) {

		if ($val == $sel_lang) {
				$other_param="disabled=\"disabled\"";
				$checked=true;
			}
			else {
				$other_param="";
				$checked=false;
			}

			$label=ucfirst($val);
			$id="lang_install_".$val;
			$name="lang_install[".$val."]";
			$text.=Form::getCheckbox($label, $id, $name, 1, $checked, $other_param);
	}

	$text.=Form::getCloseFieldset();

	$text.=Form::getOpenFieldset(_NUMBER_ESTIMATED_USERS);

	$text.=Form::getRadio(_LESS_THAN50, "estimated_less50", "estimated_users", "less50", true);
	$text.=Form::getRadio(_LESS_THAN150, "estimated_less150", "estimated_users", "less150", false);
	$text.=Form::getRadio(_MORE_THAN150, "estimated_more150", "estimated_users", "more150", false);

	$text.=Form::getCloseFieldset();

	$text.=Form::getOpenFieldset(_MORE_THAN_ONE_BRANCH);

	$text.=Form::getRadio(_ANSWER_YES, "many_branches_yes", "many_branches", "yes", false);
	$text.=Form::getRadio(_ANSWER_NO, "many_branches_no", "many_branches", "no", true);

	$text.=Form::getCloseFieldset();

	$text.=Form::getOpenFieldset(_ADMINISTRATION_TYPE);

	$text.=Form::getRadio(_ONE_ADMIN, "admin_type_one", "admin_type", "one", true);
	$text.=Form::getRadio(_SUB_ADMINS, "admin_type_many", "admin_type", "many", false);

	$text.=Form::getCloseFieldset();

	//$text.=Form::getOpenFieldset(_REQUIRE_ACCESSIBILITY);

	//$text.=Form::getRadio(_ANSWER_YES, "req_accessibility_yes", "req_accessibility", "yes", false);
	$text.=Form::getHidden("req_accessibility_no", "req_accessibility", "no");

	//$text.=Form::getCloseFieldset();

	$text.=Form::getOpenFieldset(_REGISTRATION_TYPE);

	$text.=Form::getRadio(_REG_TYPE_FREE, "reg_type_self", "reg_type", "self", false);
	$text.=Form::getRadio(_REG_TYPE_MOD, "reg_type_mod", "reg_type", "moderate", false);
	$text.=Form::getRadio(_REG_TYPE_ADMIN, "reg_type_admin", "reg_type", "admin", true);

	$text.=Form::getCloseFieldset();

	$spec_code="";
	foreach ($_SESSION["install"] as $platform_code=>$do_install) {
		if ($do_install) {
			$spec_code=getSpecificCode($platform_code, "getSimpleInterfaceOptions");
		}
	}

	if (!empty($spec_code)) {
		$text.=Form::getOpenFieldset(_SIMPLIFIED_INTERFACE);
		$text.=$spec_code;
		$text.=Form::getCloseFieldset();
	}

	$text.=Form::getOpenFieldset(_ADMIN_USER_INFO);
	$text.=Form::getTextfield(_ADMIN_USERNAME, "admin_user", "admin_user", 255);
	$text.=Form::getPassword(_ADMIN_PASS, "admin_pass", "admin_pass", 255);
	$text.=Form::getPassword(_ADMIN_CONFPASS, "admin_confpass", "admin_confpass", 255);
	$text.=Form::getTextfield(_ADMIN_EMAIL, "admin_email", "admin_email", 255);
	$text.=Form::getCloseFieldset();

	$text.=Form::getOpenFieldset(_WEBSITE_INFO);

	$platform_arr=array();
	foreach ($_SESSION["install"] as $platform_code=>$do_install) {
		if (($do_install) && ($platform_code !== "framework") && ($platform_code !== "ecom")) {
			$platform_arr[$platform_code]=constant("_".strtoupper($platform_code));
		}
	}
	if (isset($platform_arr["scs"]))
		unset($platform_arr["scs"]);

	$text.=Form::getDropdown(_DEFAULT_PLATFORM, 'default_platform', 'default_platform', $platform_arr, 'lms');

	$https=(isset($_SERVER["HTTPS"]) ? $_SERVER["HTTPS"] : FALSE);
	$baseurl=($https ? "https://" : "http://").$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF'])."/";
	$baseurl=preg_replace("/install\\/$/", "", $baseurl);
	$text.=Form::getTextfield(_SITE_DEFAULT_SENDER, "default_sender_email", "default_sender_email", 255);
	$text.=Form::getTextfield(_SITE_BASE_URL, "site_base_url", "site_base_url", 255, $baseurl);
	$text.=Form::getCloseFieldset();

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('next', 'next', _NEXT)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}


function check_step_5() {

	if ((isset($_POST["lang_install"])) && (is_array($_POST["lang_install"])))
		$_SESSION["lang_install"]=array_keys($_POST["lang_install"]);
	else
		$_SESSION["lang_install"]=array();
	$_SESSION["lang_install"][]=DoceboUpgradeGui::getLanguage();

	if (isset($_SESSION["lang_to_load"]))
		unset($_SESSION["lang_to_load"]);

	foreach ($_SESSION["install"] as $platform_code=>$do_install) {

		if ($do_install) {
			$_SESSION["lang_to_load"][$platform_code]=array();

			foreach($_SESSION["lang_install"] as $lang) {
				$_SESSION["lang_to_load"][$platform_code][$lang]=$lang;
			}
		}
	}

	$data_ok=TRUE;

	if (!empty($_POST["admin_user"]))
		$_SESSION["admin_user"]=$_POST["admin_user"];
	else {
		$msg=_INVALID_USERNAME;
		$data_ok=FALSE;
	}


	if ((!empty($_POST["admin_pass"])) && ($_POST["admin_pass"] == $_POST["admin_confpass"]))
		$_SESSION["admin_pass"]=$_POST["admin_pass"];
	else {
		$msg=_INVALID_PASSWORD;
		$data_ok=FALSE;
	}

		$_SESSION["admin_email"]=$_POST["admin_email"];

		$_SESSION["default_sender_email"]=$_POST["default_sender_email"];

	if ((!empty($_POST["site_base_url"])) && (valid_url($_POST["site_base_url"])))
		$_SESSION["site_base_url"]=$_POST["site_base_url"];
	else {
		$msg=_INVALID_SITEBASEURL;
		$data_ok=FALSE;
	}

	foreach ($_SESSION["install"] as $platform_code=>$do_install) {
		if ($do_install) {
			$check=getSpecificCode($platform_code, "checkSimpleInterfaceOptions", "-1");
			if (($check != "-1") && (!$check)) {
				$data_ok=FALSE;
				if (isset($GLOBALS["msg"]))
					$msg=$GLOBALS["msg"];
				else
					$msg="";
			}
		}
	}

	if (!$data_ok) {

		$text ='<h3 class="title_area">'.$msg.'</h3>';

		$text.=Form::openForm('installform', 'index.php')
			.Form::getHidden('step', 'step', 'step5_reload')

			.Form::openButtonSpace()

			.Form::getButton('back', 'back', _BACK)

			.Form::closeButtonSpace()
			.Form::closeForm();

		echo $text;
	}
	else {

		$_SESSION["estimated_users"]=$_POST["estimated_users"];
		$_SESSION["many_branches"]=$_POST["many_branches"];
		$_SESSION["admin_type"]=$_POST["admin_type"];
		$_SESSION["req_accessibility"]=$_POST["req_accessibility"];
		$_SESSION["reg_type"]=$_POST["reg_type"];
		$_SESSION["default_platform"]=$_POST["default_platform"];

		foreach ($_SESSION["install"] as $platform_code=>$do_install) {
			if ($do_install) {
				$save=getSpecificCode($platform_code, "saveSimpleInterfaceOptions");
			}
		}

		step_6();
	}
}


function valid_email($email) {
	return preg_match("/^[A-Z0-9\\._%-]+@[A-Z0-9\\._%-]+\\.[A-Z]{2,10}$/i", $email);
}


function valid_url($url) {
	// Url must end with "/"
	return preg_match("/^(http|https):\\/\\/(.*?)\\/$/i", $url);
}


function step_6() {

	$text ='<h3 class="title_area">'._TITLE_STEP6.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'step7')
		.Form::openElementSpace();

	$db_sorg = new DoceboSql($_SESSION['dbhost'], $_SESSION['dbuname'], $_SESSION['dbpass'], $_SESSION['dbname'], false);

	$sq='ALTER DATABASE `'.$_SESSION['dbname']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	$db_sorg->query($sq);

	$res=TRUE;

	// -- Finding mysql version -------------------
	$qtxt="SELECT VERSION()";
	$q=$db_sorg->query($qtxt);
	$version=mysql_result($q, 0);
	$match=array();
	preg_match("/^\\d+\\.\\d+/", $version, $match);
	$mysql_ver=$match[0]*100;
	// --------------------------------------------

	$platform_arr=getPlatformArray();
	foreach ($platform_arr as $platform_code=>$platform_folder) {

		$fn = $GLOBALS['where_upgrade']."/data/sql/".$platform_code.".sql";

		if (file_exists($fn)) {

			$handle = fopen($fn, "r");
			$content = fread($handle, filesize($fn));
			fclose($handle);

			// This two regexp works fine; don't edit them! :)
			$content=preg_replace("/--(.*)[^\$]/", "", $content);
			$sql_arr=preg_split("/;([\s]*)[\n\r]/", $content);

			foreach ($sql_arr as $sql) {
				$qtxt=trim($sql);

				if (!empty($qtxt)) {
/*
					// ----- MySQL < 4.1 compatibility: ---------
					if ($mysql_ver < 410) {
						$qtxt = str_replace("DEFAULT CHARSET=utf8", "", $qtxt);
						$qtxt = str_replace("character set utf8 collate", "", $qtxt);
						$qtxt = str_replace("utf8_bin", "binary", $qtxt);
						$qtxt = str_replace("ENGINE=MyISAM", "TYPE=MyISAM", $qtxt);
					}
					// ------------------------------------------
*/
					$q=$db_sorg->query($qtxt);
					if (!$q) {
						$text.=$db_sorg->error();
						$res=FALSE;
					}
				}
			}
		}
	}

	if ($res) {
		$text.=_DB_IMPORT_OK;

		$lang_install=$_SESSION["lang_install"];
		$lang_arr=DoceboUpgradeGui::getLanguageList("language");

		foreach ($lang_arr as $language) {
			if (!in_array($language, $lang_install)) {

				$qtxt="DELETE FROM core_lang_language WHERE lang_code='".$language."'";
				$q=$db_sorg->query($qtxt);

			}
		}

		// Create the admin user
		registerAdminUser($db_sorg);
		// Store settings
		storeSettings($db_sorg);
	}
	else
		$text.="<br /><br />"._DB_IMPORT_FAILED;

	$text.="<br />"._NEXT_IMPORT_LANG;


	$db_sorg->closeConn();

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('next', 'next', _NEXT)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}


function step_7() {

	$goto_next_step=FALSE;

	$text ='<h3 class="title_area">'._TITLE_STEP7.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::openElementSpace();
	
	$pl_list = array_keys($_SESSION["install"]);
	
	$text.= '<script type="text/javascript">';
	$text.= "	var language = new Array('".implode("','", $_SESSION["lang_install"])."');";
	$text.= "	var platform = new Array('".implode("','", $pl_list)."');";
	$text.= "	var next_lang = '"._NEXT."';";
	$text.= "	YAHOO.util.Event.onDOMReady(setup, true);";
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

/*
	$platform_code=end(array_keys($_SESSION["lang_to_load"]));
	$lang=end($_SESSION["lang_to_load"][$platform_code]);

	array_pop($_SESSION["lang_to_load"][$platform_code]);

	if (count($_SESSION["lang_to_load"][$platform_code]) < 1)
		array_pop($_SESSION["lang_to_load"]);

	if (count($_SESSION["lang_to_load"]) < 1)
		$goto_next_step=TRUE;


	require_once($GLOBALS['where_upgrade'].'/lib/lib.lang.php');
	$GLOBALS["db"] = new DoceboSql($_SESSION['dbhost'], $_SESSION['dbuname'], $_SESSION['dbpass'], $_SESSION['dbname'], false);


	$text.=_LANG_INSTALLED." ("._LANGUAGE.": <b>".ucfirst($lang)."</b>, ";
	$text.=_PLATFORM.":<b>".constant("_".strtoupper($platform_code))."</b>)";

	$fn=$GLOBALS['where_upgrade'].'/../xml_language/platform['.$platform_code.']_lang['.$lang.'].xml';

	if (file_exists($fn))
		lang_importXML($fn);

	$GLOBALS["db"]->closeConn();

*/
	$text.=Form::getHidden('step', 'step', 'step8');

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.'<div id="next_button">'
		.'<button type="submit" '
		."\n\t".'class="button" '
		."\n\t".'id="start" '
		."\n\t".'name="start" '
		."\n\t".'onclick="load_lang(); return false;" '
		."\n\t".'value="Start">Start</button>'."\n"
		.'</div>'
		
		//.Form::getButton('next', 'next', _NEXT, 'button', 'disabled="disabled"')

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}

function step_8() {

	$text ='<h3 class="title_area">'._TITLE_STEP8.'</h3>';

	$text.=Form::openForm('installform', 'index.php')
		.Form::getHidden('step', 'step', 'finish')
		.Form::openElementSpace();

//	$db = new DoceboSql($_SESSION['dbhost'], $_SESSION['dbuname'], $_SESSION['dbpass'], $_SESSION['dbname'], false);

	$saved =(isset($_SESSION["config_saved"]) && !$_SESSION["config_saved"] ? FALSE : TRUE);

	if (!$saved) {
		$config =$_SESSION["config"];
		$text.='<label for="config">'._CONFIGURATION.'</label><br />';
		$text.="<b>"._COPY_N_PASTE_CONFIG."</b><br />\n";
		$text.="<textarea rows=\"20\" cols=\"72\" id=\"config\" name=\"config\" readonly=\"readonly\">";
		$text.=htmlspecialchars($config)."</textarea><br />\n";
	}

	$text.=_INSTALLATION_COMPLETE;

	$platform_arr=getPlatformArray();
	$text.="<p>";
	$text.=_TO_ADMIN.":<br />";
	$url=$_SESSION["site_base_url"].$platform_arr["framework"]."/";
	$text.="<a href=\"".$url."\">".$url."</a><br /><br />\n";

	$text.=_TO_WEBSITE.":<br />";
	$url=$_SESSION["site_base_url"];
	$text.="<a href=\"".$url."\">".$url."</a><br /><br />\n";

	$text.=_INSTALLED_APPS.":<br />";

	$installed_apps=$_SESSION["install"];
	unset($installed_apps["framework"]);
	unset($installed_apps["scs"]);

	$text.="</p>";

	$text.="<ul>";
	foreach ($installed_apps as $platform_code=>$do_install) {
		if ($do_install) {
			$url=$_SESSION["site_base_url"].$platform_arr[$platform_code]."/";
			$text.="<li><b>".constant("_".strtoupper($platform_code))."</b><br />\n";
			$text.="<a href=\"".$url."\">".$url."</a></li>\n";
		}
	}
	$text.="</ul>";

	$text.="<p>"._REMOVE_INSTALL_FOLDERS_AND_WRITE_PERM."</p>";

	$text .= Form::closeElementSpace()
		.Form::openButtonSpace()

		.Form::getButton('finish', 'finish', _FINISH)

		.Form::closeButtonSpace()
		.Form::closeForm();

	echo $text;
}

function finish()
{
	$url=str_replace("&amp;", "&", $_SESSION["site_base_url"]);
	header("location: ".$url);
}

function registerAdminUser(& $db) {

	// ----------- Registering admin user ---------------------------------

	$qtxt="SELECT * FROM core_user WHERE userid='/".$_SESSION["admin_user"]."'";
	$q=$db->query($qtxt);

	if (($q) && ($db->numRows($q) > 0)) { // Did the user refreshed the page?

		// You never know..
		$qtxt ="UPDATE core_user SET pass='".md5($_SESSION["admin_pass"])."' ";
		$qtxt.="WHERE userid='/".$_SESSION["admin_user"]."'";
		$q=$db->query($qtxt);

	}
	else { // Let's create the admin user..

		$qtxt="INSERT INTO core_st (idst) VALUES(NULL)";
		$q=$db->query($qtxt);
		$user_idst=$db->lastInsertId();

		$qtxt ="SELECT groupid, idst FROM core_group WHERE groupid='/framework/level/godadmin' ";
		$qtxt.="OR groupid='/oc_0'";
		$q=$db->query($qtxt);

		$godadmin=0;
		$oc_0=0;
		$res=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=$db->fetchArray($q)) {
				$res[$row["groupid"]]=$row["idst"];
			}
			$godadmin=$res["/framework/level/godadmin"];
			$oc_0=$res["/oc_0"];
		}

		$qtxt="INSERT INTO core_group_members (idst, idstMember) VALUES('".$oc_0."', '".$user_idst."')";
		$q=$db->query($qtxt);
		$qtxt="INSERT INTO core_group_members (idst, idstMember) VALUES('".$godadmin."', '".$user_idst."')";
		$q=$db->query($qtxt);

		$qtxt ="INSERT INTO core_user (idst, userid, pass, email) ";
		$qtxt.="VALUES ('".$user_idst."', '/".$_SESSION["admin_user"]."', '".md5($_SESSION["admin_pass"])."', '".$_SESSION["admin_email"]."')";
		$q=$db->query($qtxt);
	}
}

function storeSettings(& $db) {

	// ----------- Saving settings according to user answers -----------------------------
	if ($_SESSION["estimated_users"] == "more150")
		$use_tree=1;
	else
		$use_tree=0;

	if ($_SESSION["many_branches"] == "yes")
		$use_tree=1;

	$qtxt ="UPDATE core_setting SET param_value='".$use_tree."' ";
	$qtxt.="WHERE param_name='use_org_chart'";
	$q=$db->query($qtxt);

	if ($_SESSION["admin_type"] == "one")
		$use_admin=0;
	else if ($_SESSION["admin_type"] == "many")
		$use_admin=1;

	$qtxt ="UPDATE core_setting SET param_value='".$use_admin."' ";
	$qtxt.="WHERE param_name='use_admin'";
	$q=$db->query($qtxt);

	if ($_SESSION["req_accessibility"] == "yes")
		$accessibility="on";
	else if ($_SESSION["req_accessibility"] == "no")
		$accessibility="off";

	$qtxt ="UPDATE core_setting SET param_value='".$accessibility."' ";
	$qtxt.="WHERE param_name='accessibility'";
	$q=$db->query($qtxt);

	$qtxt ="UPDATE core_setting SET param_value='".$_SESSION["reg_type"]."' ";
	$qtxt.="WHERE param_name='register_type'";
	$q=$db->query($qtxt);

	$qtxt ="UPDATE core_setting SET param_value='".$_SESSION["default_sender_email"]."' ";
	$qtxt.="WHERE param_name='mail_sender'";
	$q=$db->query($qtxt);

	$sel_lang=DoceboUpgradeGui::getLanguage();
	$qtxt ="UPDATE core_setting SET param_value='".$sel_lang."' ";
	$qtxt.="WHERE param_name='default_language'";
	$q=$db->query($qtxt);

	$prefix=array();
	$prefix["framework"]="core";
	$prefix["lms"]="learning";
	$prefix["cms"]="cms";
	$prefix["scs"]="conference";
	$prefix["crm"]="crm";
	$prefix["ecom"]="ecom";

	$platform_arr=getPlatformArray();
	unset($platform_arr["scs"]);
	foreach ($platform_arr as $platform_code=>$platform_folder) {

		$url=$_SESSION["site_base_url"].$platform_folder."/";

		$qtxt ="UPDATE ".$prefix[$platform_code]."_setting SET param_value='".$url."' ";
		$qtxt.="WHERE param_name='url'";
		$q=$db->query($qtxt);
	}

	// ----------- Set the default platform -----------------------------

	$qtxt="UPDATE core_platform SET main='false'";
	$q=$db->query($qtxt);

	$qtxt="UPDATE core_platform SET main='true' WHERE platform='".$_SESSION["default_platform"]."'";
	$q=$db->query($qtxt);

	$GLOBALS["db"]=& $db;

	$spec_code="";
	$to_activate=array();
	foreach ($_SESSION["install"] as $platform_code=>$do_install) {
		if ($do_install) {
			$to_activate[]="'".$platform_code."'";
			$post_install=getSpecificCode($platform_code, "postInstall");
		}
	}

	$qtxt="UPDATE core_platform SET is_active='true' WHERE platform IN (".implode(",", $to_activate).")";
	$q=$db->query($qtxt);

	// ----------- Generating config file -----------------------------
	$config="";
	$fn = $GLOBALS['where_upgrade']."/data/config_template.php";

	if (file_exists($fn)) {

		$handle = fopen($fn, "r");
		$config = fread($handle, filesize($fn));
		fclose($handle);

	}

	$config=str_replace("[%-DB_HOST-%]", $_SESSION["dbhost"], $config);
	$config=str_replace("[%-DB_USER-%]", $_SESSION["dbuname"], $config);
	$config=str_replace("[%-DB_PASS-%]", $_SESSION["dbpass"], $config);
	$config=str_replace("[%-DB_NAME-%]", $_SESSION["dbname"], $config);

	if ($_SESSION["upload_method"] == "http") {
		$upload_method="fs";

		$config=str_replace("[%-FTP_HOST-%]", "localhost", $config);
		$config=str_replace("[%-FTP_PORT-%]", "21", $config);
		$config=str_replace("[%-FTP_USER-%]", "", $config);
		$config=str_replace("[%-FTP_PASS-%]", "", $config);
		$config=str_replace("[%-FTP_PATH-%]", "/", $config);
	}
	else if ($_SESSION["upload_method"] == "ftp") {
		$upload_method="ftp";

		$config=str_replace("[%-FTP_HOST-%]", $_SESSION["ftphost"], $config);
		$config=str_replace("[%-FTP_PORT-%]", $_SESSION["ftpport"], $config);
		$config=str_replace("[%-FTP_USER-%]", $_SESSION["ftpuser"], $config);
		$config=str_replace("[%-FTP_PASS-%]", $_SESSION["ftppass"], $config);
		$config=str_replace("[%-FTP_PATH-%]", $_SESSION["ftppath"], $config);
	}

	$config=str_replace("[%-UPLOAD_METHOD-%]", $upload_method, $config);

	$config_fn=$GLOBALS["path_to_root"]."config.php";
	$saved=FALSE;
	if (is_writeable($config_fn)) {

		$handle = fopen($config_fn, 'w');
		if (fwrite($handle, $config)) $saved=TRUE;
		fclose($handle);

		@chmod($config_fn, 0644);
	}

	if (!$saved) {
		$_SESSION["config_saved"] =FALSE;
		$_SESSION["config"] =$config;
	}
}

function getSpecificCode($platform_code, $function_name, $error_res="") {
	$res=$error_res;

	$fn=$GLOBALS['where_upgrade'].'/platform_specific/'.$platform_code.'.php';
	if (file_exists($fn)) {

		require_once($fn);

		$function=$platform_code."_".$function_name;
		if (function_exists($function)) {
			eval("\$res.=".$function."();");
		}
	}
	
	return $res;
}

// XXX: switch
function dispatchStep($step) {

	switch($step) {
		case "step2" : check_step_1();break;
		case "step3" : check_step_2();break;
		case "step4" : check_step_3();break;
		case "step5" : check_step_4();break;
		case "step5_reload" : step_5();break;
		case "step6" : check_step_5();break;
		case "step7" : step_7();break;
		case "step7_reload" : step_7();break;
		case "step8" : step_8();break;
		case "finish" : finish();break;
		default : step_1();break;
	}
}

?>
