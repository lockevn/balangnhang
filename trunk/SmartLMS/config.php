<?php

define(ABSPATH, dirname(__FILE__).'/'); // LockeVN: ABSPATH has value=where this config.php lay

require_once(ABSPATH."Lib/External/Savant3.php");
// init share Template Savant engine here
$tpl = new Savant3();
$tpl->setPath('template', 'template');
class Config
{
	const CDN_FTP_ADDRESS = 'ftp://mediaqqq:luftenuplinhtinhxoe2009@media.TiuTit.com';
	
	const API_URL = 'http://api.TiuTit.com';
	const AVATAR_URL = 'http://media.tiutit.com/dynamic/qblog/image/avatar/';
	const GROUP_AVATAR_URL = 'http://media.tiutit.com/dynamic/qblog/image/groupavatar/';

	const IMG_URL = 'http://media.tiutit.com/dynamic/qblog/image/';
	const WEB_URL = 'http://www.tiutit.com/';

	const AVATARSIZE = 70;
	const AVATARSMALLSIZE = 20;
	const AVATARMEDIUMSIZE = 30;
	const AVATARBIGSIZE = 100;

	const POST_IMG_WIDTH = 120;

	const ITEMPERPAGE = 20;
	
	const CFG_QBLOG_EMAIL = 'TiuTit.com <no-reply@loop.vn>';
}



/************************************************************************/
/* DOCEBO CORE - Framework                                              */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2005                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

// INFO: LockeVN ấn các tham số config vào Global, có thể chỉnh dần sang CONST trong Class

$GLOBALS['dbhost'] 		= 'smartlms.dyndns.org';					//host where the database is
$GLOBALS['dbuname'] 	= 'root';						//database username
$GLOBALS['dbpass'] 		= 'guruunited2008';							//database password for the user
$GLOBALS['dbname'] 		= 'docebo';					//database name


// INFO: LockeVN: các prefix để phân biệt bảng của các module trong docebo
$GLOBALS['prefix_fw'] 	= 'core';					//prefix for tables
$GLOBALS['prefix_lms'] 	= 'learning';				//prefix for tables
$GLOBALS['prefix_cms'] 	= 'cms';					//prefix for tables
$GLOBALS['prefix_scs'] 	= 'conference';				//prefix for tables
$GLOBALS['prefix_ecom'] = 'ecom';					//prefix for tables
$GLOBALS['prefix_crm'] = 'crm';						//prefix for tables



/*file upload information************************************************/
$GLOBALS['uploadType'] = 'fs';

$GLOBALS['ftphost'] 	= 'localhost';					// normally this settings is ok
$GLOBALS['ftpport'] 	= '21';							// same as above
$GLOBALS['ftpuser'] 	= '';
$GLOBALS['ftppass'] 	= '';
$GLOBALS['ftppath'] 	= '/';

$GLOBALS['where_files']  = '/files';

$GLOBALS['db_conn_names'] = 'utf8';
$GLOBALS['db_conn_char_set'] = 'utf8';
$GLOBALS['mail_br'] = "\r\n";

?>