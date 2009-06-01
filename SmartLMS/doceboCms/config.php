<?php

/*************************************************************************/
/* SPAGHETTILEARNING - E-Learning System                                 */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

//framework position
$GLOBALS['where_framework_relative'] = '../doceboCore';
$GLOBALS['where_framework'] = dirname(__FILE__).'/../doceboCore';

//lms position
$GLOBALS['where_lms_relative'] = '../doceboLms';
$GLOBALS['where_lms'] = dirname(__FILE__).'/../doceboLms';

//cms position
$GLOBALS['where_cms_relative'] = '.';
$GLOBALS['where_cms'] = dirname(__FILE__);

//scs position
$GLOBALS['where_scs_relative'] = '../doceboScs';
$GLOBALS['where_scs'] = dirname(__FILE__).'/../doceboScs';

//kms position
$GLOBALS['where_kms_relative'] = '../doceboKms';
$GLOBALS['where_kms'] = dirname(__FILE__).'/../doceboKms';

//crm position
$GLOBALS['where_crm_relative'] = '../doceboCrm';
$GLOBALS['where_crm'] = dirname(__FILE__).'/../doceboCrm';

//ecom position
$GLOBALS['where_ecom_relative'] = '../doceboEcom';
$GLOBALS['where_ecom'] = dirname(__FILE__).'/../doceboEcom';

//files position
$GLOBALS['where_files_relative'] = '../files';


/* Information needed for database access********************************* */

$GLOBALS["prefix_cms"] = "cms";							//prefix for tables
$GLOBALS['platform'] = 'cms';

$GLOBALS['where_config'] = dirname(__FILE__).'/..';


?>