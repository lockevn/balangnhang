<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
session_start();

require_once(ABSPATH."Lib/External/Savant3.php");
require_once(ABSPATH."Lib/HttpNavigation.php");
require_once(ABSPATH."Lib/URLParamHelper.php");

require_once(ABSPATH."Business/Common.php");
require_once(ABSPATH."Business/Security.php");

require_once(ABSPATH."Page/PageBuilder.php");


//********************* Set some ENVIRONMENT VAR for client side render **********************//


$mod = GetParamSafe('mod');
$mod = empty($mod) ? 'dashboard' : $mod;
$tab = GetParamSafe('tab');

$tpl->assign('mod', $mod);
$tpl->assign('tab', $tab);
$tpl->assign('CFG', $configs);
$tpl->assign('URL', $_SERVER['REQUEST_URI']);


// browser compat render
if (isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') !== false))
{
	$tpl->assign("ie6", true);
}
if (isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
{
	$tpl->assign("ieBrowser", true);
}



//********************* SECURITY ACCESS RESITRICT **********************//
$arrayNotAllowGuest = array(
	'home','setting', 'resetpwd',
	'advanced_post',
	'group_invite_owner', 'create_group', 'invite_friend');
if(in_array($mod, $arrayNotAllowGuest))
{
	$authkey = Security::GetCurrentAUauthkey(true);
}
else
{
	$authkey = Security::GetCurrentAUauthkey();
}

if($authkey)
{
	$tpl->assign('authkey', $authkey);
	$pinfo = Security::GetCurrentAUProfileInfo($authkey);
	$tpl->assign('AUpid', $pinfo->id);
	$tpl->assign('AUpu', strtolower($pinfo->u));
}
else
{
	if(in_array($mod, $arrayNotAllowGuest))
	{
		Security::Logout('/login');
	}
	else
	{
		Security::Logout();
	}
}



//********************* CONTEXT PARAM **********************//
$pinfo = Common::GetCurrentProfileInfo();
$tpl->assign('pid', $pinfo['id']);
$tpl->assign('pu', strtolower($pinfo['u']));

$ginfo = Common::GetCurrentGroupInfo();
$tpl->assign('gid', $ginfo['id']);
$tpl->assign('gcode', strtolower($ginfo['code']));


$rendertype = GetParamSafe('rendertype');
if($rendertype === "Pagelet")
{    
	$pagelet = preg_replace('/[\W]+/', '', GetParamSafe('pagelet'));
	$customlayout = preg_replace('/[\W]+/', '', GetParamSafe('customlayout'));    
	$customlayout = in_array($customlayout, array_values(PageBuilder::$PageLayoutMap), true) ? $customlayout : 'raw_empty_for_pagelet';     
	
	$zonecontent = '';
	require_once(ABSPATH."Pagelet/$pagelet.php");
	$zonecontent .= $$pagelet;    
	$tpl->assign('ZONE', $zonecontent);
	
	$tpl->display("LAYOUT.$customlayout.tpl.php");
	die();
}

//********************* PAGELET RENDER **********************//
if(in_array($mod, PageBuilder::$AllowedCustomModule))
{
	require_once(ABSPATH."Page/$mod.php");
}
else
{
	if(array_key_exists($mod, PageBuilder::$PageMap))
	{
		PageBuilder::Render($mod, $tpl);
	}
	else
	{
		HttpNavigation::OutputRedirectToBrowser('/pagenotfound.php');
	}
}


require_once(ABSPATH."Pagelet/header.php");
//require_once(ABSPATH."Pagelet/my_toolbar.php");
$tpl->assign('ZONE_TopBar', $header . $my_toolbar);


require_once(ABSPATH."Pagelet/footer.php");
$tpl->assign('ZONE_Footer', $footer);


if(array_key_exists($mod, PageBuilder::$PageLayoutMap))
{
	// use custom layout define in PageBuilder    
	$customlayout = PageBuilder::$PageLayoutMap[$mod];
	$tpl->display("LAYOUT.$customlayout.tpl.php");
}
else
{
	// use default LAYOUT.index.tpl.php
	$tpl->display('LAYOUT.index.tpl.php');
}

?>