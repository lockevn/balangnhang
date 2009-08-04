<?PHP // $Id: version.php,v 1.6 2004/02/19 13:25:24 moodler Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of resume
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2008022101;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2007021520;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
