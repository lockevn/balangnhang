<?php

class DoceboUpgradeGui {


	function getLanguage() {

		if (isset($_POST["sel_lang"])) {
			$GLOBALS["lang"]=$_POST["sel_lang"];
			$_SESSION["default_language"]=$_POST["sel_lang"];
		}
		else if (isset($_SESSION["default_language"]))
			$GLOBALS["lang"]=$_SESSION["default_language"];
		else
			$GLOBALS["lang"]="english";

		return $GLOBALS["lang"];
	}

	function includeLang($language = false) {

		if ($language === FALSE)
			$language=DoceboUpgradeGui::getLanguage();

		require_once($GLOBALS['where_upgrade'].'/lang/'.$language.'.php');
	}

	function getBrowserLangCode($language = false) {

		if ($language === FALSE)
			$language=DoceboUpgradeGui::getLanguage();

		$code_arr=array_flip(DoceboUpgradeGui::getLanguageList());

		return $code_arr[$language];
	}

	function getLanguageList($key="code") {
		// key can be "code" or "language"

		$res=array();
		if ($key == "code") {
			$res["ru"]="Arabic";
			$res["bs"]="bosnian";
			$res["hr"]="croatian";
			$res["da"]="danish";
			$res["nl"]="dutch";
			$res["en"]="english";
			$res["fa"]="Farsi";
			$res["fr"]="french";
			$res["de"]="german";
			$res["it"]="italian";
			$res["jp"]="Japanese";
			$res["pt-br"]="portuguese-br";
			$res["ru"]="russian";
			$res["ch"]="simplified chinese";
			$res["sp"]="spanish";
			$res["ta"]="tamil";
			$res["tr"]="turkish";
		}
		else if ($key == "language") {
			$res["Arabic"]="Arabic";
			$res["bosnian"]="bosnian";
			$res["croatian"]="croatian";
			$res["danish"]="danish";
			$res["dutch"]="dutch";
			$res["english"]="english";
			$res["Farsi"]="Farsi";
			$res["french"]="french";
			$res["german"]="german";
			$res["italian"]="italian";
			$res["Japanese"]="Japanese";
			$res["portuguese-br"]="portuguese-br";
			$res["russian"]="russian";
			$res["simplified chinese"]="simplified chinese";
			$res["spanish"]="spanish";
			$res["tamil"]="tamil";
			$res["turkish"]="turkish";
		}

		return $res;
	}

}

function config_line($param_name, $param_value) {

	return '<div class="no_float"><div class="label_effect">'
		.$param_name.'</div>'
		.$param_value
		.'</div>';
}

function server_info() {

	$php_conf = ini_get_all();

	$intest = '<div>'
			.'<div class="label_effect">';

	$html = '<div class="conf_line_title">'._SERVERINFO.'</div>'
		.config_line(_SERVER_ADDR, $_SERVER['SERVER_ADDR'] )
		.config_line(_SERVER_PORT, $_SERVER['SERVER_PORT'] )
		.config_line(_SERVER_NAME, $_SERVER['SERVER_NAME'] )
		.config_line(_SERVER_ADMIN, $_SERVER['SERVER_ADMIN'] )
		.config_line(_SERVER_SOFTWARE, $_SERVER['SERVER_SOFTWARE'] )
		.'<br />'


		.'<div class="conf_line_title">'._PHPINFO.'</div>'
		.config_line(_PHPVERSION, phpversion())
		.config_line(_SAFEMODE, ( $php_conf['safe_mode']['local_value']
			? _ON
			: _OFF ))
		.config_line(_REGISTER_GLOBAL, ( $php_conf['register_globals']['local_value']
			? _ON
			: _OFF ))
		.config_line(_MAGIC_QUOTES_GPC, ( $php_conf['magic_quotes_gpc']['local_value']
			? _ON
			: _OFF ))
		.config_line(_UPLOAD_MAX_FILESIZE, $php_conf['upload_max_filesize']['local_value'])
		.config_line(_POST_MAX_SIZE, $php_conf['post_max_size']['local_value'])
		.config_line(_MAX_EXECUTION_TIME, $php_conf['max_execution_time']['local_value'].'s' )
		.config_line(_ALLOW_URL_INCLUDE, ( $php_conf['allow_url_include']['local_value']
			? '<span class="font_red")>'._ON.'(<u onClick=javascript:window.open("http://php.net/manual/en/filesystem.configuration.php#ini.allow-url-include")>'._DANGER.'</u>)</span>'
			: _OFF ));

	if(version_compare(phpversion(), "5.0.0", "<")) {
		$html .= config_line(_DOMXML, ( extension_loaded('domxml')
			? _ON
			: '<span class="font_red">'._OFF.' ('._NOTSCORM.')</span>' ));
	}
	$html .= config_line(_LDAP, ( extension_loaded('ldap')
			? _ON
			: '<span class="font_red">'._OFF.' '._ONLY_IF_YU_WANT_TO_USE_IT.'</span>' ));
	$html .= '<div class="no_float"></div><br />';


	return $html;
}


?>