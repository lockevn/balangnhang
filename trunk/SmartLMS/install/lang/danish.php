<?php

define("_INSTALLER_TITLE", "Docebo 3.6.0.3 - Installation");
define("_INSTALL", "Installation");
define("_JUMP_TO_CONTENT", "Jump to content");

// choose begin

define("_SELECT_LANGUAGE", "Select language");
define("_LANGUAGE", "Language");
define("_LANG_INSTALLED", "Language has been installed");
define("_PLATFORM", "Choose the application you need to install");

define("_ITALIAN", "Italian");
define("_ENGLISH", "English");
define("_FRENCH", "French");
define("_SPANISH", "Spanish");
define("_GERMAN", "German");
define("_PORTUGUESE-BR", "Portuguese-br");
define("_TAMIL", "Tamil");
define("_CROATIAN", "Croatian");
define("_BOSNIAN", "Bosnian");

define("_TITLE_STEP1", "Step 1: Select language");
define("_TITLE_STEP2", "Step 2: License");
define("_TITLE_STEP3", "Step 3: Choose application to install");
define("_TITLE_STEP4", "Step 4: Configuration");
define("_TITLE_STEP5", "Step 5: Installation customization");
define("_TITLE_STEP6", "Step 6: Database importing");
define("_TITLE_STEP7", "Step 7: Languages importing");
define("_TITLE_STEP8", "Step 8: INstallation completed");

define("_IS_PRESENT_DIRECTORIES","You have some directory that are no longer used, we suggest to delete it : ");
define("_LACKING_DIRECTORIES","Some directory are missing, without these directory you can't install a certain part of the application or use correctly the system : ");
define("_CANT_CONNECT_WITH_DB", "Can't connect to DB, please check inserted data");
define("_CANT_CONNECT_WITH_FTP","Can't connect in ftp to the specified server, please check inserted parameters");
define("_CHECKED_DIRECTORIES","Some directory where files are stored does not exist or does not have correct permission");
define("_CHECKED_FILES","Certain files does not have adguate permission");
define("_EMPTY_DIRECTORIES","");
define("_SELECT_WHATINSTALL", "Select what platform you need to install");


define("_WARNING_NOT_INSTALL", "<b>Attention</b>: if you uncheck an application you can't restore it with the automatic procedure.");

define("_FRAMEWORK", "Docebo Core Framework");
define("_LMS", "Docebo Learning Management System");
define("_ECOM", "Docebo E-Commerce");
define("_CMS", "Docebo Content Management System");
define("_KMS", "Docebo Knowledge Management System");
define("_SCS", "Docebo Syncronous Collaborative System");

define("_NEXT", "Next step");
define("_BACK", "Back");
define("_REFRESH", "Refresh");
define("_DOINSTALL", "Install");
define("_FINISH", "End");

define("_DATABASE_INFO", "Database information");
define("_DB_HOST", "Address");
define("_DB_NAME", "Database name");
define("_DB_USERNAME", "Database user");
define("_DB_PASS", "Password");
define("_DB_CONFPASS", "Confirm password");

define("_UPLOAD_METHOD", "Upload file method (suggested FTP, if you are on windows at home use HTTP");
define("_HTTP_UPLOAD", "Classic method (HTTP)");
define("_FTP_UPLOAD", "Upload files using FTP");
define("_NOTAVAILABLE", "Not available");

define("_FTP_INFO", "FTP access data");
define("_IF_FTP_SELECTED", "(If you have selected FTP as Upload method)");
define("_FTP_HOST", "Server address");
define("_FTP_PORT", "Port number (generally is correct)");
define("_FTP_USERNAME", "User name");
define("_FTP_PASS", "Password");
define("_FTP_CONFPASS", "Confirm password");
define("_FTP_PATH", "FTP path (is the root where are stored file, ex. /htdocs/ /mainfile_html/");


define("_SOFTWARE_LICENSE", "Software license for the docebo software");
define("_AGREE_LICENSE", "I accept terms license");
define("_MUST_ACCEPT_LICENSE", "You must accept license before continue");

define("_DOMXML_REQUIRED", "To install the Docebo suite you must have the domxml module installed or a PHP version greater than 5.");

define("_LANG_TO_INSTALL", "Languages to install");
define("_NUMBER_ESTIMATED_USERS", "Numer of registered user that will use the software");
define("_LESS_THAN50", "under 50");
define("_LESS_THAN150", "Between 50 and 150");
define("_MORE_THAN150", "more than 150");
define("_MORE_THAN_ONE_BRANCH", "Your company or association have more than one location?");
define("_ANSWER_YES", "Yes;");
define("_ANSWER_NO", "No");
define("_ADMINISTRATION_TYPE", "Administration type");
define("_ONE_ADMIN", "One administrator");
define("_SUB_ADMINS", "Administrator and sub-administrators");

