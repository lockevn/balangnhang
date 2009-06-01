<?php

define("_UPGRADER_TITLE", "Docebo 3.6.0.3 - Update");
define("_JUMP_TO_CONTENT", "Jump to content");
define("_CHOOSE_LANG", "Choose language");
define("_LANG_SELECTION", "Select language");

// choose begin
define("_TITLE_1OF2", "Step 1 of 2 : Select start version");
define("_IS_PRESENT_DIRECTORIES","There are alredy in the structure directory no longer used, we suggest to delete it: ");
define("_LACKING_DIRECTORIES","Some directory are missed, without it you can't use correctly the system : ");
define("_CANT_CONNECT_WITH_DB", "DB connection failed, check correct parameters on config.php");
define("_CANT_CONNECT_WITH_FTP","FTP connection failed, check correct parameters on config.php");
define("_CHECKED_DIRECTORIES","Some directory for file storage does not exist or does not have right writing permission");
define("_EMPTY_DIRECTORIES","Some directory for file storage are empty, are you sure you don't have any older files to import?");
define("_START_VERSION","Starting version");
define("_END_VERSION","Final version");
define("_DOUPGRADE", "Proceed with upgrade");

// result 
define("_TITLE_2OF2","Step 2 of 2 : System update");
define("_UPGRADING_VERSION","Update version : ");
define("_FAILED_OPERATION","Operation failed, error code : ");
define("_SUCCESSFULL_OPERATION", "Operation successful for : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Critical error on update, update has been stopped");
define("_TITLE_STEP3", "Language update");
define("_LANG_INSTALLED", "Updating language");
define("_LANGUAGE", "");
define("_PLATFORM", " for platform");
define("_LANGUAGE_NOT_FOUND", "Language file not found");
define("_NEXT", "Next");
define("_NEXTSTEP", "Next step");
define("_ENDSTEP", "End");
define("_END_PHRASE", "Update has been completed successful");
define("_CRITICAL_ERROR","Critical error ");
define("_NOTSCORM","This server does not support domxml or it is not php5, you can't use docebo, ask to your provider to install domxml extension on this server");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","overload function is not active, this means that you must have a php version 4.3.0 or greater. Linux mandriva is complied without overload, search a package with a name similar to: php4-overload-xxxxx.mdk and install the module, Linux fedora core 4 sometime experience bug with the overload, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">please patch it</a>. If you are on windows machine we suggest to use <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "Proceed with standard languages update (will overwrite all)");
define("_NEXT_ONLY_ADD", "Preceed with language update, this will add only new words but will not overwrite old one");
define("_CONVERT_TO_UTF", "utf-8 conversion in progress ...");
define("_CONVERT_TO_UTF_COMMENT", "We are updating languages and content to utf-8 method, don't stop this operation");
?>