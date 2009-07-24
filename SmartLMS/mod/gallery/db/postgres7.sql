# $Id: postgres7.sql,v 1.1 2006/03/25 00:18:28 michaelpenne Exp $
#
# This file contains a complete database schema for all the 
# tables used by this module, written in Postgres7

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

CREATE TABLE prefix_gallery (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  albumid INT8  NOT NULL default '0',
  permissions text NOT NULL default '',
  timecreated INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0'
);