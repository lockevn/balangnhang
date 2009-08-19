<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
global $CFG;

require_once(ABSPATH."lib/Net/XHR.php");
require_once(ABSPATH."lib/URLParamHelper.php");

$dic = GetParamSafe('dic');
$word = GetParamSafe('word');

$url = "http://vdict.com/fsearch.php?word=$word&dictionaries=$dic";
$result = XHR::execCURL($url);

/// trim some text on result
$result = str_replace("<link href='templates/user/style.css' media='screen' rel='stylesheet' type='text/css' />", '', $result);
//$result = preg_replace("/<script.*?script>/i", "", $result);

echo $result;

?>