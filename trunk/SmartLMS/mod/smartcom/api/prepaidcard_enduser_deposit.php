<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH."lib/db/DBHelper.php");

// define('AJAX_CALL',true);

// TEST: 
require_login();
$username = $USER->username;


$numOfFail = 0;
if(isset($_SESSION['prepaidcard_end_user_deposit']))
{
	$numOfFail = $_SESSION['prepaidcard_end_user_deposit'];
	if($numOfFail > 3)
	{
		echo -$numOfFail;
		// TEST: die();
	}
}

$code = required_param('code', PARAM_TEXT);



$mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}
$mysqli->autocommit(false);


$result = $mysqli->query("select * from mdl_smartcom_card where code='$code'");
$card = DBHelper::GetAssocArray($result);
$card = $card[0];
if($card)
{	
	$dbret = $mysqli->query("delete from mdl_smartcom_card where code='$code'");	
	if($dbret)
	{
		$serialno = $card['serialno'];
		$code = $card['code'];
		$facevalue = $card['facevalue'];
		$coinvalue = $card['coinvalue'];
		$periodvalue = $card['periodvalue'];
		$batchcode = $card['batchcode'];
		$publishdatetime = $card['publishdatetime'];

		$sql = "
insert into mdl_smartcom_card_used
(serialno, code, facevalue, coinvalue, periodvalue, batchcode, publishdatetime,
depositforusername)
values ('$serialno', '$code', $facevalue, $coinvalue, $periodvalue, '$batchcode', '$publishdatetime',
'$username')
		";		
		$dbret = $mysqli->query($sql);
	}
	
	if($dbret)
	{        
		$result = $mysqli->query("select * from mdl_smartcom_account where username = '$username'");
		$alreadyHasAccount = DBHelper::GetAssocArray($result);
		$alreadyHasAccount = $alreadyHasAccount[0];
		if($alreadyHasAccount)
		{            
			$coinvalue = $card['coinvalue'];
			$periodvalue = $card['periodvalue'];
			$sql = "update mdl_smartcom_account set coinvalue = coinvalue + $coinvalue, expiredate = DATE_ADD(expiredate, INTERVAL $periodvalue DAY) ";
			$dbret = $mysqli->query($sql);
		}
		else
		{
			$sql = "insert into mdl_smartcom_account (username, coinvalue, expiredate) values('$username', $coinvalue, DATE_ADD(CURDATE(), INTERVAL $periodvalue DAY)) ";
			$dbret = $mysqli->query($sql);			
		}		
	}
	
	
	if($dbret === true)
	{
		$mysqli->commit();
		die('0');
	}
	else
	{
		$mysqli->rollback();
		die('-1');
	}    
}
else
{	
	$numOfFail++;
	$_SESSION['prepaidcard_end_user_deposit'] = $numOfFail;
	die($numOfFail);
}

?>