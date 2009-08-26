<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
global $CFG;

require_once(ABSPATH."lib/Net/XHR.php");
require_once(ABSPATH."lib/URLParamHelper.php");

$dic = GetParamSafe('dic');
$word = GetParamSafe('word');

$url = "http://vdict.com/fsearch.php?dictionaries=$dic&word=" . rawurlencode($word);
$result = XHR::execCURL($url);

/// trim some text on result
$result = str_replace("<link href='templates/user/style.css' media='screen' rel='stylesheet' type='text/css' />", '', $result);
$result = str_replace("<!-- Cache File -->", '', $result);
//$result = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', '', $result);
//$result = preg_replace('/<.*html.*>/i', '', $result);
//$result = preg_replace('/<head.*head>/is', '', $result);
$result = preg_replace('/<script.*?script>/is', '', $result);
//$result = str_replace("body>", 'div>', $result);

echo $result;

?>