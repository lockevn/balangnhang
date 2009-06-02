<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


/**
* @desc return the parameter value, with mysql_espace. Avoid SQL Injection.
* @param parameter name
* @return string value of the param
*/
function GetParamSafe($paramName)
{
    $str = $_REQUEST[$paramName];
    // now client has to urlencode '+' as %2B before submiting to API.
    //$str = str_replace('+','%2B',$str);
    if(!empty($str) && !get_magic_quotes_gpc())
    {
    	$str = addslashes($str);
    }
    return $str;
}


?>