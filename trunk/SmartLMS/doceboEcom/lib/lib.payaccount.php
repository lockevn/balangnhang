<?php

function &getInstancePayAccount($account_name) {
	
	$re_payaccount ="SELECT class_file, class_name
	FROM ".$GLOBALS['prefix_ecom']."_payaccount
	WHERE account_name = '".$account_name."' ";
	
	list($class_file, $class_name)= mysql_fetch_row(mysql_query($re_payaccount));
	
	require_once($GLOBALS['where_ecom'].'/admin/modules/payaccount/'.$class_file);
	$obj_payaccount = eval("return new $class_name(); ");
	return $obj_payaccount;
}

?>