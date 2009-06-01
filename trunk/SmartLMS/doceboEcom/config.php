<?php

/************************************************************************/
/* DOCEBO ECOM - E-commercs System										*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

//framework position
$GLOBALS['where_framework_relative'] = '../doceboCore';
$GLOBALS['where_framework'] = dirname(__FILE__).'/../doceboCore';

//lms position
$GLOBALS['where_lms_relative'] = '../doceboLms';
$GLOBALS['where_lms'] = dirname(__FILE__).'/../doceboLms';

//cms position
$GLOBALS['where_cms_relative'] = '../doceboCms';
$GLOBALS['where_cms'] = dirname(__FILE__).'/../doceboCms';

//kms position
$GLOBALS['where_kms_relative'] = '../doceboKms';
$GLOBALS['where_kms'] = dirname(__FILE__).'/../doceboKms';

//scs position
$GLOBALS['where_scs_relative'] = '../doceboScs';
$GLOBALS['where_scs'] = dirname(__FILE__).'/../doceboScs';

//ecom position
$GLOBALS['where_ecom_relative'] = '.';
$GLOBALS['where_ecom'] = dirname(__FILE__);

// file save position
$GLOBALS['where_files_relative'] = '../files';

// config with db info position
$GLOBALS['where_config'] = dirname(__FILE__).'/..';

/*Information needed for database access**********************************/

$GLOBALS['platform'] = 'ecom';
$GLOBALS['prefix_ecom'] = 'ecom';

?>
