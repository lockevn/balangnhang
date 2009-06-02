<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/URLParamHelper.php");
require_once(ABSPATH."Lib/Net/XHR.php");

session_start();

ob_start();
$username = GetParamSafe('username');
$password = md5(GetParamSafe('password'));
$type = GetParamSafe('type');


$arr = array();
$str_log_err = "invalid_login";
$str_xml_err = "system_error";

$csecret = 'tiutitwebtiutitweb';
$url = Config::API_URL."/public/qid/getrequesttoken.php?format=xml&u=$username&cs=$csecret&sm=md5&from=tiutitweb&service=web&ak=false";
$cs = XHR::execCURL_ReturnCommandStatus($url);
parse_str($cs->info, $arr);
$h1 = $arr['h1'];
// TODO: check is this h1 ok??

$ssecret = $arr['ssecret'];
$h2 = md5($password.$ssecret);
$url =  Config::API_URL."/public/qid/getauthenticatetoken.php?u=$username&h2=$h2&type=$type";
$cs = XHR::execCURL_ReturnCommandStatus($url);
parse_str($cs->info, $arr);
$authkey = $arr["authkey"];

if(!empty($authkey))
{
	setcookie('authkey', $authkey, time() + 1209600, '/');
	setcookie('username', $username,  time() + 1209600, '/');

	echo 'success#'. $authkey . '#'.$username;
	exit();
}
else
{
    // if fail , return empty
	// echo $arrResult['info'];
}

ob_end_flush();
?>