define("_REQUIRE_ACCESSIBILITY", "Do you need to follow accessibility standard for disabilities ?");
define("_REGISTRATION_TYPE", "Kind of registration");
define("_REG_TYPE_FREE", "Everyone can register");
define("_REG_TYPE_MOD", "Moderator have to approve");
define("_REG_TYPE_ADMIN", "Only administrator can create users");

define("_SIMPLIFIED_INTERFACE", "Option for make interfaces easy");
define("_ADMIN_USER_INFO", "Information regarding the administrator user");
define("_ADMIN_USERNAME", "Username");
define("_ADMIN_PASS", "Password");
define("_ADMIN_CONFPASS", "Confirm password");
define("_ADMIN_EMAIL", "e-mail");
define("_WEBSITE_INFO", "Information of the website");
define("_DEFAULT_PLATFORM", "Main application (Home page)");
define("_SITE_DEFAULT_SENDER", "Default e-mail address");
define("_SITE_BASE_URL", "Base url of the website (don't change)");


define("_INVALID_USERNAME", "Username not valid.");
define("_INVALID_PASSWORD", "Password not valid or password don't mach.");
define("_INVALID_EMAIL", "E-Mail address not valid.");
define("_INVALID_DEFAULTSENDEREMAIL", "Default mail address is not valid.");
define("_INVALID_SITEBASEURL", "Base url not valid, the address must finish with \"/\".");


define("_DB_IMPORT_OK", "Database correctly loaded");
define("_DB_IMPORT_FAILED", "You have experienced some database error, please retry or use manual procedure");
define("_NEXT_IMPORT_LANG", "Now we will import languages, this operation can take ong time (1 minute or more), don't close the browser and click\"next step\" ONLY when page is completely loaded. If you experience problem please ask to your server administrator or hosting provider to set the PHP timeout to an higer value or use the manual procedure");


define("_CONFIGURATION", "Configuration");
define("_INSTALLATION_COMPLETE", "Installation has been completed");
define("_COPY_N_PASTE_CONFIG", "Copy and paste the following text inside the file config.php");


define("_TO_ADMIN", "For administration interface click here");
define("_TO_WEBSITE", "For entering in the main page click on following link");
define("_INSTALLED_APPS", "Application installed");

define("_REMOVE_INSTALL_FOLDERS_AND_WRITE_PERM", "<b>Attention:</b> before proceed please delete the folder install/ from the website and lower the write permission to the config.php file");


// result
define("_FAILED_OPERATION","Operation failed, error code : ");
define("_SUCCESSFULL_OPERATION", "Operation success for : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","Critical error on update, the update has been interrupted");


// specific
define("_LMS_ENABLE_EVENT_UI", "Users can decide what notification to receive");
define("_LMS_ENABLE_GROUPSUB_UI", "Users can choose if subscribe to new groups or not");

// diagnostic
define("_SERVERINFO","Server information");
define("_SERVER_ADDR","Server address : ");
define("_SERVER_PORT","Server port : ");
define("_SERVER_NAME","Server name : ");
define("_SERVER_ADMIN","Server administrator : ");
define("_SERVER_SOFTWARE","Server software : ");
define("_PHPINFO","PHP Information : ");
define("_PHPVERSION","PHP Version : ");
define("_SAFEMODE","Safe mode : ");
define("_REGISTER_GLOBAL","register_global : ");
define("_MAGIC_QUOTES_GPC","magic_quotes_gpc : ");
define("_UPLOAD_MAX_FILESIZE","upload_max_filsize : ");
define("_POST_MAX_SIZE","post_max_size : ");
define("_MAX_EXECUTION_TIME","max_execution_time : ");
define("_ALLOW_URL_INCLUDE","allow_url_include : ");
define("_DANGER","Danger - Set to OFF");
define("_DOMXML","domxml(); : ");
define("_LDAP","Ldap : ");
define("_ON","ON ");
define("_OFF","OFF ");
define("_NEXT_STEP","Next step ");
define("_ONLY_IF_YU_WANT_TO_USE_IT","Consider this warning only if you need to use LDAP ");
define("_NOTSCORM","This server does not support domxml or it is not php5, you can't upgrade docebo, ask to your provider to install domxml extension on this server");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","overload function is not active, this means that you must have a php version 4.3.0 or greater. Linux mandriva is complied without overload, search a package with a name similar to: php4-overload-xxxxx.mdk and install the module, Linux fedora core 4 sometime experience bug with the overload, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">please patch it</a>. If you are on windows machine we suggest to use <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_CRITICAL_ERROR","Critical error ");

?>