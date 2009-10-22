<?php // $Id: users.php,v 1.26.2.13 2009/02/12 02:29:35 jerome Exp $

// This file defines settingpages and externalpages under the "users" category

//$ADMIN->add('smartcom', new admin_category('smartcom', get_string('smart','smartcom')));
//$ADMIN->add('smartcom', new admin_category('accounts', get_string('accounts', 'admin')));
//$ADMIN->add('smartcom', new admin_category('roles', get_string('permissions', 'role')));

if ($hassiteconfig) {

	$ADMIN->add('smartcom', new admin_externalpage('prepaidcard_generator', get_string('prepaidcard_generator', 'smartcom'), $CFG->wwwroot . '/mod/smartcom/index.php?courseid=1&submodule=prepaidcard_generator'));
	$ADMIN->add('smartcom', new admin_externalpage('prepaidcard_usage_report', get_string('prepaidcard_usage_report', 'smartcom'), $CFG->wwwroot . '/mod/smartcom/index.php?courseid=1&submodule=prepaidcard_usage_report'));
	//$ADMIN->add('smartcom', new admin_externalpage('prepaidcard_manager', get_string('prepaidcard_manager', 'smartcom'), $CFG->wwwroot . '/mod/smartcom/index.php?courseid=1&submodule=prepaidcard_manager'));
	$ADMIN->add('smartcom', new admin_externalpage('prepaidcard_adjust', get_string('prepaidcard_adjust', 'smartcom'), $CFG->wwwroot . '/mod/smartcom/index.php?courseid=1&submodule=prepaidcard_adjust'));
	
	
            
            
         
} // end of speedup

?>
