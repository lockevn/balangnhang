/************************************************************************/
/* DOCEBO - Framework							*/
/* ===================================================================  */
/*									*/
/* Copyright (c) 2004 - 2005 - 2006					*/
/* http://www.docebo.com						*/
/*									*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.	*/
/************************************************************************/

Requirements:

Server specs: Linux, Windows, MAcos, Unix, Sun with 
- Apache 1.3.x or higher, IIS6
- PHP 5.2 with function overload(); and domxml(); (doxmxml only for php4) enabled (Linux mandriva is not complied by default with overload function and FC4 experience a bug to fix)
- Mysql 4.1 or higher
- Doesn't matter if safe mode or register global are on or off ;-)
- If you need to test on your windows home pc we suggest easyphp 1.8 or XAMPP

Installation procedure:

- Be sure you have your ftp parameters (host, user, password) and database parameters (user, password dbname) available
- If you are on your home pc with your easyphp 1.8 create manually a database going on http://localhost/mysql/, remeber that user for db connection on easyphp is "root" and password must be left blank
- Upload all the files in your root directory
- Launch http://www.yoursite.com/install/
- Follow installation instructions
- Once you have finished you are ok

Note: The system will load XML file languages, wait and don't click until page is fully loaded!

Upgrade procedure from docebo 2.0.x to  docebo 3.x

Refer to manuals

Upgrade procedure from docebo 3.x to higher version:

- Overwrite all the old files (don't delete the config.php!!!!)
- Launch www.yourwebsite.com/upgrade
- Follow instructions

New languages load (without upgrade procedure)

- Go in the administration area
- Go in configuration
- Go in language import/export
- Import xml files

Note: This will overwrite ALL your language files chages in the language selected!! (example, if you overwrite english you will loose all your english modifications)

More info on installation on manuals or wiki:

http://www.docebo.org

