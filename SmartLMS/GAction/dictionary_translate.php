<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
global $CFG;
echo 'a';

require_once($CFG->dirroot."/GLib/Net/XHR.php");

$dic = $_REQUEST['dic'];
echo $dic;

$word = GetParamSafe('word');

die($dic + $word);


$url = Config::API_URL.'/message/blog/add.php';

$c = $_POST['c'];
// avoid error when contain @ at begin of string field
if($c[0] === '@')
{
    $c = ' '.$c;
}

$imgdata = array(
        'c'=>                  $c,
        'direction'=>          $_POST['direction'],
        'deviceid'=>           1,
        'devicename'=>         'web',
        'authkey'=>            $_COOKIE['authkey'],
        'img'=>                $fileoriginalname,
        'inreplytoid'=>        $_POST['inreplytoid'],
        'inreplytou'=>         $_POST['inreplytou'],
        'inreplytomsgguid'=>   $_POST['inreplytomsgguid']
    );

$commandStatus = XHR::execCURL_PostData_ReturnCommandStatus($url, $imgdata);

?>