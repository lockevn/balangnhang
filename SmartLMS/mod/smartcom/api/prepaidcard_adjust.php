<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH."lib/db/DBHelper.php");

define('AJAX_CALL',true);

require_login();

$numOfFail = 0;
if(isset($_SESSION['prepaidcard_end_user_deposit']))
{
	$numOfFail = $_SESSION['prepaidcard_end_user_deposit'];
	if($numOfFail > 3)
	{
		echo -$numOfFail;
		die();
	}
}

$idlist = required_param('idlist', PARAM_TEXT);
$coinvalue = required_param('coinvalue', PARAM_INT);
$periodvalue = required_param('periodvalue', PARAM_INT);
$batchcode = required_param('batchcode', PARAM_TEXT);

$sql = "
update mdl_smartcom_card set 
coinvalue = coinvalue + $coinvalue, 
periodvalue = periodvalue + $periodvalue,
batchcode = LEFT(CONCAT('$batchcode', ';', batchcode) , 200)
where id in ($idlist)
";

$ret = execute_sql($sql, false);
if($ret)
{
	echo 'ok';
}
else
{
	echo 'fail';
}

?>