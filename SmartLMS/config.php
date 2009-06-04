<?php

define(ABSPATH, dirname(__FILE__).'/'); // LockeVN: ABSPATH has value=where this config.php lay

require_once(ABSPATH."Lib/External/Savant3.php");
// init share Template Savant engine here
$tpl = new Savant3();
$tpl->setPath('template', 'template');

class GCONFIG
{	
	const API_URL = 'http://smartlms.dyndns.org/api/';
	const WEB_URL = 'http://smartlms.dyndns.org/';	
	const ITEMPERPAGE = 20;	
	const CFG_QBLOG_EMAIL = 'SmartCom.vn <no-reply@smartcom.vn>';
}

class DB_PREFIX
{
	const CORE = 'core';
	const LMS = 'learning';
	const CMS = 'cms';
	const SCS = 'conference';
	const ECOM = 'ecom';
	const CRM = 'crm';	
}

class PATH
{
	const FILE = '/files';
}

/************************************************************************/
/* DOCEBO CORE - Framework                                              */
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