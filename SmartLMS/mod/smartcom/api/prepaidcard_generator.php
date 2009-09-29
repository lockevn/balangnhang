<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH."lib/db/DBHelper.php");
require_once(ABSPATH."lib/Text.php");

define('AJAX_CALL',true);
require_login();



$facevalue = required_param('facevalue', PARAM_INT);
$coinvalue = required_param('coinvalue', PARAM_INT);
$periodvalue = required_param('periodvalue', PARAM_INT);
$howmuch = required_param('howmuch', PARAM_INT);
$batchcode = required_param('batchcode', PARAM_TEXT);
$batchcode = Text::ToCamelCase($batchcode, true);

if($howmuch < 1)
{
	die('howmuch must be > 0');
}


function SecretCodeExisted($code)
{
	if(empty($code))
	{
		return true;
	}        
	
	$ret = get_record('smartcom_card', 'code', $code);
	if($ret)
	{
		return true;
	}
	else
	{
		return false;
	}
}


$arraySecretCodeUNIQUE = array();
while(count($arraySecretCodeUNIQUE) < $howmuch)
{
	$arraySecretCode = array();
	for($i=0; $i<1.5*$howmuch; $i++)
	{
		$code = '';
		while(SecretCodeExisted($code))
		{
			$code = Text::generateRandomStr(10, '1234567890');
		}
		$arraySecretCode[] = $code;
	}
	$arraySecretCodeUNIQUE = array_unique($arraySecretCode);
}

$arraySecretCodeUNIQUE = array_slice($arraySecretCodeUNIQUE, 0, $howmuch);


$sql = "
insert into mdl_smartcom_card
(serialno,code,facevalue,coinvalue,periodvalue,batchcode) 
values ";


$filename = date('Ymd') . ".$facevalue.$coinvalue.$periodvalue.$batchcode." . time(). '.csv';

$writeCSVFilePath = $CFG->dirroot . '/mod/smartcom/asset/' . $filename;
$fhandler = fopen($writeCSVFilePath, 'w') 
or 
die("GURUCORE: can't open and write to file storage: '$writeCSVFilePath'. Please contact technical administrator to change file write permission on that folder.");

// HEADER
$sData = "serialno,secretcode,facevalue,coinvalue,periodvalue,batchcode\n";
fwrite($fhandler, $sData);

// DATA
foreach (((array)$arraySecretCodeUNIQUE) as $value) {	
	$serialno = time() . Text::generateRandomStr(5, '1234567890');
		
	$sData = "$serialno,$value,$facevalue,$coinvalue,$periodvalue,$batchcode";
	fwrite($fhandler, $sData . "\n");
	
	$sql .= "('$serialno','$value',$facevalue,$coinvalue,$periodvalue,'$batchcode'),";
}
fclose($fhandler);

$sql = trim($sql, ',');
$ret = execute_sql($sql, false);
if($ret)
{
	echo $CFG->wwwroot. "/mod/smartcom/asset/$filename";
}

?>