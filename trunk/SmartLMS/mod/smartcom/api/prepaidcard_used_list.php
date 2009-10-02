<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

define('AJAX_CALL',true);
header("Content-type: text/javascript;charset=utf-8");
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('mod/smartcom:prepaidcardusagereport', $context);


$page = $_GET['page']; // get the requested page 
$limit = $_GET['rows']; // get how many rows we want to have into the grid 
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
$sord = $_GET['sord']; // get the direction 
if(!$sidx) $sidx =1;
if(!$limit) $limit = 1000;
if(!$page) $page = 1;


$serialno = optional_param('serialno', null, PARAM_TEXT);
$facevalue = optional_param('facevalue', null, PARAM_TEXT);
$batchcode = optional_param('batchcode', null, PARAM_TEXT);
$fromdate = optional_param('fromdate', null, PARAM_TEXT);
$todate = optional_param('todate', null, PARAM_TEXT);

$depositforusername = optional_param('depositforusername', null, PARAM_TEXT);
$fromuseddatetime = optional_param('fromuseddatetime', null, PARAM_TEXT);
$touseddatetime = optional_param('touseddatetime', null, PARAM_TEXT);


$sql = "from mdl_smartcom_card_used where true ";

if($serialno)
{
	$arrserialno = explode(',', $serialno);		
	foreach ($arrserialno as &$value) {
		$value = "'$value'";
	}
	unset($value);
	$serialno = implode(',', $arrserialno);
	$sql .= " and serialno in ($serialno)";
}

if($facevalue)
{
	$sql .= " and facevalue = $facevalue";
}
if($batchcode)
{
	$sql .= " and batchcode = '$batchcode'";
}

if($fromdate)
{
	$sql .= " and expiredate >= $fromdate";
}
if($todate)
{
	$sql .= " and expiredate <= $todate";
}


if($depositforusername)
{
	$sql .= " and depositforusername = '$depositforusername'";
}

if($fromuseddatetime)
{
	$sql .= " and useddatetime >= $fromuseddatetime";
}
if($todate)
{
	$sql .= " and useddatetime <= $touseddatetime";
}


$result = mysql_query("SELECT COUNT(*) AS count " . $sql); 
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count']; 
if( $count >0 ) 
{ $total_pages = ceil($count/$limit); } 
else 
{ $total_pages = 0; } 

if ($page > $total_pages) 
$page=$total_pages; 

$start = $limit*$page - $limit; // do not put $limit*($page - 1) 


$responce->page = $page; 
$responce->total = $total_pages; 
$responce->records = $count; 


$sql = 'select id, serialno, code, facevalue, coinvalue, periodvalue, batchcode, publishdatetime, depositforusername, useddatetime ' .  $sql.
" ORDER BY $sidx $sord LIMIT $start , $limit";
$result = mysql_query($sql);
if($result)
{
	$i=0;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{ 
	$responce->rows[$i]['id']=$row['id'];	
	$responce->rows[$i]['cell']= array_values($row); 
	$i++; 
	}
	echo json_encode($responce); 
}
?>