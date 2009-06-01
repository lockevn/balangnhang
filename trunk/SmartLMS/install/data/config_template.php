<?php

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

$GLOBALS['dbhost'] 		= '[%-DB_HOST-%]';					//host where the database is
$GLOBALS['dbuname'] 	= '[%-DB_USER-%]';						//database username
$GLOBALS['dbpass'] 		= '[%-DB_PASS-%]';							//database password for the user
$GLOBALS['dbname'] 		= '[%-DB_NAME-%]';					//database name

$GLOBALS['prefix_fw'] 	= 'core';					//prefix for tables
$GLOBALS['prefix_lms'] 	= 'learning';				//prefix for tables
$GLOBALS['prefix_cms'] 	= 'cms';					//prefix for tables
$GLOBALS['prefix_scs'] 	= 'conference';				//prefix for tables
$GLOBALS['prefix_ecom'] = 'ecom';					//prefix for tables
$GLOBALS['prefix_crm'] = 'crm';						//prefix for tables

/*file upload information************************************************/

$GLOBALS['uploadType'] = '[%-UPLOAD_METHOD-%]';

$GLOBALS['ftphost'] 	= '[%-FTP_HOST-%]';					// normally this settings is ok
$GLOBALS['ftpport'] 	= '[%-FTP_PORT-%]';							// same as above
$GLOBALS['ftpuser'] 	= '[%-FTP_USER-%]';
$GLOBALS['ftppass'] 	= '[%-FTP_PASS-%]';
$GLOBALS['ftppath'] 	= '[%-FTP_PATH-%]';

$GLOBALS['where_files']  = '/files';

$GLOBALS['db_conn_names'] = 'utf8';
$GLOBALS['db_conn_char_set'] = 'utf8';
$GLOBALS['mail_br'] = "\r\n";

?>
