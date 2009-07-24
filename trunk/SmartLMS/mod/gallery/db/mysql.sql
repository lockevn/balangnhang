# $Id: mysql.sql,v 1.1 2006/03/25 00:18:28 michaelpenne Exp $
#
# This file contains a complete database schema for all the 
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

CREATE TABLE prefix_gallery (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  albumid int(10) unsigned NOT NULL default '0',
  permissions text NOT NULL,
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY course (course)
) COMMENT='Main information about each gallery';